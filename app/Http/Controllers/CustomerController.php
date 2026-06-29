<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\Outlet;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function verifyTable($outlet_id, $table_no)
    {
        $table = DiningTable::where('outlet_id', $outlet_id)
            ->where('table_no', $table_no)
            ->where('is_active', true)
            ->first();

        if (!$table) {
            // Gunakan errorResponse dari Trait
            return $this->errorResponse('Meja tidak ditemukan atau sedang tidak aktif.', 404);
        }

        $outlet = Outlet::with('company')->find($outlet_id);

        $data = [
            'company_id' => $outlet->company_id,
            'company_name' => $outlet->company->name,
            'outlet_id' => $outlet->id,
            'outlet_name' => $outlet->name,
            'table_id' => $table->id,
            'table_no' => $table->table_no,
        ];

        // Gunakan successResponse dari Trait
        return $this->successResponse($data, 'Berhasil verifikasi meja');
    }

    public function getMenus(Request $request)
    {
        // Validasi secara manual agar bisa menggunakan custom format jika gagal
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 400, $validator->errors());
        }

        $company_id = $request->company_id;

        $categories = Category::where('company_id', $company_id)
            ->orderBy('sort_order', 'asc')
            ->with(['menus' => function ($query) {
                $query->where('is_available', true);
                    //   ->where('stock', '>', 0);
            }])
            ->get();

        return $this->successResponse($categories, 'Berhasil mengambil daftar menu');
    }

    public function checkout(Request $request)
    {
        // 1. Validasi Input JSON
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id',
            'table_id' => 'required|exists:dining_tables,id',
            'customer_name' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi pesanan gagal', 400, $validator->errors());
        }

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItemsData = [];

            // 3. Hitung Ulang Harga Asli dari Database
            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);
                
                // Cek ketersediaan stok/status menu (Opsional tapi direkomendasikan)
                if (!$menu->is_available || $menu->stock < $item['qty']) {
                    throw new \Exception("Menu {$menu->name} tidak tersedia atau stok tidak mencukupi.");
                }

                $itemSubtotal = $menu->price * $item['qty'];
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'menu_id' => $menu->id,
                    'qty' => $item['qty'],
                    'price' => $menu->price, // Simpan harga saat ini sebagai histori
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                    'status' => 'WAITING'
                ];
            }

            // 4. Kalkulasi Pajak dan Total
            $taxAmount = $subtotal * 0.11; // Contoh: Pajak Resto 11%
            $serviceAmount = 0; // Set 0 jika tidak ada service charge
            $totalAmount = $subtotal + $taxAmount + $serviceAmount;

            // 5. Generate Nomor Order Unik (Contoh: ORD-20260626-A1B2C)
            $orderNo = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // 6. Simpan Header Transaksi (Tabel Orders)
            $order = Order::create([
                'order_no' => $orderNo,
                'outlet_id' => $request->outlet_id,
                'table_id' => $request->table_id,
                'customer_name' => $request->customer_name,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_amount' => $serviceAmount,
                'total_amount' => $totalAmount,
                'payment_status' => 'UNPAID',
                'order_status' => 'WAITING_PAYMENT',
            ]);

            // 7. Simpan Detail Transaksi (Tabel Order Items)
            foreach ($orderItemsData as $itemData) {
                // Menggunakan relasi $order->items() untuk insert
                $order->items()->create($itemData);
            }

            // (Opsional) Jika Anda ingin mengurangkan stok menu secara langsung
            // Menu::find($itemData['menu_id'])->decrement('stock', $itemData['qty']);

            // 8. Commit: Permanen-kan perubahan ke Database
            DB::commit();

            // Load relasi item agar tampil rapi di response
            $order->load('items');

            return $this->successResponse($order, 'Pesanan berhasil dibuat, silakan lakukan pembayaran.', 201);

        } catch (\Exception $e) {
            // 9. Rollback: Batalkan semua jika ada error di tengah jalan
            DB::rollBack();
            return $this->errorResponse('Gagal membuat pesanan: ' . $e->getMessage(), 500);
        }
    }
}