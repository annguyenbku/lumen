<?php

namespace App;

// use Illuminate\Database\Eloquent\SoftDeletes;
// use Modules\Category\Entities\Category;
// use Modules\Filter\Eloquent\HasFilter;
// use Modules\Group\Admin\GroupTable;
// use Modules\Media\Eloquent\HasMedia;
// use Modules\Product\Entities\Product;
// use Modules\Support\Eloquent\Model;
// use Modules\Support\Eloquent\Sluggable;
// use Modules\Support\Eloquent\Translatable;
// use Modules\Support\Search\Searchable;
// use Illuminate\Support\Facades\Cache;
// use Modules\Page\Entities\Page;
// use Modules\Location\Entities\Location;
// use Modules\Brand\Entities\Brand;
// use Modules\Review\Entities\Review;
// use Modules\Ward\Entities\Ward;
// use Modules\Street\Entities\Street;
// use Modules\Province\Entities\Province;
// use Modules\Store\Entities\Store;
use App\Product;
use App\Category;

class Group extends Model
{
    // use Translatable, Searchable, Sluggable, HasMedia, SoftDeletes;

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
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = [];

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

    public function categories ()
    {
        return $this->belongsToMany(
            Category::class,
            'group_categories'
        );
    }

    public function products ()
    {
        return $this->hasMany(
            Product::class,
            'group_id',
            'id'
        );
    }

}
