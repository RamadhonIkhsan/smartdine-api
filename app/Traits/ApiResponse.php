<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Format untuk response sukses
     */
    protected function successResponse($data = null, $message = 'Berhasil', $code = 200)
    {
        return response()->json([
            'rc' => $code,
            'rm' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Format untuk response gagal/error
     */
    protected function errorResponse($message = 'Terjadi kesalahan', $code = 400, $data = null)
    {
        $response = [
            'rc' => $code,
            'rm' => $message,
        ];

        // Tampilkan key 'data' hanya jika ada isinya (misal untuk list error validasi)
        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}