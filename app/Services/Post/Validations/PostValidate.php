<?php

namespace App\Services\Post\Validations;

use Illuminate\Http\Request;

trait PostValidate
{
    public function validateSave(Request $request)
    {
        $request->validate([
            "created_by" => "required|exists:users,id",
            "title" => "required|max:191",
            "type" => "required|in:faq,event,feedback",
            "body" => "nullable",
            "image_title" => "nullable|mimes:jpg,png,jpeg|max:20480",
        ]);
    }

    public function validateUpdate(Request $request)
    {
        $request->validate([
            "title" => "required|max:191",
            "body" => "nullable",
            "image_title" => "nullable|mimes:jpg,png,jpeg|max:20480",
        ]);
    }
}
