<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function test(){
        $test = DB::table('menus')->get();
        print_r($test);
    }
    public function get_data_categories(){
        $categories_data = DB::table('categories')->select('id','slug')->get();

        
        // $categories_data = DB::select('select categories.id,attribute_categories.attribute_id from categories
        //                                 INNER JOIN attribute_categories
        //                                 ON categories.id=attribute_categories.category_id
        // ');
        // print_r($categories_data);
        return response()->json(array('categories'=>$categories_data ),200);   
    }
}
