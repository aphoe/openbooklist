<?php

namespace App\Http\Controllers\Users\Categories;

use App\Http\Controllers\Controller;
use App\Repositories\SettingRepository;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateCategoryPerPageController extends Controller
{
    public function __invoke(Request $request, SettingRepository $repository): RedirectResponse
    {
        $categoryService = new CategoryService();
        $perPage = $categoryService->getValidPerPage($request->input('per_page'));

        $user = $request->user();
        $settingName = CategoryService::PER_PAGE_SETTING;

        $existing = $user->settings()->where('setting', $settingName)->first();

        if ($existing) {
            $repository->update($existing, $settingName, (string) $perPage);
        } else {
            $repository->create($user, $settingName, (string) $perPage);
        }

        return back();
    }
}
