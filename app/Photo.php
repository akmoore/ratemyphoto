<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'preferred', 'image_sm', 'image_md',
        'image_lg', 'image_org', 'image_name', 'image_thumb'
    ];

    protected $appends = ['name', 'alt'];

    //Attributes (Dynamic Fields)
    public function getNameAttribute(){
        return $this->image_md;
    }

    public function getAltAttribute(){
        return $this->image_name;
    }

    //Relationships
    /**
     * Associate user to photos
     *
     * @return object
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * Associate downloads to photo
     *
     * @return object
     */
    public function downloads(){
        return $this->hasMany(Download::class);
    }
}
