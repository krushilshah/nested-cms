<?php

namespace App\Services;

use App\Http\Resources\PageCollection;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageService
{
    
    public function getAllPages()
    {
        $pages = Page::whereNull('parent_id')->with('descendants')->get();
        return new PageCollection($pages);
    }

    
    public function createPage(array $data)
    {
        $page = Page::create($data);
        return new PageResource($page->load('children'));
    }


    public function getPageById($id)
    {
        $page = Page::with('descendants')->findOrFail($id);
        return new PageResource($page);
    }


    public function updatePage($id, array $data)
    {
        $page = Page::findOrFail($id);
        $page->update($data);
        return new PageResource($page->load('children'));
    }


    public function deletePage($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();
        return true;
    }

    
    public function resolvePageByPath(array $slugs)
    {
        $page = null;
        $currentParentId = null;

        foreach ($slugs as $slug) {
            $page = Page::where('slug', $slug)
                ->where('parent_id', $currentParentId)
                ->firstOrFail();
            $currentParentId = $page->id;
        }

        return new PageResource($page->load('children'));
    }
}