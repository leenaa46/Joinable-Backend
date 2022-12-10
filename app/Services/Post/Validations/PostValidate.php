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
            "type" => "required|in:faq,event,feedback,company_content",
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

    public function validateSaveEvent(Request $request)
    {
        $request->validate([
            "activities" => "nullable|array",
            "activities.*" => "nullable|exists:variables,id,type,activity,deleted_at,NULL",
            "schedule" => "required|date|after_or_equal:" . date('Y-m-d H:i:s')
        ]);
    }

    public function validateSaveFeedback(Request $request)
    {
        $request->validate([
            "feedback_status_id" => "required|exists:variables,id,type,feedback_status,deleted_at,NULL",
        ]);
    }
}
