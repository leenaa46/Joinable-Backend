<?php

namespace App\Services\Variable\Validations;

use Illuminate\Http\Request;
use App\Models\Variable;

trait VariableValidate
{
    public function validateSave(Request $request)
    {
        $request->validate([
            "name" => "required|max:191|unique:variables,name,NULL,id,type,$request->type,company_id,$request->company_id,deleted_at,NULL",
            "type" => "required|in:activity,career",
            "description" => "nullable|max:191",
            "company_id" => "required|exists:companies,id,deleted_at,NULL",
            "image_logo" => "nullable|mimes:jpg,png,jpeg|max:20480"
        ]);
    }
}
