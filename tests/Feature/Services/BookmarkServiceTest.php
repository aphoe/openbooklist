<?php

namespace Tests\Feature\Services;

use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use Spatie\LaravelScreenshot\Facades\Screenshot;
use Tests\TestCase;

class BookmarkServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_downloads_and_resizes_image(): void
    {
        Storage::fake('public');

        // Create a fake 600x600 image to trigger resize
        $img = imagecreatetruecolor(600, 600);
        ob_start();
        imagepng($img);
        $imageContent = ob_get_clean();
        imagedestroy($img);

        Http::fake([
            'https://example.com/huge-image.png' => Http::response($imageContent, 200, ['Content-Type' => 'image/png']),
        ]);

        $service = new BookmarkService;
        $filename = $service->downloadAndResizeImage('https://example.com/huge-image.png');

        $this->assertNotNull($filename);
        $this->assertStringStartsWith('bookmarks/', $filename);
        Storage::disk('public')->assertExists($filename);
    }

    public function test_it_returns_null_on_failed_download(): void
    {
        Http::fake([
            'https://example.com/missing.png' => Http::response(null, 404),
        ]);

        $service = new BookmarkService;
        $filename = $service->downloadAndResizeImage('https://example.com/missing.png');

        $this->assertNull($filename);
    }

    public function test_it_takes_website_screenshot_with_expected_settings(): void
    {
        Screenshot::fake();

        $service = new BookmarkService;
        $filename = $service->takeWebsiteScreenshot('https://example.com/article');

        $this->assertNotNull($filename);
        $this->assertStringStartsWith('bookmarks/', $filename);
        $this->assertStringEndsWith('.jpg', $filename);

        Screenshot::assertSaved(function ($builder, $path) {
            return $builder->url === 'https://example.com/article'
                && $builder->width === 512
                && $builder->height === 269
                && $builder->quality === 80
                && str_ends_with($path, '.jpg');
        });
    }

    public function test_make_absolute_url(): void
    {
        $service = new BookmarkService;

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('makeAbsoluteUrl');
        $property = $reflection->getProperty('currentUrl');

        $property->setValue($service, 'https://example.com/path/page.html');

        $this->assertEquals('https://example.com/image.jpg', $method->invoke($service, '/image.jpg'));
        $this->assertEquals('https://example.com/relative/img.png', $method->invoke($service, 'relative/img.png'));
        $this->assertEquals('https://other.com/img.jpg', $method->invoke($service, '//other.com/img.jpg'));
        $this->assertEquals('http://external.com/i.jpg', $method->invoke($service, 'http://external.com/i.jpg'));
    }

    public function test_guess_extension(): void
    {
        $service = new BookmarkService;

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('guessExtension');

        $this->assertEquals('png', $method->invoke($service, 'image/png', 'http://a.com/i'));
        $this->assertEquals('jpg', $method->invoke($service, 'image/jpeg', 'http://a.com/i'));
        $this->assertEquals('gif', $method->invoke($service, null, 'http://a.com/i.gif'));
        $this->assertEquals('webp', $method->invoke($service, null, 'http://a.com/img.WEBp'));
        $this->assertEquals('jpg', $method->invoke($service, null, 'http://a.com/img')); // default fallback
    }
}
