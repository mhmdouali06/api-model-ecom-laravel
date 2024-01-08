<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data=product::with("category","images")->paginate(10);
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','min:3',Rule::unique('products', 'name')->ignore($request->id)->whereNull('deleted_at'),
                'string'
            ],
            'description' => ['string'],
            'image' => ['image', 'max:2048', 'mimes:jpeg,png,jpg'],


            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = new product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->slug= Str::slug( $request->name);
        if($request->image){
            $product->image = $request->image->store('products', 'public');
        }
       
       if($product->save()){    
        if($request->images){
            foreach($request->images as $image){
                $product->images()->create([
                    'image' => $image->store('products', 'public'),
                    "product_id" => $product->id
                ]);
            }
        }
       }
        return $product;
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $product=product::where("slug","=",$slug)->first();

        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
   /**
 * Update the specified resource in storage.
 */
public function update(Request $request, product $product)
{
    $validator = Validator::make($request->all(), [
        'name' => ['required', 'min:3', Rule::unique('products', 'name')->ignore($product->id)->whereNull('deleted_at'), 'string'],
        'description' => ['string'],
        'image' => ['image', 'max:2048', 'mimes:jpeg,png,jpg'],
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    if($request->has('name')){

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);

    }
    if($request->has('description')){
        $product->description = $request->description;
    }

    if ($request->hasFile('image')) {
        Storage::disk('public')->delete($product->image);

        $product->image = $request->image->store('products', 'public');
    }

    $product->save();

    return $product;
}

/**
 * Remove the specified resource from storage.
 */
public function destroy(product $product)
{
    $product->delete();

    return response()->json(['message' => 'Product deleted successfully']);
}

}
