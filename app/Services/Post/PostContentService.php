<?php

namespace App\Services\Post;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Post\Validations\PostContentValidate;
use App\Services\BaseService;
use App\Models\PostContent;

class PostContentService extends BaseService
{
    use PostContentValidate;

    protected $model;
    protected static $COMMON_RELATIONSHIP = [];

    public function __construct(PostContent $postContent)
    {
        $this->model = $postContent;
    }

    /**
     * Create a new PostContent
     * 
     * @param Request $request
     * 
     * @return PostContent
     */
    public function create(Request $request)
    {
        DB::beginTransaction();

        $this->validateSave($request);

        try {
            $postContent = $this->model->newInstance();
            $postContent->post_id = $request->post_id;
            $postContent->title = $request->title;
            $postContent->order = $request->order;
            $postContent->body = $request->body;
            $postContent->save();

            if ($request->image_content)
                $this->addFileToModel($request->image_content, $postContent, $postContent->image_content_collection_name);

            DB::commit();
            return $postContent;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Get By Model
     * 
     * @param PostContent $postContent
     * 
     * @return PostContent
     */
    public function getByModel(PostContent $postContent)
    {
        return $postContent->load(self::$COMMON_RELATIONSHIP);
    }
}
