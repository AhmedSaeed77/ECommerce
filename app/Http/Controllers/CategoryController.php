<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\Category\CategoryService;

class CategoryController extends Controller
{
    protected $categoryservice;
    public function __construct(CategoryService $categoryservice)
    {
        $this->categoryservice = $categoryservice;
    }

    public function index()
    {
        $products = $this->categoryservice ->getAllCategories();
        $newproducts [] = $products;
        return returnData($newproducts, 'done',200);
    }

    public function store(Request $request)
    {
        $products = $this->categoryservice->store($request);
        $newproducts [] = $products;
        return returnData($newproducts, 'done',200);
    }

    public function update(Request $request,$id)
    {
        $products = $this->categoryservice->update($request,$id);
        $newproducts [] = $products;
        return returnData($newproducts, 'done',200);
    }

    public function delete($id)
    {
        $products = $this->categoryservice->delete($id);
        $newproducts [] = $products;
        return returnData($newproducts, 'done',200);
    }
}
