<?php

namespace App\Services\Post;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Post\Validations\PostValidate;
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
        $posts = $this->model->query()->activeCompany();

        switch (request()->type) {
            case 'company_content':
                $posts->with(['image_title', 'post_contents']);
                break;
        }

        return $this->formatQuery($posts, ['title'], 'faq');
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

    /**
     * Give Post Variables
     * 
     * @param Request $request
     * @param Post $post
     * @param Boolean $withRelation = true
     * 
     * @return Post
     */
    public function givePostVariables(Request $request, Post $post, $withRelation = true)
    {
        DB::beginTransaction();
        $this->validateVariable($request);

        try {
            switch ($request->action) {
                case "add":
                    $post->variables()->syncWithoutDetaching($request->variables);
                    break;
                case "remove":
                    $post->variables()->detach($request->variables);
                    break;
            }

            DB::commit();

            return $withRelation
                ? $this->getByModel($post)
                : $post;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update a new Post
     * 
     * @param Request $request
     * @param Post $post
     * 
     * @return Post
     */
    public function update(Request $request, Post $post)
    {
        DB::beginTransaction();

        $this->validateUpdate($request);

        try {
            $post->name = $request->name ?: $post->name;
            $post->gender = $request->gender;
            $post->gender_description = $request->gender_description;
            $post->joined_at = $request->joined_at;
            $post->introduce_message = $request->introduce_message;
            $post->save();

            if ($request->image_profile) {
                $post->clearMediaCollection($post->image_profile_collection_name);
                $this->addFileToModel($request->image_profile, $post, $post->image_profile_collection_name);
            }

            DB::commit();
            return $post;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
