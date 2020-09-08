<?php

namespace App;

// use Modules\Admin\Ui\AdminTable;
// use Modules\Support\Eloquent\Model;
// use Modules\Meta\Eloquent\HasMetaData;
// use Modules\Support\Eloquent\Sluggable;
// use Modules\Support\Eloquent\Translatable;
// use Modules\Media\Entities\File;
// use Modules\Media\Eloquent\HasMedia;
// use Modules\Category\Entities\Category;
// use Modules\Group\Entities\Group;

use App\Block;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    // use Translatable, Sluggable, HasMetaData, HasMedia;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
   
    public function blocks(){
        $this->hasMany(PageBlock::class);
        dd($this->hasMany(PageBlock::class));
        return $this->hasMany(PageBlock::class)->orderBy('position');
    }
    // End Block

}
