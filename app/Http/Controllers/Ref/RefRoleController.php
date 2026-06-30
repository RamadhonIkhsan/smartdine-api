<?php

namespace App\Http\Controllers\Ref;

use App\Http\Controllers\Controller;
use App\Models\RefRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RefRoleController extends Controller
{
    /**
     * GET ALL: Menampilkan semua role referensi
     */
    public function index()
    {
        $roles = RefRole::all();
        return $this->successResponse($roles, 'Berhasil mengambil daftar role');
    }

    /**
     * GET BY ID: Menampilkan satu role spesifik
     */
    public function show($id)
    {
        $role = RefRole::find($id);

        if (!$role) {
            return $this->errorResponse('Role tidak ditemukan', 404);
        }

        return $this->successResponse($role, 'Berhasil mengambil data role');
    }

    /**
     * CREATE / STORE: Menambahkan role baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Validasi unik ke tabel ref_role, kolom name
            'role_name' => 'required|string|max:50|unique:ref_role,role_name',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        $data = $request->all();
        $data['is_active'] = $request->input('is_active', true); // Default true jika tidak diisi

        $role = RefRole::create($data);

        return $this->successResponse($role, 'Role berhasil ditambahkan', 200);
    }

    /**
     * UPDATE: Mengubah data role
     */
    public function update(Request $request, $id)
    {
        $role = RefRole::find($id);

        if (!$role) {
            return $this->errorResponse('Role tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            // Pengecualian unik untuk id milik role ini sendiri saat update
            'role_name' => 'string|max:50|unique:ref_role,role_name,' . $id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        $role->update($request->all());

        return $this->successResponse($role, 'Role berhasil diperbarui');
    }

    /**
     * DELETE / DESTROY: Menghapus role
     */
    public function destroy($id)
    {
        $role = RefRole::find($id);

        if (!$role) {
            return $this->errorResponse('Role tidak ditemukan', 404);
        }

        $role->delete();

        return $this->successResponse(null, 'Role berhasil dihapus');
    }
}