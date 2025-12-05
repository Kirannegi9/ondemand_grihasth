<?php

namespace App\Http\Controllers;

class SubCategoryController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
    }
    
	  public function index()
    {
        return view("subcategories.index");
    }

     public function edit($id)
    {
    	return view('subcategories.edit')->with('id', $id);
    }

    public function create()
    {
        return view('subcategories.create');
    }

}


