<?php

namespace App\Services\Personal;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Personal\Validations\PersonalValidate;
use App\Services\BaseService;
use App\Models\Personal;

class PersonalService extends BaseService
{
    use PersonalValidate;

    protected $model;
    protected static $COMMON_RELATIONSHIP = ['user.company', 'activities', 'careers'];

    public function __construct(Personal $personal)
    {
        $this->model = $personal;
    }

    /**
     * Create a new Personal
     * 
     * @param Request $request
     * 
     * @return Personal
     */
    public function create(Request $request)
    {
        DB::beginTransaction();

        $this->validateSave($request);

        try {
            $personal = $this->model->newInstance();
            $personal->user_id = $request->user_id;
            $personal->name = $request->name;
            $personal->gender = $request->gender;
            $personal->gender_description = $request->gender_description;
            $personal->joined_at = $request->joined_at;
            $personal->introduce_message = $request->introduce_message;
            $personal->save();

            if ($request->image_profile)
                $this->addFileToModel($request->image_profile, $personal, $personal->image_profile_collection_name);

            DB::commit();
            return $personal;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Get By Model
     * 
     * @param Personal $personal
     * 
     * @return Personal
     */
    public function getByModel(Personal $personal)
    {
        return $personal->load(self::$COMMON_RELATIONSHIP);
    }
}
