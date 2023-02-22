<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductRequest;

class ProductsController extends Controller
{
 
    public function index(Request $request){
        

      

        // dd($request->all());
        $params = $request->only('limit', 'sortby', 'orderby');
        // dd($params['limit']);
        $products = Products::orderBy($params['sortby'], $params['orderby'])->limit($params['limit'])->get();
        // dd($products);

        if($products){
            // return response()->json(['data' => $products]);
            return response()->json([
                'message' => 'All Product',
                'code' => 200,
                'error' => false,
                'data' => $products
            ], 200);
        }else{
            return response()->json([
                'message' => 'Cek your parameters',
            ], 404);
        }

    }

    public function getProductById($id)
    {
        $products = Products::where('id', $id)->get();

        return response()->json([
            'message' => 'Get ProductById',
            'code' => 200,
            'error' => false,
            'data' => $products
        ], 200);

    }

    public function save(ProductRequest $request){
        // dd($request->user()->role);
        if (auth()->user()->role == 1) {
            $product = Products::create([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'quantity' => $request->input('quantity')
            ]);
            $product->save();

            // return response()->json(['message' => 'Product created successfully']);
            return response()->json([
                'message' => 'Product created successfully',
                'code' => 201,
                'error' => false,
                'data' => $product
            ], 201);
        } else {
            // return response()->json(['error' => 'Unauthorized'], 403);
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }
    
    }

    public function updateProduct(ProductRequest $request, $id){
        // dd(auth()->user()->role);
        // hanya admin yang dapat melakukan update
        // if(auth()->user()->role !== 1){
        //     abort(403, 'Unauthorized action.');
        // }

        if(auth()->user()->role == 1){
            $product = Products::find($id);
            $product->update($request->all());
    
            return  response()->json([
                'message' => 'Product updated successfully',
                'code' => 200,
                'error' => false,
                'data' => $product
            ], 200);
        }else{
            return  response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

    }

    public function destroy($id)
    {
        // periksa apakah user adalah admin
        if (auth()->user()->role == 1) {
            $product = Products::find($id);
            $product->delete();
    
            // return response()->json(['message' => 'Product deleted successfully.']);
            return  response()->json([
                'message' => 'Product deleted successfully.',
                'code' => 200,
                'error' => false
            ], 200);
           
        }else{
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
    }
}
