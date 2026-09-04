<?php

namespace App\Services;

use Alaouy\Youtube\Youtube as YoutubeClient;
use App\Managers\OpenRouterManager;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Spatie\LaravelScreenshot\Facades\Screenshot;
use Spekulatius\PHPScraper\PHPScraper;
use voku\helper\HtmlDomParser;

class BookmarkService
{
    public const string PER_PAGE_SETTING = 'bookmark_per_page';

    protected ?PHPScraper $scraper = null;

    protected ?HtmlDomParser $dom = null;

    protected ?string $currentUrl = null;

    protected OpenRouterManager $openRouterManager;

    public function __construct()
    {
        $this->openRouterManager = new OpenRouterManager;
    }

    /**
     * Get preset pagination options.
     */
    public function getPaginationPresets(): array
    {
        return [16, 32, 64, 96, 128, 256, 512];
    }

    /**
     * Get valid per-page value, or default to 32.
     */
    public function getValidPerPage(?int $requested): int
    {
        $presets = $this->getPaginationPresets();

        if ($requested && in_array($requested, $presets)) {
            return $requested;
        }

        return 32;
    }

    /**
     * Check if a URL already exists for a user.
     */
    public function urlExistsForUser(User $user, string $url): bool
    {
        return Bookmark::where('user_id', $user->id)
            ->where('url', $url)
            ->exists();
    }

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
     * Get description from AI generation (if enabled), or meta/og:description tags.
     */
    public function getDescription(): ?string
    {
        if (! $this->scraper) {
            return null;
        }

        // Use AI-generated description as primary option when enabled
        $user = Auth::user();

        if ($user && $user->use_ai_description && $user->ai_model) {
            $bodyHtml = $this->getBodyHtml();

            if ($bodyHtml) {
                $aiDescription = $this->openRouterManager->generateDescription($bodyHtml, $user->ai_model);

                if ($aiDescription) {
                    return $aiDescription;
                }
            }
        }

        // Fall back to og:description or meta description
        $og = $this->scraper->openGraph();

        return $og['og:description'] ?? $this->scraper->description;
    }

    /**
     * Get the raw HTML content of the page body.
     */
    public function getBodyHtml(): ?string
    {
        if (! $this->scraper) {
            return null;
        }

        try {
            return $this->scraper->client()->getResponse()->getContent();
        } catch (\Throwable $e) {
            return null;
        }
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
     * Capture a screenshot of a webpage as 512x269 JPEG.
     */
    public function takeWebsiteScreenshot(string $url): ?string
    {
        try {
            $filename = 'bookmarks/'.Str::uuid().'.jpg';

            Storage::disk('public')->makeDirectory('bookmarks');

            Screenshot::url($url)
                ->size(512, 269)
                ->quality(80)
                ->save(Storage::disk('public')->path($filename));

            return $filename;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * Delete a bookmark image from the public storage disk when applicable.
     */
    public function deleteStoredImage(?string $imagePath): void
    {
        if (! is_string($imagePath) || $imagePath === '') {
            return;
        }

        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return;
        }

        $normalizedPath = str_replace('\\', '/', $imagePath);
        $normalizedPath = ltrim($normalizedPath, '/');

        if (Str::startsWith($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if (! Str::startsWith($normalizedPath, 'bookmarks/') || str_contains($normalizedPath, '..')) {
            return;
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }
    }

    /**
     * Fetch all metadata for a URL.
     *
     * @return array{title: ?string, description: ?string, image: ?string}
     */
    public function fetchMetadata(string $url, ?User $user = null): array
    {
        $language = $user?->language ?: 'en';

        if ($this->isYoutubeUrl($url) && config('project.youtube_api_key') !== null) {
            try {
                return $this->fetchYoutubeMetadata($url, $language);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->fetchHtml($url);

        return [
            'title' => $this->cleanTitle($this->getTitle(), $url),
            'description' => $this->getDescription(),
            'image' => $this->getImage(),
        ];
    }

    /**
     * Apply host-specific clean-ups to a page title before it is persisted.
     *
     * GitHub renders repository titles as "GitHub - owner/repo: description";
     * the "GitHub - " prefix is noise, so strip it for github.com URLs.
     * Shared by metadata fetching and the store/update endpoints.
     */
    public function cleanTitle(?string $title, string $url): ?string
    {
        if (! is_string($title)) {
            return $title;
        }

        $title = trim($title);

        $host = parse_url($url, PHP_URL_HOST);
        $host = is_string($host) ? preg_replace('/^www\./', '', strtolower($host)) : '';

        if ($host === 'github.com' && str_starts_with($title, 'GitHub - ')) {
            $title = ltrim(substr($title, strlen('GitHub - ')));
        }

        return $title;
    }

    /**
     * Fetch metadata from YouTube when a valid API key is configured.
     *
     * @return array{title: ?string, description: ?string, image: ?string}
     */
    protected function fetchYoutubeMetadata(string $url, string $language): array
    {
        try {
            $videoId = YoutubeClient::parseVidFromURL($url);

            $videoInfo = $this->createYoutubeClient()->getLocalizedVideoInfo($videoId, $language, ['snippet']);

            if (! $videoInfo || ! isset($videoInfo->snippet)) {
                return [
                    'title' => null,
                    'description' => null,
                    'image' => null,
                ];
            }

            return [
                'title' => $videoInfo->snippet->title ?? null,
                'description' => $videoInfo->snippet->description ?? null,
                'image' => $this->getLargestYoutubeThumbnailUrl($videoInfo->snippet->thumbnails ?? null),
            ];
        } catch (\Throwable $e) {
            report($e);

            return [
                'title' => null,
                'description' => null,
                'image' => null,
            ];
        }
    }

    /**
     * Determine whether a URL belongs to YouTube.
     */
    protected function isYoutubeUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return false;
        }

        return str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be');
    }

    /**
     * Return the largest thumbnail URL available from the YouTube snippet.
     */
    protected function getLargestYoutubeThumbnailUrl(mixed $thumbnails): ?string
    {
        if (! is_object($thumbnails)) {
            return null;
        }

        /** @var array<string, object> $thumbnailOptions */
        $thumbnailOptions = get_object_vars($thumbnails);

        $largestUrl = null;
        $largestArea = -1;

        foreach ($thumbnailOptions as $thumbnail) {
            if (! is_object($thumbnail) || ! isset($thumbnail->url) || ! is_string($thumbnail->url)) {
                continue;
            }

            $width = isset($thumbnail->width) ? (int) $thumbnail->width : 0;
            $height = isset($thumbnail->height) ? (int) $thumbnail->height : 0;
            $area = $width * $height;

            if ($area > $largestArea) {
                $largestArea = $area;
                $largestUrl = $thumbnail->url;
            }
        }

        return $largestUrl;
    }

    /**
     * Build the YouTube client from project configuration.
     */
    protected function createYoutubeClient(): YoutubeClient
    {
        return new YoutubeClient((string) config('project.youtube_api_key'));
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
            $html = $this->scraper->client()->getResponse()->getContent();
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
            $html = $this->scraper->client()->getResponse()->getContent();
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
