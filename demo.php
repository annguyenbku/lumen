<!--branding-section-area start-->
<div class="branding-section-area" style='background:rgba(0, 0, 0, 0) url("<?php if(isset($branding_bgs)) {$branding_bg = (array) $branding_bgs; echo $branding_bg[0]->file;} ?>") no-repeat scroll center center / cover !important'>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="active-slider pos-rltv carosule-pagi cp-line pagi-02">
                            <?php
                                $new_collection = \Modules\Collection\Entities\Collection::where('id',$new_collections[0])->first();
                                if($new_collection){
                                $condition = json_decode($new_collection->conditions,true);
                                $products = productCollections($condition);
                                $i = 1;
                                foreach($products as $key=>$product){
                            ?>
                            <?php
                                $group = $product;
                                if(request()->has('attribute')){
                                $sku = getSkuFromGroup2($group);
                                }else{
                                $sku = getSkuFromGroup($group);
                                }
                            ?>
                                    <?php
                                        
                                        $product_id = explode("_", $group->idSku2);
                                        //dd($product_id[0]);
                                    ?>
                            <div class="single-slider">
                                <div class="row">
                                    <div class="col-xl-7 col-lg-6 col-md-6">
                                        <div class="brand-img text-center">
                                            <img src="https://product.hstatic.net/1000284478/product/assorted_mxgchero_2_63d3a570329144f086eced15fc23b839_1024x1024.jpg" alt="">
                                        </div>
                                    </div>
                                    <div class="col-xl-5 col-lg-6 col-md-6">
                                        <div class="brand-content ptb-55">
                                            <div class="brand-text color-lightgrey">
                                                <h6>{{trans('storefront::storefront.index.new_products')}}</h6>
                                                <h2 class="uppercase montserrat">{{ $group->name }}</h2>
                                                <h3 class="montserrat">@if($group->lowest_price != $group->highest_price){{ trans('group::group-categories.from') }} 
                                    {{ formatMoney($group->lowest_price) }} @endif @if($group->lowest_price == $group->highest_price)-
                                        {{ formatMoney($group->highest_price) }}@endif</h3>

                                                
                                                <div class="social-icon-wraper mt-35">
                                                    <div class="social-icon">
                                                        <ul>
															<?php
																$product_id = explode("_", $group->idSku2);
																//dd($product_id[0]);
															?>
															<li><a href="#" data-id="{{$product_id[0]}}" 
																data-name="{{$group->name}}" 
																data-price="{{$group->selling_price}}" data-tooltip="Add To Cart" class="add_to_cart_ajax add-cart add-cart-text" data-placement="left" tabindex="0"><i class="fa fa-cart-plus"></i></i></a></li>
															<li><a id="detail_wishlist" data-id="{{$product_id[0]}}" data-type="detail" href="#" data-tooltip="Wishlist" class="w-list" tabindex="0"><i class="far fa-heart fa-lg"></i></a></li>
															<li><a href="#" data-tooltip="Compare" class="cpare" tabindex="0"><i class="fas fa-sync-alt fa-lg"></i></a></li>
															<li><a href="#" data-tooltip="Quick View" class="q-view" data-toggle="modal" data-target=".modal" tabindex="0"><i class="fas fa-eye fa-lg"></i></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="brand-timer shadow-box-2 mt-50">
                                                <div class="timer-wraper text-center">
                                                    <div class="timer">
                                                        <div data-countdown="2015/02/01"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php
                        }
                    }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--branding-section-area end-->