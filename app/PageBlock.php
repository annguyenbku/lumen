<?php

namespace App;

// use Modules\Block\Entities\Block;
// use Modules\Support\Eloquent\Model;
// use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\Model;



class PageBlock extends Model
{
    // use Translatable;
    
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    protected $translatedAttributes = [
        // 'title',
        // 'content'
    ];

    public $timestamps = false;
    protected $fillable = [
        // 'position',
        // 'block_id',
    ];

    protected static function boot ()
    {
        // parent::boot();
    }

    public function block(){
        return $this->hasOne(Block::class,'id','block_id');
    }
}
