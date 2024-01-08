<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;




class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::with("children")->whereNull('parent_id')->paginate(10);
        // $data = Category::with("children")->whereNull('parent_id')->paginate(10);
        // $data = Category::with("children")->paginate(10);
        return CategoryResource::collection($data);
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','min:3',Rule::unique('categories', 'name')->ignore($request->id)->whereNull('deleted_at'),
                'string'
            ],
                    'description' => ['string'],
            'parent' => ['integer'],
            'image' => ['image', 'max:2048', 'mimes:jpeg,png,jpg'],


            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->slug= Str::slug( $request->name);
        if($request->parent){
            $category->parent_id = $request->parent;
        }
        if($request->image){
            $category->image = $request->image->store('categories', 'public');
        }
        $category->save();
        return $category;
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
{
    $category = Category::where('slug', $slug)->with("children","parent")->first();

    if (!$category) {
        abort(404, 'Category not found');
    }
    $data=new CategoryResource($category);

    return $data;
}


    /**
 * Update the specified resource in storage.
 */
public function update(Request $request, $id)
{
    $category = Category::find($id);
    if (!$category) {
        abort(404, 'Category not found');
    }

    $validator = Validator::make($request->all(), [
        'name' => ['min:3', 'string'],
        'description' => ['string'],
        'parent' => ['integer'],
        'image' => ['image', 'max:2048', 'mimes:jpeg,png,jpg'],
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    if ($request->has('name')) {
        // Update only if the name attribute is present in the request
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
    }

    if ($request->has('description')) {
        $category->description = $request->description;
    }

    if ($request->has('parent')) {
        $category->parent_id = $request->parent;
    }

    if ($request->hasFile('image')) {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->image = $request->image->store('categories', 'public');
    }

    $category->save();

    return response()->json($category, 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $category = Category::where('slug', $slug)->with('children')->first();
    
        if (!$category) {
            abort(404, 'Category not found');
        }
    
        $this->deleteCategoryRecursively($category);
        $data = [
            'message' => 'Category and its children deleted successfully'
        ];
    
        return response()->json($data, 200);
    }
    
    private function deleteCategoryRecursively($category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
    
        foreach ($category->children as $child) {
            $this->deleteCategoryRecursively($child);
        }
    
        $category->delete();
    }
    
}