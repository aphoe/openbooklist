<?php

namespace Tests\Feature\Feature\Users\Bookmarks;

use Tests\TestCase;

class SetBookmarkImageControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_guest_is_redirected_from_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
