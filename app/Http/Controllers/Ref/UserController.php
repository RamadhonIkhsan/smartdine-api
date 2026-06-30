<?php

namespace App\Http\Controllers\Ref;

use DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * GET ALL: Menampilkan semua user (Bisa difilter dengan ?company_id=1)
     */
    public function index(Request $request)
    {
        $query = DB::table('users')
                ->select(
                    'users.*',
                    'c.name as company_name',
                    'r.role_name'
                )
                ->leftJoin('companies as c', 'c.id', '=', 'users.company_id')
                ->leftJoin('ref_role as r', 'r.id', '=', 'users.role_id');

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $users = $query->get();
        return $this->successResponse($users, 'Berhasil mengambil daftar pengguna');
    }

    /**
     * GET BY ID: Menampilkan satu user spesifik
     */
    public function show($id)
    {
        $user = DB::table('users')
                ->select(
                    'users.*',
                    'c.name as company_name',
                    'r.role_name'
                )
                ->leftJoin('companies as c', 'c.id', '=', 'users.company_id')
                ->leftJoin('ref_role as r', 'r.id', '=', 'users.role_id')
                ->where('users.id', $id)
                ->get();

        if (!$user) {
            return $this->errorResponse('Pengguna tidak ditemukan', 404);
        }

        return $this->successResponse($user, 'Berhasil mengambil data pengguna');
    }

    /**
     * CREATE / STORE: Menambahkan user/karyawan baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'fullname' => 'required|string|max:255',
            'email' => 'required|string',
            'username' => 'required|string|max:50|unique:users,username', // Username harus unik
            'role_id' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        $data = $request->all();
        // Enkripsi password menggunakan Bcrypt/Hash bawaan Laravel
        $data['password'] = Hash::make('password123');

        $user = User::create($data);

        return $this->successResponse($user, 'Pengguna berhasil ditambahkan', 200);
    }

    /**
     * UPDATE: Mengubah data user
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('Pengguna tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            'fullname' => 'string|max:255',
            'username' => 'string|max:50|unique:users,username,' . $id, // Unik kecuali milik user ini sendiri
            'role_id' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        $data = $request->all();

        $user->update($data);

        return $this->successResponse($user, 'Pengguna berhasil diperbarui');
    }

    /**
     * DELETE / DESTROY: Menghapus user
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('Pengguna tidak ditemukan', 404);
        }

        // Proteksi agar user tidak menghapus dirinya sendiri secara tidak sengaja saat login nanti
        if (auth()->id() == $user->id) {
            return $this->errorResponse('Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.', 400);
        }

        $user->delete();

        return $this->successResponse(null, 'Pengguna berhasil dihapus');
    }
}