<?php
namespace App\Twilio\Messages;

use App\Code;
use App\User;
use \Kevupton\Twilavel\Messages\Message;


class VerifyPhone extends Message {

    const FROM = '+12028314884';

    /** @var string custom verification code */
    private $code;

    public function __construct($id,$phone)
    {
        // use mobile other use user mobile
        parent::__construct($phone, self::FROM);

        // get a random code
        $this->code =str_random(4);
        Code::create(['user_id' => $id,'sms_code'=>$this->code]);
    }

    public function getBody()
    {
        return 'Your verification code: ' . $this->code;
    }
}