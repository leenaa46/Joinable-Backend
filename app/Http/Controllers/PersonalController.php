<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Personal\PersonalService;
use App\Models\Personal;
use App\Http\Controllers\Controller;

class PersonalController extends Controller
{
    protected $service;

    public function __construct(PersonalService $PersonalService)
    {
        $this->service = $PersonalService;
    }

    public function index()
    {
        $res = $this->service->all();
        return $this->success($res, __('success.get_data'));
    }

    public function show(Personal $Personal)
    {
        if ($Personal->company_id && $Personal->company_id != \auth()->user()->company_id) \abort(404, __("fail.not_found"));

        $res = $this->service->getByModel($Personal);
        return $this->success($res, __('success.get_data'));
    }

    public function getMyInfo()
    {
        $res = $this->service->getByModel(\auth()->user()->personal);
        return $this->success($res, __('success.get_data'));
    }
}
