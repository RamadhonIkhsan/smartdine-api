<?php

namespace App\Http\Controllers\Ref;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * 1. GET ALL - Dikelompokkan berdasarkan Perusahaan -> Kategori -> Menu
     */
    public function index()
    {
        $user = Auth::user();

        // Jika SUPERADMIN (Platform Owner), munculkan semua perusahaan
        if ($user->role === 'SUPERADMIN') {
            $tree = Company::with(['categories' => function ($q) {
                $q->orderBy('sort_order', 'asc')->with('menus');
            }])->get();
        } else {
            // Jika OWNER/KARYAWAN, hanya munculkan perusahaan mereka sendiri
            $tree = Company::with(['categories' => function ($q) {
                $q->orderBy('sort_order', 'asc')->with('menus');
            }])->where('id', $user->company_id)->get();
        }

        return $this->successResponse($tree, 'Berhasil');
    }

    public function show($id)
    {
        $menu = Menu::with('category')->find($id);

        if (!$menu) {
            return $this->errorResponse('Menu tidak ditemukan', 404);
        }

        return $this->successResponse($menu, 'Berhasil mengambil data menu');
    }

    /**
     * 3. CREATE MENU - Diperketat wajib sesuai Company & Kategori yang sah
     */
    public function store(Request $request)
    {
        $userCompanyId = Auth::user()->company_id;

        $validator = Validator::make($request->all(), [
            // Validasi agar category_id yang dipilih HARUS milik company user yang sedang login
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($userCompanyId) {
                    $query->where('company_id', $userCompanyId);
                }),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cooking_time' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'is_available' => 'boolean'
        ], [
            'category_id.exists' => 'Kategori tidak valid atau bukan milik perusahaan Anda.'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 400, $validator->errors());
        }

        $data = $request->all();
        $data['company_id'] = $userCompanyId;

        $menu = Menu::create($data);

        return $this->successResponse($menu, 'Menu berhasil ditambahkan', 201);
    }

    /**
     * UPDATE MENU
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return $this->errorResponse('Menu tidak ditemukan', 404);
        }

        $userCompanyId = Auth::user()->company_id;

        $validator = Validator::make($request->all(), [
            'category_id' => [
                Rule::exists('categories', 'id')->where(function ($query) use ($userCompanyId) {
                    $query->where('company_id', $userCompanyId);
                }),
            ],
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'cooking_time' => 'integer|min:1',
            'stock' => 'integer|min:0',
            'is_available' => 'boolean'
        ], [
            'category_id.exists' => 'Kategori tidak valid atau bukan milik perusahaan Anda.'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 400, $validator->errors());
        }

        $menu->update($request->all());

        return $this->successResponse($menu, 'Menu berhasil diperbarui');
    }

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