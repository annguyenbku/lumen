<?php

namespace App;

use Modules\Support\Eloquent\TranslationModel;

class MenuItemTranslation extends TranslationModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
