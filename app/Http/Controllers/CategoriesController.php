<?php


namespace App\Http\Controllers;

use Laravel\Lumen\Application;
use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CategoriesController extends Controller
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

    private function reformatRequest($request)
    {
        return [
            'fromPrice' => $this->array_get($request, 'fromPrice', null),
            'toPrice'   => $this->array_get($request, 'toPrice', null),
            'sort' => $this->array_get($request, 'sort', null),
            'attribute' => $this->array_get($request, 'attribute', []),
            'ajax' => $this->array_get($request, 'ajax', null),
            'page' => $this->array_get($request, 'page', null)
        ];
    }

    public function show_categories($slug1, $slug2 = null, $slug3 = null, Request $request)
    {
        $this->slug = $slug3 ?? $slug2 ?? $slug1;
        $categories = (array) DB::table('categories')
        ->join('category_translations', 'categories.id', '=', 'category_translations.category_id')
        ->where('slug', $this->slug)
        ->first();
        var_dump($categories);
        die;  
        $image = DB::table('entity_files')
                    ->join('files','entity_files.file_id','=','files.id')
                    ->where('entity_files.entity_id','=',$categories["id"])
                    ->where('entity_files.entity_type','Modules\Category\Entities\Category')
                    ->where('entity_files.zone','=','feature_image')
                    ->get();
                dd($image);
        // echo 'a';die;
        // if(file_exists(public_path().'/category_all.json')){

        if($request->has('cache')){

            return response(file_get_contents(public_path().'/category_all.json'), 200)->header('Content-Type', 'application/json');
        }else{
            $this->slug = $slug3 ?? $slug2 ?? $slug1;

            $this->request = $this->reformatRequest(Request::capture()->all());

            $response = array();

            $response['data'] = array();

            if ($this->slug != "all"){

                $categories = (array) DB::table('categories')
                    ->join('category_translations', 'categories.id', '=', 'category_translations.category_id')
                    ->where('slug', $this->slug)
                    ->first();
                $image = DB::table('entity_files')
                    ->join('files','entity_files.file_id','=','files.id')
                    ->where('entity_files.entity_id','=',$categories["id"])
                    ->where('entity_files.entity_type','Modules\Category\Entities\Category')
                    ->where('entity_files.zone','=','feature_image')
                    ->get();
                dd($image);
                $type = $categories["name"];

                $this->catId = $categories["id"];

            }
            else {

                $categories = array_values( (array) DB::table('categories')
                    ->join('category_translations', 'categories.id', '=', 'category_translations.category_id')
                    ->get());

                $type = $this->slug;

                $this->catId = 1;

            }

            if ($this->request['page']){
                $current_page = $this->request['page'];
            }
            else {
                $current_page = 1;
            }

            $response['data']['page'] = array(
                "name" => "page_info",
                "category_current" => $type,
                "category_slug" => $this->slug,
                "current_page" => $current_page,
                "last_page" => $this->getTotalGroupNumber([]),
                "next_page_url" => $categories,
                "privious_page_url" => "/catagories/women?sort=latest&page=1",
                "api_link_products" => "/get-products/women?sort=latest&page=3",
                "title" => $type
            );

            // Create file json
            $file  = public_path('category_all.json');
            File::put(
                $file,
                json_encode($response),
	        );

            return response()->json($response, 200);
        }
    }

    private function getTotalGroupNumber($pinGroups = [])
    {
        if(count($pinGroups)>0){
//            dd($this->buildSearchQuery()->get());
            return intval(floor(floatval(count($this->buildSearchQuery()->get())) / 60.0));
        }

        return intval(floor(floatval(count($this->buildSearchQuery()->get())) / 60.0));
    }

    private function buildSearchQuery($type='')
    {
        $request = $this->request;

        $attributes = $this->formatAttributes($request['attribute']);
//         dd($attributes);
        if($type != ''){
            $builder = $this->buildFromToPriceQueryBuilderSale($attributes);
        }else{
            $builder = $this->buildFromToPriceQueryBuilder($attributes);
        }

//        dd($builder->get());

        if($type != 'sale'){
            if ($this->slug != "all") {
                if ($this->catId) {
                    $builder->join('group_categories AS gc', 'gc.group_id', '=', 'groups.id')
                        ->where('gc.category_id', $this->catId);
                }
            }
            else{
                $builder->join('group_categories AS gc', 'gc.group_id', '=', 'groups.id')->groupBy('groups.id');
            }
        }
//        dd($builder->get()[0]);
        $current_route = array_key_first(Application::getInstance()->router->namedRoutes);

//        if($type != 'sale' && \Route::current()->getName() != 'products.categorysale'){

        if($type != 'sale' && $current_route != 'products.categorysale'){

            switch ($request['sort']) {
                case 'priceLowToHigh':
                    $builder->orderBy('lowest_price', 'ASC');
                    break;

                case 'priceHighToLow':
                    $builder->orderBy('highest_price', 'DESC');
                    break;

                default:
                    $builder->orderBy('id', 'DESC');
            }
        }

        if ($request['fromPrice']) {
            $builder->where('lowest_price', '>=', $request['fromPrice']);
        }

        if ($request['toPrice']) {
            $builder->where('highest_price', '<=', $request['toPrice']);
        }

        return $builder;
    }

    private function formatAttributes($attributes)
    {
        $formattedAttributes = [];
        foreach ($attributes AS $attrId => $attrValues) {
            if ($attrId == self::MA_GOC) {
                $formattedAttributes[$attrId] = $attrValues;
            } else {
                $formattedAttributes[$attrId] = $this->getAttributeValuesFromAttributeGroup($attrId, $attrValues);
            }
        }

        return $formattedAttributes;
    }

    function array_get($array, $key, $default = null)
    {
        return Arr::get($array, $key, $default);
    }


    function buildFromToPriceQueryBuilder($attributes = [],$type = 'normal') {
        $lowestPrice = DB::table('products AS p')
            ->select('p.group_id',
                DB::raw('MIN(p.selling_price) as lowest_price'),
                DB::raw('MAX(p.selling_price) as highest_price'),
                DB::raw('MAX(p.updated_at) as latest'),
                DB::raw('MIN(CONCAT(id, "___", sku)) as idSku2'),
                DB::raw('MIN(sku) as idSkuS'),
                DB::raw('p.qty as stock'),
                DB::raw('p.id as product_id')
            )
            ->whereIn('p.is_active', [0, 1])
            ->groupBy('p.group_id');

        // sales, new, wishlist ()
        // $new = DB::table('products')->select('group_id')->where('new', 1)->groupBy('group_id');
        // $sales = DB::table('products')->select('group_id')->where('new', 1)->groupBy('group_id');
        // select group_id from products WHERE special_price > 0 AND special_price < selling_price AND special_price_end > NOW() GROUP BY group_id



        $featureProduct = DB::table('products')
            ->select('group_id',
                DB::raw('MIN(CONCAT(id, "___", sku)) as idSku'),
                'selling_price',
                'price'
            )
            ->where('is_active', 1)
            ->groupBy('group_id');

        if(count($attributes)>0){
            foreach ($attributes AS $attrId => $attrValueIds) {
                $tableName = 'pav_' . $attrId;
                $lowestPrice->join("product_attribute_values AS {$tableName}", "{$tableName}.product_id", '=', 'p.id')
                    ->where("{$tableName}.attribute_id", $attrId)
                    ->whereIn("{$tableName}.attribute_value_id", $attrValueIds);
            }
        }

        return Group::with('products')->joinSub($lowestPrice, 'product', function ($join) {
            $join->on('groups.id', '=', 'product.group_id');
        })->joinSub($featureProduct, 'featureProduct', function ($join) {
            $join->on('groups.id', '=', 'featureProduct.group_id');
        });
    }


    function buildFromToPriceQueryBuilderSale($attributes = []){

        $lowestPrice = DB::table('products AS p')
            ->select('p.group_id',
                DB::raw('MIN(p.selling_price) as lowest_price'),
                DB::raw('MAX(p.selling_price) as highest_price'),
                DB::raw('MAX(p.updated_at) as latest'),
                DB::raw('MIN(CONCAT(id, "___", sku)) as idSku2'),
                DB::raw('MIN(sku) as idSkuS'),
                DB::raw('p.qty as stock'),
                DB::raw('p.id as product_id')
            )
            ->whereIn('p.is_active', [0, 1])
            ->groupBy('p.group_id');

        // sales, new, wishlist ()
        // $new = DB::table('products')->select('group_id')->where('new', 1)->groupBy('group_id');
        // $sales = DB::table('products')->select('group_id')->where('new', 1)->groupBy('group_id');
        // select group_id from products WHERE special_price > 0 AND special_price < selling_price AND special_price_end > NOW() GROUP BY group_id


        // IF FOR GET SALE
        $featureProduct = DB::table('products')
            ->select('group_id',
                DB::raw('MIN(CONCAT(id, "___", sku)) as idSku'),
                'selling_price',
                'price'
            )
            ->where('is_active', 1)
            ->where('selling_price','<>', DB::raw('products.price'))
            ->where('special_price_start','<=',Carbon::now())->where('special_price_end','>=',Carbon::now())
            ->groupBy('group_id');


        foreach ($attributes AS $attrId => $attrValueIds) {
            $tableName = 'pav_' . $attrId;
            $lowestPrice->join("product_attribute_values AS {$tableName}", "{$tableName}.product_id", '=', 'p.id')
                ->where("{$tableName}.attribute_id", $attrId)
                ->whereIn("{$tableName}.attribute_value_id", $attrValueIds);
        }

        return Group::with('products')->joinSub($lowestPrice, 'product', function ($join) {
            $join->on('groups.id', '=', 'product.group_id');
        })->joinSub($featureProduct, 'featureProduct', function ($join) {
            $join->on('groups.id', '=', 'featureProduct.group_id');
        })->with(array(
            'categories' => function ($query){
                $query->orderBy('position','ASC');
            }
        ));
    }

}
