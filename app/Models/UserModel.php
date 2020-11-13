<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



class UserModel extends Model
{
    
    protected $table = "user";

    protected $fillable = ["id","openid","mobile","email","password","username","img","sex","province","city","district","status","ip","refresh_token","updated_at","created_at"];

    public static function pageLists($conditionArray = array(),$orderbuy,$page,$pagesize){

        $result = (new static)->select();
        //有条件
        if($conditionArray){
            foreach($conditionArray as $key=>$val){
                $conditionArrayTemp[] = $key . $val;
            }
            $conditionString = implode(' AND ',$conditionArrayTemp);
            $result = $result->whereRaw($conditionString);
        }

        $offset = ($page - 1) * $pagesize;
        $result = $result->where('isdel','=',0)->orderBy($orderbuy,"desc")

            ->skip($offset)->take($pagesize)
            ->get();
        return $result;
    }

    public static function isExistOpenid($openid){
        $result = (new static)->select(array('id','job_no','mobile'))
            ->where("openid","=",$openid)
            ->where("isdel","=",0)
            ->first();
        return $result;
    }


    public static function getInfo($openid){
        $result = (new static)->select()
            ->where("openid","=",$openid)
            ->where("isdel","=",0)
            ->first();
        return $result;
    }

    public static function getUserInfo($uid){
        $result = (new static)->select()
            ->where("id","=",$uid)
            ->where("isdel","=",0)
            ->first();
        return $result;
    }

    //获取所有任务列表
    public static function getTaskList($isdel = 0){
        return (new static)->select(['id', 'username'])->where('isdel', $isdel) -> get();
    }

    public static function getById($id){
        return (new static)->where('id', $id)->where('isdel', 0)->first();
    }

}
