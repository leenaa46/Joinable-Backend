<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Personal\PersonalService;
use App\Models\Personal;
use App\Http\Controllers\Controller;

class PersonalController extends Controller
{
    protected $service;

    public function __construct(PersonalService $personalService)
    {
        $this->service = $personalService;
    }

    public function index()
    {
        $res = $this->service->all();
        return $this->success($res, __('success.get_data'));
    }

    public function show(Personal $personal)
    {
        if ($personal->company_id && $personal->company_id != \auth()->user()->company_id) \abort(404, __("fail.not_found"));

        $res = $this->service->getByModel($personal);
        return $this->success($res, __('success.get_data'));
    }

    public function getMyInfo()
    {
        $res = $this->service->getByModel(\auth()->user()->personal);
        return $this->success($res, __('success.get_data'));
    }

    public function giveVariable(Request $request)
    {
        $res = $this->service->givePersonalVariables($request, \auth()->user()->personal);
        return $this->success($res, __('success.save_data'));
    }

    public function updateMyInfo()
    {
        $res = $this->service->update(request(), \auth()->user()->personal);
        return $this->success($res, __('success.update_data'));
    }
}
