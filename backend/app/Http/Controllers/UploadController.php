<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {

        try {
            if (!$request->hasFile('image')) {
                return response()->json(['error' => 'Không có file được chọn'], 400);
            }

            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            
            return response()->json(['url' => $uploadedFileUrl], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi upload ảnh: ' . $e->getMessage()], 500);
        }

    }
}