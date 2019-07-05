<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Code extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sms_code';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'sms_code'];

    public function user() {
        return $this->hasOne('App\User','id','user_id');
    }
}