<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    //
       protected $fillable = [
        'category_id',
        'asset_name',
        'asset_code',
        'status',
                'department_id',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

        public function department()
    {
        return $this->belongsTo(Department::class);
    }


}
