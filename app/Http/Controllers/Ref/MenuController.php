<?php

namespace App\Http\Controllers\Ref;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * GET ALL: Menampilkan daftar menu.
     * Bisa difilter dengan ?company_id=1 atau ?category_id=1
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $menus = $query->get();
        return $this->successResponse($menus, 'Berhasil mengambil daftar menu');
    }

    /**
     * GET BY ID: Menampilkan satu menu spesifik
     */
    public function show($id)
    {
        // $menu = Menu::with(['company', 'category'])->find($id);
        $menu = Menu::find($id);

        if (!$menu) {
            return $this->errorResponse('Menu tidak ditemukan', 404);
        }

        return $this->successResponse($menu, 'Berhasil mengambil data menu');
    }

    /**
     * CREATE / STORE: Menambahkan menu baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cooking_time' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'is_available' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        // Validasi Ekstra: Pastikan category_id benar-benar milik company_id tersebut
        $category = Category::find($request->category_id);
        if ($category && $category->company_id != $request->company_id) {
            return $this->errorResponse('Kategori tidak valid untuk perusahaan ini.', 400);
        }

        // Set default is_available ke true jika tidak dikirim
        $data = $request->all();
        $data['is_available'] = $request->input('is_available', true);

        $menu = Menu::create($data);

        return $this->successResponse($menu, 'Menu berhasil ditambahkan', 200);
    }

    /**
     * UPDATE: Mengubah data menu
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return $this->errorResponse('Menu tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'price' => 'numeric|min:0',
            'cooking_time' => 'integer|min:1',
            'stock' => 'integer|min:0',
            'is_available' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        // Jika mengubah kategori, pastikan kategori baru masih di dalam company yang sama
        if ($request->has('category_id')) {
            $category = Category::find($request->category_id);
            if ($category && $category->company_id != $menu->company_id) {
                return $this->errorResponse('Kategori yang dipilih bukan milik perusahaan ini.', 400);
            }
        }

        // Amankan agar company_id tidak bisa diubah sembarangan via update
        $menu->update($request->except('company_id'));

        return $this->successResponse($menu, 'Menu berhasil diperbarui');
    }

    /**
     * DELETE / DESTROY: Menghapus menu
     */
    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return $this->errorResponse('Menu tidak ditemukan', 404);
        }

        $menu->delete();

        return $this->successResponse(null, 'Menu berhasil dihapus');
    }
}