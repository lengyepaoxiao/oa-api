<?php

return [

    //微信公众号

    "weixin_appid"	    => "",//"wx09a87902a7439541",		//公众号APPID

    "weixin_appsecret"	=> "",//"083247ba3335919d883e05156ae9353a",//AppSecret

    "weixin_token"		=> "",						// Token

    "weixin_aes_key"    => "",			// EncodingAESKey，安全模式下请一定

    'cert_path'          => storage_path() . '/rsa/apiclient_cert.pem',

    'key_path'           => storage_path() . '/rsa/apiclient_key.pem',




    //微信小程序

    "merchant_id"	    => "",			//微信支付商户号

    "merchant_key"	    => "", //key

    "program_appid"      => '',

    "programe_appsecret" => '',

    "programe_token"    => '',

    "programe_aes_key"  => '',

   ];
