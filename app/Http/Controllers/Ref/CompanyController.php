<?php

namespace App\Http\Controllers\Ref;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * GET ALL: Menampilkan semua data perusahaan
     */
    public function index()
    {
        $companies = Company::all();
        return $this->successResponse($companies, 'Berhasil mengambil daftar perusahaan');
    }

    /**
     * GET BY ID: Menampilkan satu data perusahaan secara spesifik
     */
    public function show($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return $this->errorResponse('Perusahaan tidak ditemukan', 404);
        }

        return $this->successResponse($company, 'Berhasil mengambil data perusahaan');
    }

    /**
     * CREATE / STORE: Menambahkan perusahaan baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:companies,domain',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'service_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        $company = Company::create($request->all());

        return $this->successResponse($company, 'Perusahaan berhasil ditambahkan', 200);
    }

    /**
     * UPDATE: Mengubah data perusahaan yang sudah ada
     */
    public function update(Request $request, $id)
    {
        $company = Company::find($id);

        if (!$company) {
            return $this->errorResponse('Perusahaan tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'domain' => 'nullable|string|max:255|unique:companies,domain,' . $id,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'service_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400, $validator->errors());
        }

        $company->update($request->all());

        return $this->successResponse($company, 'Perusahaan berhasil diperbarui');
    }

    /**
     * DELETE / DESTROY: Menghapus data perusahaan
     */
    public function destroy($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return $this->errorResponse('Perusahaan tidak ditemukan', 404);
        }

        $company->delete();

        return $this->successResponse(null, 'Perusahaan berhasil dihapus');
    }
}