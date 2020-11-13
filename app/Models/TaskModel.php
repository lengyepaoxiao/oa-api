<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class TaskModel extends Model
{
    protected $table = "task";

    protected $fillable = [];

    public static function pageLists($conditionArray = array(),$orderbuy,$page,$pagesize){

        $result = (new static)->select();//有条件
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

    //列表
    public static function lists($uidArray){
        $result = (new static)->select(array('id','username','img','created_at'))->whereIn("id",$uidArray)
            ->where("isdel","=",0)
            ->get();
        return $result;
    }

    //插入一条数据
    static public function inserts($data){
        return (new static) -> insert($data);
    }

    //根据公司id获取职位数据
    static public function getInfos($company_id){
        $obj = (new static) -> where('company_id', $company_id) ->where('isdel', 0);
        return $obj -> get();
    }

    //根据职位id获取职位数据
    static public function getInfo($job_id){
        return (new static) -> where('id', $job_id) -> where('isdel', 0) -> first();
    }

    //获取除此职位id的其他职位
    static public function otherInfos($company_id, $job_id){
        return (new static) -> where('isdel', 0) -> where('company_id', $company_id) -> where('id', '!=', $job_id) -> get();
    }

    //逻辑删除职位信息
    static public function del($company_id, $id){
        $num = (new static) -> where('company_id', $company_id) ->where('isdel', 0) -> count();
        if($num > 1){
            return (new static) -> where('id', $id) -> update(['isdel'=> 1]);
        }else{
            return 0;
        }
    }

    //更新职位信息
    static public function updates($company_id, $job_id, $data){
        return (new static) ->where('id', $job_id) -> where('company_id', $company_id) ->where('isdel', 0) ->  update($data);
    }
}