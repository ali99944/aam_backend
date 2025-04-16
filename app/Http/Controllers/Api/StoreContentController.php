<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StorePageResource;
use App\Http\Resources\StorePageSectionResource;
use App\Models\StorePage;
use App\Models\StorePageSection;
use Illuminate\Http\Request;

class StoreContentController extends Controller
{
    /**
     * Retrieve a specific store page and all its sections by the page key.
     *
     * Example URL: /api/pages/{page_key}
     *
     * @param string $pageKey The unique key of the store page.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPageByKey(string $pageKey)
    {
        $storePage = StorePage::where('key', $pageKey)
                              ->with('sections') // Eager load sections
                              ->first();

        if (!$storePage) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        return response()->json(new StorePageResource($storePage));
    }

    /**
     * Retrieve a specific section by its key, regardless of the page it belongs to.
     *
     * Example URL: /api/sections/{section_key}
     *
     * @param string $sectionKey The unique key of the store page section.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSectionByKey(string $sectionKey)
    {
        $section = StorePageSection::where('key', $sectionKey)->first();

        if (!$section) {
            return response()->json(['message' => 'Section not found.'], 404);
        }

        return response()->json(new StorePageSectionResource($section));
    }

    /**
     * Retrieve a specific section by *both* page key and section key.
     * This is often more useful and RESTful.
     *
     * Example URL: /api/pages/{page_key}/sections/{section_key}
     *
     * @param string $pageKey
     * @param string $sectionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPageSectionByKey(string $pageKey, string $sectionKey)
    {
        // Find the page first
        $storePage = StorePage::where('key', $pageKey)->first();

        if (!$storePage) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        // Find the section within that page
        $section = $storePage->sections()->where('key', $sectionKey)->first();

        if (!$section) {
            return response()->json(['message' => 'Section not found on this page.'], 404);
        }

        return response()->json(new StorePageSectionResource($section));
    }

     /**
     * Retrieve all sections belonging to a specific store page by the page key.
     * (Alternative to getPageByKey if you only need the sections array)
     *
     * Example URL: /api/pages/{page_key}/sections
     *
     * @param string $pageKey The unique key of the store page.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPageSections(string $pageKey)
    {
        $storePage = StorePage::where('key', $pageKey)->first();

        if (!$storePage) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        $sections = $storePage->sections()->orderBy('name')->get(); // Get sections ordered

        return response()->json(StorePageSectionResource::collection($sections));
    }
}