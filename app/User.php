<?php
namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(@OA\Xml(name="User"))
 */
class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected  $table = "users";
    public $rememberToken;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image','name', 'username','gender','date_of_birth','education', 'experience', 'category','subcategory','wished_country', 'wished_city','salary_min','email','password','phone'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function category()
    {
        return $this->belongsToMany('App\Category' ,'user_category', 'user_id', 'category_id');
    }

    public function country() {
        return $this->belongsToMany('App\Country', 'user_country', 'user_id', 'country_id');
    }



}
