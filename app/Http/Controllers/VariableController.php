<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Variable\VariableService;
use App\Models\Variable;
use App\Http\Controllers\Controller;

class VariableController extends Controller
{
    protected $service;

    public function __construct(VariableService $variableService)
    {
        $this->service = $variableService;
    }

    public function index()
    {
        $res = $this->service->all();
        return $this->success($res, __('success.get_data'));
    }

    public function show(Variable $variable)
    {
        if ($variable->company_id && $variable->company_id != \auth()->user()->company_id) \abort(404, __("fail.not_found"));

        $res = $this->service->getByModel($variable);
        return $this->success($res, __('success.get_data'));
    }

    public function store()
    {
        $res = $this->service->create(request());
        return $this->success($res, __('success.save_data'));
    }
}
