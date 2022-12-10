<?php

namespace App\Services\Personal\Validations;

use Illuminate\Http\Request;
use App\Models\Personal;

trait PersonalValidate
{
    public function validateSave(Request $request)
    {
        $request->validate([
            "user_id" => "required|exists:users,id",
            "name" => "nullable|max:191",
            "gender" => "nullable|in:male,female,other",
            "gender_description" => "nullable",
            "joined_at" => "nullable|date",
            "introduce_message" => "nullable",
            "image_profile" => "nullable|mimes:jpg,png,jpeg|max:20480"
        ]);
    }
}
