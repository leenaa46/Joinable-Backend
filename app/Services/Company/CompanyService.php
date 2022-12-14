<?php

namespace App\Services\Company;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Company\Validations\CompanyValidate;
use App\Services\BaseService;
use App\Models\Media;
use App\Models\Company;

class CompanyService extends BaseService
{
    use CompanyValidate;

    protected $model;
    protected static $COMMON_RELATIONSHIP = ['image_galleries'];

    public function __construct(Company $company)
    {
        $this->model = $company;
    }

    /**
     * Create a new company
     * 
     * @param Request $request
     * 
     * @return Company
     */
    public function create(Request $request)
    {
        DB::beginTransaction();

        $this->validateSave($request);

        try {
            $company = $this->model->newInstance();
            $company->joinable_code = $this->getNewId();
            $company->name = $request->name;
            $company->slogan = $request->slogan;
            $company->save();

            if ($request->image_profile)
                $this->addFileToModel($request->image_profile, $company, $company->image_profile_collection_name);

            DB::commit();
            return $company;
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
     * @param Company $company
     * 
     * @return Company
     */
    public function getByModel(Company $company)
    {
        return $company->load(self::$COMMON_RELATIONSHIP);
    }

    /**
     * Get By Joinable Code
     * 
     * @param string $joinableCode
     * 
     * @return Company
     */
    public function getByCode($joinableCode)
    {
        $company = $this->model->where('joinable_code', $joinableCode)->firstOrFail();

        return $company;
    }

    /**
     * Update a new Company
     * 
     * @param Request $request
     * @param Company $company
     * @param Boolean $withRelation = true
     * 
     * @return Company
     */
    public function update(Request $request, Company $company, $withRelation = true)
    {
        DB::beginTransaction();

        $this->validateUpdate($request, $company);

        try {
            $company->name = $request->name ?: $company->name;
            $company->slogan = $request->slogan;
            $company->save();

            if ($request->image_profile) {
                $company->clearMediaCollection($company->image_profile_collection_name);
                $this->addFileToModel($request->image_profile, $company, $company->image_profile_collection_name);
            }

            if ($request->image_galleries) {
                foreach ($request->image_galleries as $image_gallery) {
                    $this->addFileToModel($image_gallery, $company, $company->image_gallery_collection_name);
                }
            }

            DB::commit();
            return $withRelation
                ? $this->getByModel($company)
                : $company;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
