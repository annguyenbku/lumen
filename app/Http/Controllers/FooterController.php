<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Page;


class FooterController extends Controller
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
    private function get_value_storefront($name){
        $result = DB::table('settings')
                ->select('plain_value')
                ->where('key',$name)
                ->first();
        return substr(Arr::get((array)$result, 'plain_value'),7);
    }
    public function footer_json($footer_menu_id = 3,$locale = 'vi_VN'){
        /*Get footer menu return result to $footer_menus*/
        ////Get the menu_parent in top 
        $footer_menu_parent = DB::table('menu_items')
                        ->join('menu_item_translations','menu_items.id','=','menu_item_translations.menu_item_id')
                        ->select('menu_items.id',
                                'menu_items.is_fluid',
                                'menu_items.type',
                                'menu_items.slug',
                                'menu_item_translations.name',
                                'menu_items.url')
                        ->where('menu_id',$footer_menu_id) 
                        // ->where('menu_items.parent_id','1')
                        ->where('menu_item_translations.locale',$locale)
                        ->where('menu_item_translations.name','!=','root')
                        ->get();
        $footer_menu_child  = array();
        ////Get submenu for one by one menu_parent           
        foreach($footer_menu_parent as $menu){
            $child = array_values( (array)DB::table('menu_items')
                                ->join('menu_item_translations','menu_items.id','=','menu_item_translations.menu_item_id')
                                ->select('menu_items.parent_id',
                                        'menu_items.is_fluid',
                                        'menu_items.type',
                                        'menu_items.slug',
                                        'menu_item_translations.name',
                                        'menu_items.url')
                                ->where('menu_id',$footer_menu_id)
                                ->where('menu_items.parent_id',Arr::get((array)$menu, 'id'))
                                ->where('menu_item_translations.locale',$locale)
                                ->where('menu_item_translations.name','!=','root')
                                ->get());
           
            $footer_menu_child =array_merge($footer_menu_child,$child);
        }
        ////Return array result menus
        $footer_menus = array();
        foreach($footer_menu_parent as $key=>$menu){
            if(Arr::get((array)$menu, 'is_fluid')==1){
                $footer_submenu =$footer_menu_child[$key];
            }
            elseif(Arr::get((array)$menu, 'is_fluid')==0){
                $footer_submenu = null;
            }
            $menus = array( 
                'title'=>Arr::get((array)$menu, 'name'),
                'type'=>Arr::get((array)$menu, 'type'),
                'url'=>Arr::get((array)$menu, 'url'),
                'is_fluid'=>Arr::get((array)$menu, 'is_fluid'),
                'slug'=>Arr::get((array)$menu, 'slug'),
                'block'=>$footer_submenu
            );
            $footer_menus = array_merge($footer_menus,[$menus]);
        }
        /*Get currency anh social link from database*/
        $social_link = DB::table('settings')
                        ->select('key')
                        ->where('plain_value','s:1:"#";')
                        ->get();
        /*Get some information footer*/
        
        /*return footer json*/
        $response = array(
            'footer_menu'=>$footer_menus,
            'social_link'=>$social_link,
            'address'=>$this->get_value_storefront('store_address_1'),
            'phone_number' =>$this->get_value_storefront('store_phone')
        );
       
        return response()->json($response,200);  
    }

    public function getPage($slug, Request $request){

        if($request->has('cache')){
  
          return response(file_get_contents(public_path().'/category_all.json'), 200)->header('Content-Type', 'application/json');
        }else{
  
  
        }
  
        if($slug == 'test'){
          // return Home
          $s = 'home';
        }else{
          $s = $slug;
        }
        // var_dump($s);
        // die;
  
        $page = Page::where('slug',$s)->first();
  
        $blocks = $page->blocks;
        $b = [];
        $arrblockIds =[];
        if($blocks->count()>0){
          foreach($blocks as $block){
            // dd($block->content);
            $a = [];
            $a['id'] = $block->id;
            $a['position'] = $block->position;
            $a['block_id'] = $block->block_id;
            $a['name'] = $block->name;
            // $a['content'] = $block->content;
            $a['values'] = json_decode($block->content,true);
            $b[] = $a;
            $arrblockIds[] = $block->block_id;
          }
        }
        // if(count($arrblockIds)>0){
        //   $blockValues =  \Modules\Block\Entities\Block::withoutGlobalScope('active')->whereIn('id',$arrblockIds)->get();
        //   dd($blockValues); 
        // }
  
  
        if($page){
          $page = $page->toArray();
        }
        $page['blocks'] = $b;
  
        return response()->json($page, 200);
    }
}
