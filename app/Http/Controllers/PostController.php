<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Post\PostService;
use App\Models\Post;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    protected $service;

    public function __construct(PostService $postService)
    {
        $this->service = $postService;
    }

    public function getCompanyContent()
    {
        $res = $this->service->getContent();
        return $this->success($res, __('success.get_data'));
    }

    public function saveCompanyContent()
    {
        $res = $this->service->saveContent();
        return $this->success($res, __('success.save_data'));
    }
}
