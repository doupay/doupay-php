<?php

namespace doupay\doupayphp;
use doupay\doupayphp\Utils\Lib;
use doupay\doupayphp\Api\Paymentinfo;

class Constants
{
    public static $openSysLog = false;
    public static $basrUrl = "http://pay.apipay.one";
    public static $language = 'en_US';
    public static $Version = 'v1.0';

    /**
     *
     * @param appId appid
     * @param secret secret
     * @param publicKey publicKey
     * @param privateKey privateKey
     * @param expireTime 过期时间,以秒为单位,eg:30分钟有效期,就传"60*30"
     */
    public static function init($appid, $secret, $publicKey, $privateKey, $expireTime){
		if(empty($appid)){
            return Lib::result(401, 'appid is null.');
        }
        if(empty($secret)){
            return Lib::result(402, 'secret is null.');
        }
        if(empty($publicKey)){
            return Lib::result(412, 'publicKey is null.');
        }
        if(empty($privateKey)){
            return Lib::result(413, 'privateKey is null.');
        }
        if(empty($expireTime)){
            return Lib::result(414, 'expireTime is null.');
        }
        if($expireTime <=0 ){
            return Lib::result(415, 'expireTime error.');
        }
        return new Paymentinfo(self::$basrUrl, self::$language, self::$Version, $appid, $secret, $publicKey, $privateKey, $expireTime);
    }


}