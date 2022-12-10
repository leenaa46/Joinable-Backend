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
    protected static $COMMON_RELATIONSHIP = ['user.company'];

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
            $personal->joinable_code = $this->getNewId();
            $personal->name = $request->name;
            $personal->slogan = $request->slogan;
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
     * Generate New Unique Joinable Id
     * 
     * @return string
     */
    public function getNewId()
    {
        $str = Str::random(7);

        return $this->model->where('joinable_code', $str)->first()
            ? $this->getNewId()
            : $str;
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
     * Get By Joinable Code
     * 
     * @param string $joinableCode
     * 
     * @return Personal
     */
    public function getByCode($joinableCode)
    {
        $personal = $this->model->where('joinable_code', $joinableCode)->firstOrFail();

        return $personal;
    }
}
