<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Libs\Common;

class Controller extends BaseController
{

    public function _initialize(){

    }

    /**
     * 验证TOKEN
     * @param int $uid
     * @param string $token
     * @return int
     */
    protected function VerificationToken($uid,$token){

        //return 1; //TEST

        $cacheKey = "token:" . $uid;
        if(!Cache::store('redis')->has($cacheKey)){
            return 0; //不存在
        }

        //RSA解密
        $encryptData = $token;
        $publicKeyString = $this->getpublicKey();
        $token = Common::RsaDecrypt($encryptData, $publicKeyString);
        if(empty($token)){
            return 0;
        }
        $tokenData = explode(":", $token);
        $serverData = explode(":",Cache::store('redis')->get($cacheKey));
        if(empty($serverData)){
            return 0;
        }

        //是否同一用户
        if(($serverData[0] != $tokenData[0])){
            return 0;
        }

        //判断时间是否一致TOKEN是否过期限
        if($serverData[1] != $tokenData[1]){
            return 0;
        }
        $nowTime = time();
        $hour = floor((strtotime($nowTime) - strtotime($serverData[1])) % 86400 / 3600);
        if($hour >= 24){  //大于小时间就过期限
            return 0;
        }
        return 1;
    }

    /**
     * 公匙
     * @return bool|string
     */
    public function getPublicKey(){

        $publicKey = file_get_contents(storage_path() . '/rsa/rsa_public_key.pem');
        return $publicKey;
    }

    /**
     * 私匙
     * @return bool|string
     */
    public function getPrivateKey(){
        $privateKey =  file_get_contents(storage_path() . '/rsa/rsa_private_key.pem');
        return $privateKey;
    }

    /**
     * 生成Token串
     * @param $uid
     * @param $timestamp
     * @return string
     */
    protected function createTokenString($uid,$timestamp){

        $originalData = $uid.":".$timestamp;
        return $originalData;
    }

    /**
     * 写入缓存
     * @param $uid
     * @param $timestamp
     */
    protected function tokenWriteCache($uid,$timestamp){
        $cacheKey = "token:".$uid;
        $cacheValue = $uid.":".$timestamp;
        Cache::store('redis')->put($cacheKey, $cacheValue, 2592000);
    }

    //获取当前Toekn
    protected function getToken($uid){
        $cacheKey = "token:" . $uid;
        return Cache::store('redis')->has($cacheKey);
    }
	
	protected function getAppKey($appid){

        $result = $this->getAppPlatformConfig($appid);
        if(!$result){
            return false;
        }
        $appKey = $result["appkey"];
        return $appKey;
    }
	
	protected function getInfoByAppid($appid){
        $result = $this->getAppPlatformConfig($appid);
        if(!$result){
            return false;
        }
		return $result;
	}

	protected function getAppPlatformConfig($appid){
        
        $configArray = Config::get("app.app_platform");
        $pconfig = array();
        foreach ($configArray as $key=>$val){
            if($val['appid'] == $appid){
                $pconfig = $val;
                break;
            }
        }
        return $pconfig;

    }


}
