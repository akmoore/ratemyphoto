<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    /**
     * These are the fields that are mass assignable
     *
     * @var array
     */
    protected $fillable = ['photo_id'];

    //Relationships
    /**
     * Associate a photo to an individual download
     *
     * @return object
     */
    public function photo(){
        return $this->belongsTo(Photo::class);
    }
}
