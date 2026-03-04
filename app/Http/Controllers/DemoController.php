<?php

namespace App\Http\Controllers;

use App\Services\BookmarkService;

class DemoController extends Controller
{
    public function playground()
    {
        $url = 'https://spatie.be/docs/crawler/v9/basic-usage/handling-crawl-responses';
        $service = new BookmarkService;
        $result = $service->fetchHtml($url)->getImage();
        var_dump($result);
    }
}
