<?php

namespace App\Services\Post;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Post\Validations\PostValidate;
use App\Services\Post\PostContentService;
use App\Services\BaseService;
use App\Models\Post;

class PostService extends BaseService
{
    use PostValidate;

    protected $model;
    protected static $COMMON_RELATIONSHIP = [];

    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    /**
     * Get All Post In Company
     * 
     * @return Collection || PaginateCollection
     */
    public function all()
    {
        $posts = $this->model->query()->activeCompany();

        switch (request()->type) {
            case 'faq':
                $posts->with('answer');
                break;
            case 'company_content':
                $posts->with(['image_title', 'post_contents']);
                break;
        }

        return $this->formatQuery($posts, ['title'], 'faq');
    }

    /**
     * Get Post Content In Company
     * 
     * @return Collection || PaginateCollection
     */
    public function getContent()
    {
        $post = $this->model->query()->activeCompany()->where('type', 'company_content')->first();
        $postContents = $post->post_contents();

        return $this->formatQuery($postContents, ['title']);
    }

    /**
     * Save Post Content In Company
     * 
     * @return Collection || PaginateCollection
     */
    public function saveContent()
    {
        $post = $this->model->query()->activeCompany()->where('type', 'company_content')->first();

        $postContentService = \resolve(PostContentService::class);

        $request = \request();
        $request->merge([
            'post_id' => $post->id,
            "order" => $post->post_contents()->count() + 1
        ]);
        return $postContentService->create($request);
    }

    /**
     * Create a new Post
     * 
     * @param Request $request
     * 
     * @return Post
     */
    public function create(Request $request)
    {
        DB::beginTransaction();

        $this->validateSave($request);

        try {
            $post = $this->model->newInstance();
            $post->created_by = $request->created_by;
            $post->title = $request->title;
            $post->body = $request->body;
            $post->type = $request->type;
            $post->schedule = $request->schedule;
            $post->is_published = $request->is_published ?? false;
            $post->save();

            if ($request->image_title)
                $this->addFileToModel($request->image_title, $post, $post->image_title_collection_name);

            DB::commit();
            return $post;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Get By Model
     * 
     * @param Post $post
     * 
     * @return Post
     */
    public function getByModel(Post $post)
    {
        return $post->load(self::$COMMON_RELATIONSHIP);
    }
}
