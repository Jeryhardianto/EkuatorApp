<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Products;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionRequest;

class TransactionsController extends Controller
{
    public function GetListTransaction(Request $request)
    {
        $params = $request->only('limit', 'sortby', 'orderby');
        // dd($params['limit'], $params['sortby'], $params['orderby']);
        $role = auth()->user()->role;
        $userId = auth()->user()->id;

        if($role == 1){
            $transactions = Transaction::orderBy($params['sortby'], $params['orderby'])->limit($params['limit'])->get();
        }else{
            $transactions = Transaction::where('user_id', $userId)->orderBy($params['sortby'], $params['orderby'])->limit($params['limit'])->get();
        }
      
        if($transactions)
        {
            return response()->json([
                'message' => 'All Product',
                'code' => 200,
                'userID' => $userId, 
                'error' => false,
                'data' => $transactions
            ], 200);
        }else{
            return response()->json([
                'message' => 'Cek your parameters',
            ], 404);
        }
        
    }

    public function GetListTransactionById(Request $request, $id)
    {
        $role = auth()->user()->role;
        $userId = auth()->user()->id;
        // dd($userId);

        if($role == 1){
            $transactions = Transaction::where('id', $id)->get();
        }else{
            $transactions = Transaction::where('id', $id)->where('user_id', $userId)->get();
        }
      
        if(count($transactions) > 0)
        {
            return response()->json([
                'message' => 'Get Detail Product',
                'code' => 200,
                'userID' => $userId, 
                'error' => false,
                'data' => $transactions
            ], 200);
        }else{
            return response()->json([
                'message' => 'You not allowed to access',
            ], 404);
        }
        
    }





    public function createTransaction(TransactionRequest $request)
    {
       
        if(auth()->user()->role == 1){
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
      
        $product = Products::where('id', $request->produk_id)->get();
        if (!$product) {
            // return response()->json(['message' => 'Produk tidak ditemukan'], 404);
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }
        $quantity = $request->input('quantity');

        if ($quantity > $product[0]->quantity) {
            // return response()->json(['message' => 'Stok produk tidak mencukupi'], 400);
            if($product[0]->quantity == 0){
                $stock = "Stock Empty";
            }
            return response()->json([
                'message' => 'Insufficient product stock',
                "stock" => $stock
            ], 200);
        }

        // harga = hargaproduk * qty
        $price = $product[0]->price * $quantity;
        // pajak = harga * 10% (0.1) 
        $tax = $price * 0.1;
        // BiayaAdmin = harga * 0.005(5%) + pajak
        $adminFee = $price * 0.05 + $tax;
        // TotalAkhir = harga + pajak + BiayaAdmin
        $total = $price + $tax + $adminFee;

        DB::beginTransaction();

        try {
           
            Transaction::create([
                'user_id' => auth()->user()->id,
                'product_id' => $product[0]->id,
                'quantity' => $quantity,
                // Harga produk per item
                'price' => $product[0]->price,
                'tax' => $tax,
                'admin_fee' => $adminFee,
                'total' => $total,
            ]);

            $product[0]->decrement('quantity', $quantity);

            DB::commit();

            // return response()->json(['message' => 'Transaksi berhasil'], 200);
            return  response()->json([
                'message' => 'Transaction created successfully.',
                'code' => 201,
                'error' => false
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return  response()->json([
                'message' => 'Something error'.$e,
            ], 400);
        }
    }
}
