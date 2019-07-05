<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(@OA\Xml(name="Country"))
 */
class Country extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'country';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'parent_id'];

    public function child(){
        return $this->hasMany('App\Country','parent_id');
    }

    public function user()
    {
        return $this->belongsToMany('App\User');
    }


}