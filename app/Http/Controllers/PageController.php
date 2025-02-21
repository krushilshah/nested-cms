<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PageRequest;


class PageController extends Controller
{
    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }


    private function apiResponse($data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }


    public function index(): JsonResponse
    {
        $pages = $this->pageService->getAllPages();
        return $this->apiResponse($pages);
    }

    public function store(PageRequest $request): JsonResponse
    {
        $data = $request->only(['parent_id', 'slug', 'title', 'content']);
        $page = $this->pageService->createPage($data);
        return $this->apiResponse($page, 'Page created successfully', 201);
    }


    public function show($id): JsonResponse
    {
        $page = $this->pageService->getPageById($id);
        return $this->apiResponse($page);
    }

    public function update(PageRequest $request, $id): JsonResponse
    {
        $data = $request->only(['parent_id', 'slug', 'title', 'content']);
        $page = $this->pageService->updatePage($id, $data);
        return $this->apiResponse($page, 'Page updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->pageService->deletePage($id);
        return $this->apiResponse(null, 'Page deleted successfully');
    }

    public function resolve(Request $request): JsonResponse
    {
        $path = $request->path();
        $slugs = array_filter(explode('/', $path));
        $page = $this->pageService->resolvePageByPath($slugs);
        return $this->apiResponse($page);
    }
}
