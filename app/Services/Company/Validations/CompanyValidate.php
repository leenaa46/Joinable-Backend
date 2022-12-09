<?php

namespace App\Services\Company\Validations;

use Illuminate\Http\Request;
use App\Models\Company;

trait CompanyValidate
{
    public function validateSave(Request $request)
    {
        $request->validate([
            "name" => "nullable|max:191|unique:companies,name,NULL,id",
            "slogan" => "nullable|max:191",
            "image_profile" => "nullable|mimes:jpg,png,jpeg|max:20480"
        ]);
    }

    public function validateUpdate(Request $request, Company $company)
    {
        $request->validate([
            "name" => "required|max:191|unique:companies,name,$company->id,id",
            "slogan" => "nullable|max:191",
            "image_profile" => "nullable|mimes:jpg,png,jpeg|max:20480"
        ]);
    }
}
