<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Cache\Simple\RedisCache;
use Illuminate\Support\Facades\Redis;
use EasyWeChat\Factory;

class WeixinApiModel
{

    /**
     * 微信小程序配置
     * @return
     */
    public static function miniProgram(){
        $weixinConfig = Config::get("weixin");
        $config = [
            'app_id' => $weixinConfig["program_appid"],
            'secret' => $weixinConfig["programe_appsecret"],
            'token' => $weixinConfig["programe_token"],
            'aes_key' => $weixinConfig["programe_aes_key"],
            'response_type' => 'array',

            'log' => [
                'level' => 'error',
                'file' => storage_path() . '/logs/wechat.log',
            ],
        ];

        $app = Factory::miniProgram($config);

        //设定缓存
        $predis = $redis = Redis::connection();
        $cache = new RedisCache($predis);
        $app['cache'] = $cache;

        return $app;
    }

     

}