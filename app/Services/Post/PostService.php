<?php

namespace App\Services\Post;

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
    protected static $COMMON_RELATIONSHIP = ['activities', 'personals'];

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
        $posts = $this->model->query()
            ->activeCompany()
            ->with(self::$COMMON_RELATIONSHIP)
            ->withCount('personals');

        // Hide post with post_content type from all users
        $posts->where('type', '!=', 'post_content');

        // Hide unpublished posts from app users.
        if (!\auth()->user()->hasRole(['admin'])) $posts->where('is_published', \true);

        return $this->formatQuery($posts, ['title'], ['type', 'is_published']);
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
    public function create(Request $request, $withRelation = \true)
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

            return $withRelation
                ? $this->getByModel($post)
                : $post;
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
        return $post->load(self::$COMMON_RELATIONSHIP)
            ->loadCount('personals');
    }

    /**
     * Switch Post published status
     * 
     * @param Post $post
     * 
     * @return Post
     */
    public function switchPublished(Post $post)
    {
        $post->is_published = !$post->is_published;
        $post->save();

        return $post;
    }

    /**
     * Create new Event
     * 
     * @param Request $request
     * 
     * @return Function create()
     */
    public function createEvent(Request $request)
    {
        $request->merge([
            "type" => "event",
            "is_published" => true
        ]);

        DB::beginTransaction();

        $this->validateSaveEvent($request);

        try {
            $post = $this->create($request, \false);
            // In case the event has associated activities
            $post->activities()->attach($request->activities);

            DB::commit();
            return $this->getByModel($post);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    /**
     * Personal Join Event
     * 
     * @param Post $post
     * 
     * @return Post
     */
    public function personalJoinEvent(Post $post)
    {
        return $post->personals()->syncWithoutDetaching(\auth()->user()->personal->id);
    }

    /**
     * Personal Cancel Event
     * 
     * @param Post $post
     * 
     * @return Post
     */
    public function personalCancelToJoinEvent(Post $post)
    {
        return $post->personals()->detach(\auth()->user()->personal->id);
    }
}
