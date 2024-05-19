<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\Events\Validated;

class productsController extends Controller
{
    public function read(){
        $product = Products::all();
        return response()->json($product)->setStatusCode(200);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255|string',
            'description' =>'required|max:255|string',
            'price' =>'required|numeric',
            'category_id' =>'required|max:255',
            'expired_at' =>'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
            
    ]);
        
        $jwt = $request->bearerToken();

        $decoded = JWT::decode($jwt, new KEY(env('JWT_SECRET_KEY'),'HS256'));
        $email = $decoded->email;
        
        $cekCategories = Categories::where('name',$request->category_id)->first();
        
        if(!$cekCategories){
            return response()->json('Kategori tidak ada')->setStatusCode(422);
        }
        $id_category = Categories::where('name', $request->category_id)->value('id');
        $validator->setData(array_merge($validator->getData(), ['category_id'=>$id_category]));
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }   
        $validated = $validator->validate();
        $imagePath = $request->file('image')->store('images','public');
        $product = new Products($validated);
        $product->image = $imagePath;
        $product->modified_by = $email;
        $product->save();
        return response()->json('product berhasil disimpan')->setStatusCode(200);
    }
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255|string',
            'description' =>'required|max:255|string',
            'price' =>'required|numeric',
            'category_id' =>'required|max:255',
            'expired_at' =>'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
            
    ]);

        $jwt = $request->bearerToken();

        $decoded = JWT::decode($jwt, new KEY(env('JWT_SECRET_KEY'),'HS256'));
        $email = $decoded->email;
        
        $cekCategories = Categories::where('name',$request->category_id)->first();
        
        if(!$cekCategories){
            return response()->json('Kategori tidak ada')->setStatusCode(422);
        }
        $id_category = Categories::where('name', $request->category_id)->value('id');
        $validator->setData(array_merge($validator->getData(), ['category_id'=>$id_category]));
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }   

        $imagePath = $request->file('image')->store('images','public');
        $product =  Products::find($id);
        if($product){
            $product->update($validator->validate());
            $product->image = $imagePath;
            $product->modified_by = $email;
            $product->save();
        }
        
        return response()->json('product berhasil diubah')->setStatusCode(200);
    }

    public function delete($id){
        $product = Products::find($id);
        if ($product) {
            $product->delete();
            return response()->json('product berhasil dihapus')->setStatusCode(200);
        }
            return response()->json('product tidak tersedia')->setStatusCode(404);
    }
}
