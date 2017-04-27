<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public function category()
    {
    	return $this->belongsTo('App\Category', 'category_id');
    }

    public function company()
    {
    	return $this->belongsTo('App\Company', 'company_id');
    }

    public function options()
    {
        return $this->belongsToMany('App\Option', 'product_option', 'product_id', 'option_id');
    }
}
