<?php

namespace App\Services\Auth\Validations;

use Illuminate\Http\Request;

trait UserValidate
{
    public function validateRegister(Request $request)
    {
        $request->validate([
            "email" => "required|max:191|email|unique:users,email,NULL,id",
            "password" => "required|min:8|confirmed",
            "role" => "nullable|exists:roles,name",
            "company_id" => "required|exists:companies,id,deleted_at,NULL"
        ]);
    }

    public function validateLogin(Request $request)
    {
        $request->validate([
            "credential" => "required|max:191",
            "password" => "required|min:8",
        ]);
    }
}
