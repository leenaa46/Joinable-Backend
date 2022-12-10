<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function destroy($uuid)
    {
        $media = Media::where('uuid', $uuid)->firstOrFail();

        return $this->success($media->delete(), __('success.delete_data'));
    }
}
