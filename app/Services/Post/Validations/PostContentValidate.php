<?php

namespace App\Services\Post\Validations;

use Illuminate\Http\Request;

trait PostContentValidate
{
    public function validateSave(Request $request)
    {
        $request->validate([
            "post_id" => "required|exists:posts,id,deleted_at,NULL",
            "title" => "required|max:191",
            "body" => "required",
            "order" => "required|numeric",
            "image_content" => "nullable|mimes:jpg,png,jpeg|max:20480",
        ]);
    }

    public function validateUpdate(Request $request)
    {
        $request->validate([
            "title" => "required|max:191",
            "body" => "required",
            "image_content" => "nullable|mimes:jpg,png,jpeg|max:20480",
        ]);
    }
}
