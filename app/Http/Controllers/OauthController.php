<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\WeixinApiModel;
use App\Libs\Common;
use App\Models\UserModel;
use App\Libs\Wxbizdatacrypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;



class OauthController extends Controller
{

    /**
     * 微信登陆
     * @param Request $request
     */
    public function login(Request $request)
    {
        $input = $request->all();
        $code = isset($input['code']) ? (string)$input['code'] : '';

        //验证参数
        $srcParams = array("code");
        if(!Common::validationParams($input, $srcParams)){
            Common::outputJson(0,20010);
        }

        //判断调用接口是否成功
        $app = WeixinApiModel::miniProgram();
        $data = $app->auth->session($code);
        $openid = $data['openid'];

        $info = UserModel::isExistOpenid($openid);
        $error = 0;
        $status = 1;
        if(!$info){
            $status = 0;
            $error = 30001;
        }else{
            $mobile = $info['mobile'];
            $jobNo = $info['job_no'];
            if(empty($mobile) || empty($jobNo)){ //绑定手机号码
                $error = 30002;
                $status = 0;
            }
        }

        //生成登陆_TOKEN
        $timestamp = time();
        $privateKeyString = $this->getPrivateKey();
        $originalData = $this->createTokenString($openid, $timestamp);
        $token = Common::RsaEncrypt($originalData, $privateKeyString);
        $this->tokenWriteCache($openid, $timestamp);
        $data['token'] = $token;
        $data['uid'] = $openid;

        //用户会话密
        Cache::store('redis')->put('code_' . $openid, $code, 86400);
        Cache::store('redis')->put('session_' . $openid, $data['session_key'], 280);
        unset($data['session_key']);

        Common::outputJson($status, $error,$data);
    }


}

