<?php

namespace App\Http\Controllers\Ref;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order', 'asc')->get();
        return $this->successResponse($categories, 'Berhasil mengambil daftar kategori');
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Kategori tidak ditemukan', 404);
        }

        return $this->successResponse($category, 'Berhasil mengambil data kategori');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 400, $validator->errors());
        }

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id; // Kunci ke perusahaan user

        $category = Category::create($data);

        return $this->successResponse($category, 'Kategori berhasil ditambahkan', 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Kategori tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 400, $validator->errors());
        }

        $category->update($request->all());

        return $this->successResponse($category, 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Kategori tidak ditemukan', 404);
        }

        // Cek apakah kategori masih memiliki menu aktif
        if ($category->menus()->exists()) {
            return $this->errorResponse('Kategori tidak bisa dihapus karena masih memiliki menu di dalamnya.', 400);
        }

        $category->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus');
    }
}