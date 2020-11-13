<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', '347f59854$@f4023c11sd@8a6d98b66#'),

    'cipher' => 'AES-256-CBC',

	'debug' =>  env('APP_DEBUG', true),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

	'timezone' => 'PRC',

    'locale' => env('APP_LOCALE', 'en'),
    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    //登陆接口密KEY
    'login_sign'=> '2ea196804f9f9ca6bb4f1bd924f09cdb',

    //加密KEY
    'grcode_key_encrypt' => '3q3@5#$$@f4wqc112ed%8&6d==64',

    //平台版本配置
	"app_platform"	=>	[

        //小程序
        [
            "appid"		=> 1001,
            "appkey"		=>"Q!612@h",
            "privatekey"	=>"88_rsa_private_key.pem",
            "publickey"		=>"88_rsa_public_key.pem",
            "yfnotify"	=>"",
        ],

    ],

    'picture'=>[
        "size"=>3145728000,
        "type"=>['image/gif','image/png','image/jpeg','image/jpg','image/bmp','image/x-png'],
        "ext"=>['gif','jpg','png','bmp','jpeg']
    ],

];
