<?php

namespace App\Services\Variable;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Variable\Validations\VariableValidate;
use App\Services\BaseService;
use App\Models\Variable;

class VariableService extends BaseService
{
    use VariableValidate;

    protected $model;
    protected static $COMMON_RELATIONSHIP = ['personals_active'];

    public function __construct(Variable $variable)
    {
        $this->model = $variable;
    }

    /**
     * Get All Variables
     * 
     * @return Collection || PaginateCollection
     */
    public function all()
    {
        $variables = $this->model->activeCompany()
            ->withCount('personals_active');

        return $this->formatQuery($variables, ['name', 'description'], ['type', 'company_id']);
    }

    /**
     * Create a new Variable
     * 
     * @param Request $request
     * 
     * @return Variable
     */
    public function create(Request $request)
    {
        DB::beginTransaction();

        $request->merge(['company_id' => \auth()->user()->company_id]);

        $this->validateSave($request);

        try {
            $variable = $this->model->newInstance();
            $variable->type = $request->type;
            $variable->name = $request->name;
            $variable->description = $request->description;
            $variable->company_id = \auth()->user()->company_id;
            $variable->save();

            if ($request->image_logo)
                $this->addFileToModel($request->image_logo, $variable, $variable->image_logo_collection_name);

            DB::commit();
            return $variable;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Get By Model
     * 
     * @param Variable $variable
     * 
     * @return Variable
     */
    public function getByModel(Variable $variable)
    {
        return $variable
            ->loadCount('personals_active')
            ->load(self::$COMMON_RELATIONSHIP);
    }
}
