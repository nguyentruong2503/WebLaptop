<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product_types;
use Illuminate\Http\Request;

class ProductTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product_types::query();

        if ($request->has('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('typeName', 'like', '%' . $keyword . '%');
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'typeName' => 'required|string|max:255',
        ]);

        $loai = Product_types::create([
            'typeName' => $request->typeName,
        ]);

        return response()->json($loai, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loai = Product_types::find($id);
        if (!$loai) {
            return response()->json(['message' => 'Không tìm thấy loại sản phẩm'], 404);
        }
        return $loai;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $loai = Product_types::find($id);
        if (!$loai) {
            return response()->json(['message' => 'Không tìm thấy loại sản phẩm'], 404);
        }

        $request->validate([
            'typeName' => 'required|string|max:255',
        ]);

        $loai->typeName = $request->typeName;
        $loai->save();

        return response()->json($loai);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
         $loai = Product_types::find($id);
        if (!$loai) {
            return response()->json(['message' => 'Không tìm thấy loại sản phẩm'], 404);
        }

        $loai->delete();
        return response()->json(['message' => 'Xóa thành công']);
    }
}
