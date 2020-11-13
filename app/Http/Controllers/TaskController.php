<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Libs\Common;
use App\Models\TaskModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Config;


class TaskController extends Controller
{

    /**
     *  获取任务列表
     * @param Request $request
     */
    public function getLists(Request $request){

        $input = $request->all();
        $status = isset($input['status']) ? (int)$input['status'] : 0;
        $page = isset($input['page']) ? (int)$input['page'] : 1;
        $pagesize = isset($input['pagesize']) ? (int)$input['pagesize'] : 20;
        $uid = !empty($input['uid']) ? (string)$input['uid'] : '';
        $token = !empty($input['token']) ? (string)$input['token'] : '';

        //是否登陆
        if(!$this->VerificationToken($uid,$token)){
            Common::outputJson(0,20011);
        }

        $userInfo = UserModel::getInfo($uid);
        $uid = $userInfo['id'];

        //查询
        $result = TaskModel::pageLists(array("employee"=>"=" . $uid,"status"=>'='.$status), 'created_at', $page, $pagesize);
        $lists = $result->toArray();

        Common::outputJson(1,0,$lists);

    }


    /**
     *  获得任务信息
     * @param Request $request
     */
    public function getInfo(Request $request){

        $input = $request -> all();
        $taskId = isset($input['task_id']) ? (int)$input['task_id'] : 0;
        $uid = !empty($input['uid']) ? (string)$input['uid'] : '';
        $token = !empty($input['token']) ? (string)$input['token'] : '';

        //是否登陆
        if(!$this->VerificationToken($uid,$token)){
            Common::outputJson(0,20011);
        }

        $info = TaskModel::getinfo($taskId);
        ///$userInfo = UserModel::getUserInfo($info['employee']);
        //$info['employee_username'] = $info['username'];
        $info['show_img'] = 'https://api.brgrand.cn/uploads/task/' . $info['img'];

        Common::outputJson(1,0,$info);
    }


    /**
     * 上传任务图片
     * @param Request $request
     */
    public function uploadTaskImg(Request $request)
    {
        $input = $request->all();
        $fileObj = $_FILES['media'];
        $taskId = isset($input['task_id']) ? (int)$input['task_id'] : 0;
        $uid = !empty($input['uid']) ? (string)$input['uid'] : '';
        $token = isset($input['token']) ? (string)$input['token'] : '';

        if($fileObj){
            $input['media'] = 1;
        }

        //验证参数
        $srcParams = array("media","uid","task_id");
        if (!Common::validationParams($input, $srcParams)) {
            Common::outputJson(0, 200101);
        }

        //是否登陆
        if(!$this->VerificationToken($uid,$token)){
            Common::outputJson(0,20011);
        }

        //验证图片扩展名
        $file = strtolower($fileObj['name']);
        $extName = substr($file,strrpos($file,'.')+1);
        if(!in_array($extName,config('app.picture.ext'))){
            Common::outputJson(0, 20012);
        }

        //验证图片类型
        if(!in_array($fileObj['type'],Config::get('app.picture.type'))){
            Common::outputJson(0, 20013);
        }

        //验证图片大小
        if(strtolower($fileObj['size']) > Config::get('app.picture.size')){
            Common::outputJson(0, 20014);
        }

        //业务处理
        try {
            $savePath = storage_path() . '/uploads/task/';
            $fileName = $uid . "_" . time() . "." . $extName;
            move_uploaded_file($fileObj['tmp_name'],$savePath . $fileName);
            TaskModel::where('id', '=', $taskId)->update(array('img'=>$fileName,'status'=>1));
            Common::outputJson(1, 0);

        } catch (Exception $e) {
            Common::outputJson(0, 20001);

        }

    }

    /**
     *  转单
     * @param Request $request
     */
    public function changeUserTask(Request $request)
    {

        $input = $request->all();
        $taskId = isset($input['task_id']) ? (int)$input['task_id'] : 0;
        $taskUid = isset($input['task_uid']) ? (int)$input['task_uid'] : 0;
        $uid = !empty($input['uid']) ? (string)$input['uid'] : '';
        $token = !empty($input['token']) ? (string)$input['token'] : '';

        //是否登陆
        if (!$this->VerificationToken($uid, $token)) {
            Common::outputJson(0, 20011);
        }

        $userData = UserModel::getById($taskUid);
        $data = [
            'employee' => $taskUid,
            'employee_name' => trim($userData['username']),
            'job_no'=>$userData['job_no'],
            'updated_at' => date('Y-m-d H:i:s', time(0))
        ];
        TaskModel::where('id', '=', $taskId)->update($data);

        Common::outputJson(1, 0);
    }

}