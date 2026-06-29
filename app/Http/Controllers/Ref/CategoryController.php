<?php

namespace App\Http\Controllers\Ref;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * GET ALL: Menampilkan semua kategori.
     * Kita tambahkan filter query 'company_id' agar bisa melihat kategori per perusahaan.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Jika di Postman mengirimkan parameter ?company_id=1
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $categories = $query->orderBy('sort_order', 'asc')->get();
        
        return $this->successResponse($categories, 'Berhasil mengambil daftar kategori');
    }

    /**
     * GET BY ID: Menampilkan satu kategori spesifik
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Kategori tidak ditemukan', 404);
        }

        return $this->successResponse($category, 'Berhasil mengambil data kategori');
    }

    /**
     * CREATE / STORE: Menambahkan kategori baru untuk perusahaan tertentu
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id', // Wajib valid ke tabel companies
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255', // Nama icon (misal: 'coffee', 'utensils')
            'sort_order' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        $category = Category::create($request->all());

        return $this->successResponse($category, 'Kategori berhasil ditambahkan', 200);
    }

    /**
     * UPDATE: Mengubah data kategori
     */
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
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        // Jangan izinkan mengubah company_id secara tidak sengaja demi keamanan data
        $category->update($request->only(['name', 'icon', 'sort_order']));

        return $this->successResponse($category, 'Kategori berhasil diperbarui');
    }

    /**
     * DELETE / DESTROY: Menghapus kategori
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Kategori tidak ditemukan', 404);
        }

        // Proteksi bisnis: Kategori tidak boleh dihapus jika masih ada menunya
        if ($category->menus()->exists()) {
            return $this->errorResponse('Kategori gagal dihapus karena masih terikat dengan beberapa menu.', 400);
        }

        $category->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus');
    }
}