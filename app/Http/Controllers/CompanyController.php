<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Company\CompanyService;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    protected $service;

    public function __construct(CompanyService $companyService)
    {
        $this->service = $companyService;
    }

    public function getByCode($joinableCode)
    {
        $res = $this->service->getByCode($joinableCode);
        return $this->success($res, __('success.get_data'));
    }

    public function getMyInfo()
    {
        $res = $this->service->getByModel(\auth()->user()->company);
        return $this->success($res, __('success.get_data'));
    }

    public function update()
    {
        $res = $this->service->update(request(), \auth()->user()->company);
        return $this->success($res, __('success.update_data'));
    }
}
