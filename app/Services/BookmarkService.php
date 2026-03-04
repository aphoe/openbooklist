<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Spekulatius\PHPScraper\PHPScraper;
use voku\helper\HtmlDomParser;

class BookmarkService
{
    protected ?PHPScraper $scraper = null;

    protected ?HtmlDomParser $dom = null;

    protected ?string $currentUrl = null;

    /**
     * Fetch the HTML of the page using PHPScraper.
     */
    public function fetchHtml(string $url): self
    {
        $this->currentUrl = $url;
        $this->scraper = new PHPScraper(['timeout' => 15]);
        $this->scraper->go($url);

        return $this;
    }

    /**
     * Get title from the HTML page.
     */
    public function getTitle(): ?string
    {
        if (! $this->scraper) {
            return null;
        }

        // Try og:title first, then page title
        $og = $this->scraper->openGraph();

        return $og['og:title'] ?? $this->scraper->title;
    }

    /**
     * Get description from meta description or og:description tags.
     */
    public function getDescription(): ?string
    {
        if (! $this->scraper) {
            return null;
        }

        $og = $this->scraper->openGraph();

        // Try og:description first, then meta description
        return $og['og:description'] ?? $this->scraper->description;
    }

    /**
     * Get image from the page in priority order:
     * 1. og:image
     * 2. twitter:image
     * 3. apple-touch-icon >= 400px
     * 4. site icon
     * 5. First image on the page.
     */
    public function getImage(): ?string
    {
        if (! $this->scraper) {
            return null;
        }

        // 1. og:image
        $og = $this->scraper->openGraph();
        if (! empty($og['og:image'])) {
            return $this->makeAbsoluteUrl($og['og:image']);
        }

        // 2. twitter:image
        $twitterImage = $this->getTwitterImage();
        if ($twitterImage) {
            return $this->makeAbsoluteUrl($twitterImage);
        }

        // 3. apple-touch-icon >= 400px
        $appleTouchIcon = $this->getAppleTouchIcon();
        if ($appleTouchIcon) {
            return $this->makeAbsoluteUrl($appleTouchIcon);
        }

        // 4. meta image / site icon
        $metaImage = $this->scraper->image;
        if ($metaImage) {
            return $this->makeAbsoluteUrl($metaImage);
        }

        // 5. First image on the page
        $images = $this->scraper->images;
        if (! empty($images)) {
            return $images[0];
        }

        return null;
    }

    /**
     * Download and resize an image to a maximum of 512x512px.
     */
    public function downloadAndResizeImage(string $imageUrl): ?string
    {
        try {
            $response = Http::timeout(15)->get($imageUrl);

            if (! $response->successful()) {
                return null;
            }

            $imageData = $response->body();
            $extension = $this->guessExtension($response->header('Content-Type'), $imageUrl);
            $filename = 'bookmarks/'.Str::uuid().'.'.$extension;

            $manager = new ImageManager(new Driver);
            $image = $manager->read($imageData);
            $image->scaleDown(512, 512);

            $encodedImage = $image->encodeByExtension($extension);

            Storage::disk('public')->put($filename, (string) $encodedImage);

            return $filename;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * Fetch all metadata for a URL.
     *
     * @return array{title: ?string, description: ?string, image: ?string}
     */
    public function fetchMetadata(string $url): array
    {
        $this->fetchHtml($url);

        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'image' => $this->getImage(),
        ];
    }

    /**
     * Get twitter:image meta tag content.
     */
    protected function getTwitterImage(): ?string
    {
        if (! $this->scraper) {
            return null;
        }

        try {
            $html = $this->scraper->rawContent ?? '';
            if (empty($html)) {
                return null;
            }

            $dom = HtmlDomParser::str_get_html($html);

            // Try twitter:image meta tag
            $element = $dom->findOne('meta[name="twitter:image"]');
            if ($element && $element->getAttribute('content')) {
                return $element->getAttribute('content');
            }

            // Try twitter:image:src
            $element = $dom->findOne('meta[name="twitter:image:src"]');
            if ($element && $element->getAttribute('content')) {
                return $element->getAttribute('content');
            }

            // Try property attribute
            $element = $dom->findOne('meta[property="twitter:image"]');
            if ($element && $element->getAttribute('content')) {
                return $element->getAttribute('content');
            }
        } catch (\Throwable $e) {
            // Silently fail
        }

        return null;
    }

    /**
     * Get apple-touch-icon with size >= 400px.
     */
    protected function getAppleTouchIcon(): ?string
    {
        if (! $this->scraper) {
            return null;
        }

        try {
            $html = $this->scraper->rawContent ?? '';
            if (empty($html)) {
                return null;
            }

            $dom = HtmlDomParser::str_get_html($html);
            $icons = $dom->find('link[rel="apple-touch-icon"], link[rel="apple-touch-icon-precomposed"]');

            foreach ($icons as $icon) {
                $sizes = $icon->getAttribute('sizes');
                if ($sizes) {
                    $parts = explode('x', strtolower($sizes));
                    if (count($parts) === 2 && (int) $parts[0] >= 400 && (int) $parts[1] >= 400) {
                        return $icon->getAttribute('href');
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently fail
        }

        return null;
    }

    /**
     * Makes a potentially relative URL absolute.
     */
    protected function makeAbsoluteUrl(string $url): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        if (! $this->currentUrl) {
            return $url;
        }

        $parsed = parse_url($this->currentUrl);
        $base = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '');

        if (str_starts_with($url, '//')) {
            return ($parsed['scheme'] ?? 'https').':'.$url;
        }

        if (str_starts_with($url, '/')) {
            return $base.$url;
        }

        return $base.'/'.$url;
    }

    /**
     * Guess file extension from content type or URL.
     */
    protected function guessExtension(?string $contentType, string $url): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/x-icon' => 'ico',
        ];

        if ($contentType && isset($map[$contentType])) {
            return $map[$contentType];
        }

        $path = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'])) {
            return $ext === 'jpeg' ? 'jpg' : $ext;
        }

        return 'jpg';
    }
}
