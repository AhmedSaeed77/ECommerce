<?php


namespace App\Http\Services\Category;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CategoryService
{
    public function getAllCategories()
    {
        //$categories = Category::all();
        $categories = Category::with('product')->get();
        return $categories;
    }

    public function store(Request $request)
    {
        return $this->createOrUpdate( new Category() ,$request);
    }

    public function update(Request $request,$id)
    {
        $category = Category::find($id);
        return $this->createOrUpdate( $category ,$request);
    }

    public function createOrUpdate(Category $category,Request $request)
    { 
        try
        {
            $request->validate([
                                    'name' => 'required',
                                ]);

            $category->name = $request->name;
            $category->save();

            $response = [
                'category' => $category,
                'message' => 'success',
            ];   
            
            return response($response, 201);
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 404);
        }
    }

    public function delete($id)
    {
        $category = Category::find($id);
        $category->delete();
    }
}
