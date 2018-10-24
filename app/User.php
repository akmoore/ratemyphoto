<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
        'has_logged_in', 'has_selected_preferred_image', 'slug',
        'role', 'email_verify_token_exp', 'email_verify_token'
    ];

    protected $dates = [
        'email_verify_token_exp'
    ];

    /**
     * Additional fields that shall be return with record
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'name' => $this->full_name,
            'id' => $this->id,
            'has_password' => (bool) $this->password
            // 'exp_time' => \Carbon\Carbon::now('America/Chicago')->addMinutes(7)
        ];
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'fullname'
            ]
        ];
    }

    /**
     * Create fullname from frist_name and last_name
     *
     * @return string
     */
    public function getFullnameAttribute(){
        return $this->first_name . ' ' . $this->last_name;
    }

    //Relationships
    /**
     * Associate photos with user
     *
     * @return object
     */
    public function photos(){
        return $this->hasMany(Photo::class)->orderBy('image_name');
    }
}
