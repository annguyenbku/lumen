<?php

namespace Modules\Block\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;

class BlockValue extends Model
{
    use Translatable;
    public $timestamps = false;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'position',
        'key',
        'type',
    ];

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
    public $translatedAttributes = [
        'title',
    ];
    public static function boot()
    {
        parent::boot();
        static::creating(function ($entity) {
            $entity->setKey();
        });
    }
    public function setKeyAttribute($value)
    {
        $value = str_replace(
            ' ',
            '',
            $value
        );
        $value = trim(
            preg_replace(
                '/\s+/',
                ' ',
                $value
            )
        );
        $this->attributes['key'] = $value;
    }
    public function setKey()
    {
        $key = $this->getAttribute('key');
        $position = $this->getAttribute('position');
        $block_id = $this->getAttribute('block_id');

        $check = $this->where('key',$key)
            ->where('block_id',$block_id)
            ->where('position','<>',$position)
            ->withoutGlobalScope('active')
            ->exists();
        if($check){
            $key .= '-' . str_random(8);
        }

        $this->attributes['key'] = $key;
    }
}
