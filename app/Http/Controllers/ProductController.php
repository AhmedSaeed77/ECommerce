<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Services\Product\ProductService;


class ProductController extends Controller
{
    protected $productservice;
    public function __construct(ProductService $productservice)
    {
        $this->productservice = $productservice;
    }

    public function index()
    {
        $products = $this->productservice ->getAllProducts();
        $newproducts [] = $products;
        return returnData($newproducts, 'done',200);
    }

    public function store(Request $request)
    {
        return response()->json($request, 404);
        $products = $this->productservice->store($request);
        $newproducts [] = $products;
        return returnData($newproducts, 'done',200);
    }

    public function update(Request $request,$id)
    {
        $products = $this->productservice->update($request,$id);
        $newproducts [] = $products;
        return returnData($newproducts, 'done',200);
    }

    public function delete($id)
    {
        $products = $this->productservice->delete($id);
        $newproducts [] = $products;
        return returnData($newproducts, 'done',200);
    }
}
