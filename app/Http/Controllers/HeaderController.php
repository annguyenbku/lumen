<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Arr;

class HeaderController extends Controller
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
    public function header_json($user_id,$header_menu_id ,$locale = 'vi_VN'){
        /*Get user name*/
        $user_name = (array)DB::table('users')
                        ->select('first_name','last_name')
                        ->where('id',$user_id)
                        ->first();
        /*Get header menu return result to $header_menus*/
        ////Get the menu_parent in top 
        $header_menu_parent = DB::table('menu_items')
                        ->join('menu_item_translations','menu_items.id','=','menu_item_translations.menu_item_id')
                        ->select('menu_items.id',
                                'menu_items.is_fluid',
                                'menu_items.type',
                                'menu_items.slug',
                                'menu_item_translations.name',
                                'menu_items.url')
                        ->where('menu_id',$header_menu_id)
                        ->where('menu_items.parent_id','1')
                        ->where('menu_item_translations.locale',$locale)
                        ->where('menu_item_translations.name','!=','root')
                        ->get();
        $header_menu_child  = array();
        ////Get submenu for one by one menu_parent           
        foreach($header_menu_parent as $menu){
            $child = array_values( (array)DB::table('menu_items')
                                ->join('menu_item_translations','menu_items.id','=','menu_item_translations.menu_item_id')
                                ->select('menu_items.parent_id',
                                        'menu_items.is_fluid',
                                        'menu_items.type',
                                        'menu_items.slug',
                                        'menu_item_translations.name',
                                        'menu_items.url')
                                ->where('menu_id',$header_menu_id)
                                ->where('menu_items.parent_id',Arr::get((array)$menu, 'id'))
                                ->where('menu_item_translations.locale',$locale)
                                ->where('menu_item_translations.name','!=','root')
                                ->get());
           
            $header_menu_child =array_merge($header_menu_child,$child);
        }
        ////Return array result menus
        $header_menus = array();
        foreach($header_menu_parent as $key=>$menu){
            if(Arr::get((array)$menu, 'is_fluid')==1){
                $header_submenu =$header_menu_child[$key];
            }
            elseif(Arr::get((array)$menu, 'is_fluid')==0){
                $header_submenu = null;
            }
            $menus = array( 
                'title'=>Arr::get((array)$menu, 'name'),
                'type'=>Arr::get((array)$menu, 'type'),
                'url'=>Arr::get((array)$menu, 'url'),
                'is_fluid'=>Arr::get((array)$menu, 'is_fluid'),
                'slug'=>Arr::get((array)$menu, 'slug'),
                'block'=>$header_submenu
            );
            $header_menus = array_merge($header_menus,[$menus]);
        }

        /*get menu active*/
        $menus_active =(array)DB::table('menu_items')->select('is_active')->where('id',$header_menu_id)->first();
        /*Get currency anh social link from database*/
        $current_rate = DB::table('currency_rates')->select('id','currency')->get();
        $social_link = DB::table('settings')
                        ->select('key')
                        ->where('plain_value','s:1:"#";')
                        ->get();

        /*Return json header*/ 
        $response = array(
            'firstname'=>$user_name['first_name']??null,
            'lastname'=>$user_name['last_name']??null,
            'active_menu'=>$menus_active["is_active"]??null,
            'header_menu'=>$header_menus,
            'currency' =>$current_rate,
            'social_link'=>$social_link
        );
       
        return response()->json($response,200);   
    }
}
