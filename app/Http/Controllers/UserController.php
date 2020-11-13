<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Libs\Common;
use App\Models\UserModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    /**
     * 绑定手机号码
     * @param Request $request
     */
    public function saveUserInfo(Request $request)
    {

        $input = $request->all();
        $uid = isset($input['uid']) ? (string)$input['uid'] : 0;
        $token = !empty($input['token']) ? (string)$input['token'] : '';
        $jobNo = !empty($input['job_no']) ? (string)$input['job_no'] : '';
        $mobile = !empty($input['mobile']) ? (string)$input['mobile'] : '';

        //是否登陆
        if(!$this->VerificationToken($uid,$token)){
            Common::outputJson(0,20011);
        }

        //更新基础信息表
        $time = date('Y-m-d H:i:s');
        $param = array(
            'ip' => Common::getIP(),
            "openid"=>$uid,
            'updated_at' => $time,
        );
        $ret = UserModel::where('mobile', '=', $mobile)->where('job_no', '=', $jobNo)->update($param);
        if(!$ret){
            Common::outputJson(0,0);
        }

        Common::outputJson(1,0);
    }

    /**
     * 获取用户信息
     * @param Request $request
     */
    public function getUserInfo(Request $request){

        $input = $request->all();
        $uid = !empty($input['uid']) ? (string)$input['uid'] : '';
        $token = isset($input['token']) ? (string)$input['token'] : '';

        //是否登陆
        if(!$this->VerificationToken($uid,$token)){
            Common::outputJson(0,20011);
        }

        //获取当前信息
        $info = UserModel::getInfo($uid);

        Common::outputJson(1,0,$info);
    }

    /**
     * 获取员工列表
     * @param Request $request
     */
    public function getTaskList(Request $request){

        $input = $request->all();
        $uid = !empty($input['uid']) ? (string)$input['uid'] : '';
        $token = isset($input['token']) ? (string)$input['token'] : '';

        //是否登陆
        if(!$this->VerificationToken($uid,$token)){
            Common::outputJson(0,20011);
        }

        //获取当前信息
        $result = UserModel::getTaskList();
        $lists = $result->toArray();

        Common::outputJson(1,0,$lists);
    }


}

