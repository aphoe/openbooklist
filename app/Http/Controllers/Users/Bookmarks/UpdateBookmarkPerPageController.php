<?php

namespace App\Http\Controllers\Users\Bookmarks;

use App\Http\Controllers\Controller;
use App\Repositories\SettingRepository;
use App\Services\BookmarkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateBookmarkPerPageController extends Controller
{
    public function __invoke(Request $request, SettingRepository $repository): RedirectResponse
    {
        $bookmarkService = new BookmarkService();
        $perPage = $bookmarkService->getValidPerPage($request->input('per_page'));

        $user = $request->user();
        $settingName = BookmarkService::PER_PAGE_SETTING;

        $existing = $user->settings()->where('setting', $settingName)->first();

        if ($existing) {
            $repository->update($existing, $settingName, (string) $perPage);
        } else {
            $repository->create($user, $settingName, (string) $perPage);
        }

        return back();
    }
}
