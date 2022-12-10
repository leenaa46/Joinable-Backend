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
     * Get All Personal In Company
     * 
     * @return Collection || PaginateCollection
     */
    public function all()
    {
        $personals = $this->model->query()->activeCompany();

        return $this->formatQuery($personals, ['name'], ['gender']);
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

    /**
     * Give Personal Variables
     * 
     * @param Request $request
     * @param Personal $personal
     * @param Boolean $withRelation = true
     * 
     * @return Personal
     */
    public function givePersonalVariables(Request $request, Personal $personal, $withRelation = true)
    {
        DB::beginTransaction();
        $this->validateVariable($request);

        try {
            switch ($request->action) {
                case "add":
                    $personal->variables()->syncWithoutDetaching($request->variables);
                    break;
                case "remove":
                    $personal->variables()->detach($request->variables);
                    break;
            }

            DB::commit();

            return $withRelation
                ? $this->getByModel($personal)
                : $personal;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update a new Personal
     * 
     * @param Request $request
     * @param Personal $personal
     * 
     * @return Personal
     */
    public function update(Request $request, Personal $personal)
    {
        DB::beginTransaction();

        $this->validateUpdate($request);

        try {
            $personal->name = $request->name ?: $personal->name;
            $personal->gender = $request->gender;
            $personal->gender_description = $request->gender_description;
            $personal->joined_at = $request->joined_at;
            $personal->introduce_message = $request->introduce_message;
            $personal->save();

            if ($request->image_profile) {
                $personal->clearMediaCollection($personal->image_profile_collection_name);
                $this->addFileToModel($request->image_profile, $personal, $personal->image_profile_collection_name);
            }

            DB::commit();
            return $personal;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
