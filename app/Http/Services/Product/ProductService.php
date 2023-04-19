<?php


namespace App\Http\Services\Product;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductService
{
    public function getAllProducts()
    {
        $products = Product::all();
        return $products;
    }

    public function store(Request $request)
    {
        //return response()->json($request);
        return $this->createOrUpdate( new Product() ,$request);
    }

    public function update(Request $request,$id)
    {
        $product = Product::find($id);
        return $this->createOrUpdate( $product ,$request);
    }

    public function createOrUpdate(Product $product,Request $request)
    { 
        try
        {
            $request->validate([
                                    'name' => 'required',
                                    'price' => 'required',
                                    'quantity' => 'required',
                                    'description' => 'required',
                                    'images'=> 'required|image',
                                ]);

            $product->name = $request->name;
            $product->price = $request->price;
            $product->quantity = $request->quantity;
            $product->description = $request->description;
            $product->save(); 

            if($request->hasFile('images'))
            {
                foreach($request->images as $image)
                {
                    $newimage = new ProductImage();
                    $file = $image;
                    $filename = $file->getClientOriginalName();
                    $file->move('images/product',$filename);
                    $newimage->image = $filename;
                    $newimage->product_id = $product->id;
                    $newimage->save();
                }
            }
            else
            {
                return response("error", 201);
            }

            $response = [
                'product' => $product,
                'image'=>$request->file('images') ,
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
        $product = Product::find($id);
        $product->delete();
    }
}
