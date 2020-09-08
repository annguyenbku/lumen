<?php

namespace App;

// use Modules\Filter\Eloquent\HasFilter;
// use Modules\Group\Entities\Group;
// use Modules\Meta\Eloquent\HasMetaData;
// use Modules\Support\Money;
// use Modules\Media\Entities\File;
// use Modules\Tax\Entities\TaxClass;
// use Modules\Option\Entities\Option;
// use Modules\Location\Entities\Location;
// use Modules\Review\Entities\Review;
// use Modules\Support\Eloquent\Model;
// use Modules\Media\Eloquent\HasMedia;
// use Modules\Support\Search\Searchable;
// use Illuminate\Support\Facades\Request;
// use Modules\Category\Entities\Category;
// use Modules\Product\Admin\ProductTable;
// use Modules\Product\ValueObjects\Price;
// use Modules\Support\Eloquent\Sluggable;
// use Modules\Attribute\Entities\Attribute;
// use Modules\Support\Eloquent\Translatable;
// use Illuminate\Database\Eloquent\SoftDeletes;
// use Modules\Attribute\Entities\ProductAttribute;
// use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // use Translatable, Searchable, HasMedia, HasMetaData, SoftDeletes;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    protected $translatedAttributes = [];

    /**
     * The attribute that will be slugged.
     *
     * @var string
     */
    protected $slugAttribute = '';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
}
