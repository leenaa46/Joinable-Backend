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

    public function store()
    {
        $request = \request();

        // Add auth user as created_by 
        $request->merge([
            'created_by' => \auth()->user()->id,
            "is_published" => true
        ]);

        $res = $this->service->create($request);
        return $this->success($res, __('success.save_data'));
    }

    public function index()
    {
        $res = $this->service->all();
        return $this->success($res, __('success.get_data'));
    }

    public function switchPostPublishedStatus(Post $post)
    {
        $res = $this->service->switchPublished($post);
        return $this->success($res, __('success.update_data'));
    }

    public function createEvent()
    {
        $request = \request();
        // Add auth user as created_by 
        $request->merge([
            'created_by' => \auth()->user()->id,
        ]);

        $res = $this->service->createEvent($request);
        return $this->success($res, __('success.save_data'));
    }

    public function joinEvent(Post $post)
    {
        $res = $this->service->personalJoinEvent($post);
        return $this->success($res, __('success.save_data'));
    }

    public function cancelToJoinEvent(Post $post)
    {
        $res = $this->service->personalCancelToJoinEvent($post);
        return $this->success($res, __('success.save_data'));
    }
}
