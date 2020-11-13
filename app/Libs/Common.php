<?php

namespace App\Libs;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Config;
use JPush\Client as JPush;


class Common
{
    
	/**
	 * 验证日期是否合法
	 *
	 */
	public static function isDate($dateString) {
		return strtotime( date('Y-m-d', strtotime($dateString)) ) === strtotime( $dateString );
	}	
	
    /**
     * 输出JSON字符串
     * @param int $status
     * @param string $error_code
     * @param void $data
     * @return string
     */
    public static function outputJson($status,$error_code,$data=null,$extenArray=array()){

        if(isset($_SERVER['HTTP_ORIGIN'])){
            $originDomain = $_SERVER['HTTP_ORIGIN'];
        }else{
            $originDomain = 'http://www.wuyipai.cn';
        }

        //self::writeLogs('info',440,$originDomain);
        if(in_array($originDomain,['http://www.wuyipai.cn','http://m.wuyipai.cn','http://m.cn120cn.cn','http://m.slchu.cn','http://m.huiligou.cn','http://m.cekongyiqi.cn'])) {
            header('Access-Control-Allow-Origin: ' . $originDomain);
            header('Content-Type: application/javascript; charset=utf-8');
        }

        $lists = array("status"=>$status,"error_code"=>$error_code);
        if(is_array($extenArray)){
            $lists = array_merge($lists,$extenArray);
        }else{
            $tempArray = explode("^|^",$extenArray);
            $lists[$tempArray[0]] = $tempArray[1];
        }
        $lists["data"] = $data;
        $result = json_encode($lists);
        echo $result;
        exit();
    }
    
    /**
     * 验证参数是否为空
     * @param array $postParamArray POST参数
     * @param array $srcParamArray  需要验证的参数
     */
    public static function validationParams($postParamArray ,$srcParamArray){
                
        //验证key/value的是否非空值
        foreach ($srcParamArray as $value){
            if(!isset($postParamArray[$value])){
                return false;
            }
            if(empty($postParamArray[$value])){
                return false;
            }
        }
        return true;
    }
    
    /**
     * 验证签名是否正确
     * @param type $paramArray
     * @param type $sign
     * @return boolean
     */
    public static function  validationSign($appkey,$paramArray, $sign){
        $paramSign = self::md5Sign($appkey,$paramArray);
		
        if($paramSign != $sign){
            return false;
        }
        return true;
    }

    /**
     * md5签名
     * @param array $paramArray
     * @return string
     */
    public static function md5Sign($appkey,$paramArray){
        
        //去除sign参数
        if(isset($paramArray["sign"])){
            unset($paramArray["sign"]);
        }
        $paramString = Common::paramSort($paramArray);
        $sign = md5($paramString . $appkey);      
		//fwrite(fopen("/data1/www/api-service_yftechnet_com/storage/logs/sign.txt","w"),$paramString . $appkey."---".$sign);
        return $sign;
    }
    
    /**
     * 将key/value参数 a 到 z 的顺序排序
     * @param array $params
     * @return string
     */
    public static function paramSort($paramArray){
                
        $tempParamArray = array();
        $arrayKey = array_keys($paramArray);
        sort($arrayKey);
        foreach ($arrayKey as $value){
            if(empty($paramArray[$value])){
                continue;
            }
            $tempParamArray[] = $value."=".$paramArray[$value];
        }
        $paramString = implode("&", $tempParamArray);
        return $paramString;
    }

    /**
     * 生成商户消费订单号36位
     * @param type $uid
     * @return string
     */
    public static function getOrderNo(){

        $milliSecond =  floor(microtime(true) * 1000);
        $strNo = date("ymdHis") . $milliSecond.rand(0,9);
        $len = strlen($strNo);
        if($len < 16){
            $strUid = "";
            for($i = 0;$i < 16 - $len;$i++){
                $strUid .= rand(0,9);
            }
            $strNo = $strNo.$strUid;
        }
        return $strNo;
    }

    /**
     * 获取平台商户号
     * @param type $uid
     * @return string
     */
    public static function getMierchantNo(){

        $milliSecond =  floor(microtime(true) * 1000);
        $strNo = "wm".date("ymdHis") . $milliSecond.rand(100,999);
        return $strNo;
    }

    /**
     * 获取编号
     * pic+  时间(20161208201920) + 时间截 + 随机数)
     * @param type $uid
     * @return string
     */
    public static function getNumberno(){
        $milliSecond =  floor(microtime(true) * 1000);
        $strNo = "pic".date("YmdHis").$milliSecond.rand(100,999);
        return $strNo;
    }

    /**
     * 获取客户端IP
     * @return string
     */
    public static function getIP(){
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";

        return $ip;
    }
    
    /**
     * 字符串RSA加密
     * @param type $originalData   用于加密的字符串
     * @param type $privateKeyString    密钥文件内容
     * @return void
     */
    public static function RsaEncrypt($originalData, $privateKeyString){

        $privateKeyString = openssl_pkey_get_private($privateKeyString);
        if (openssl_private_encrypt($originalData, $encryptData, $privateKeyString)){
            $encryptData = base64_encode($encryptData);
            return $encryptData;
        }else{
            return false;
        }
    }
    
    /**
     * 字符串RSA解密
     * @param type $encryptData
     * @param type $publicKeyString
     * @return void
     */
    public static function RsaDecrypt($encryptData, $publicKeyString){
        
        $publicKeyString = openssl_pkey_get_public($publicKeyString);
        $encryptData = base64_decode($encryptData);
        if (openssl_public_decrypt($encryptData, $originalData, $publicKeyString)) {  
            return $originalData;
        } else {  
            return false;
        }
    }

    /**
     * 写日志文件 Common::writeLogs("error",0,json_encode($_REQUEST));
     * @param $type  日志类型
     * @param  $code 状态码
     * @param $msg   信息
     * @return bool
     */
    public static function writeLogs($type,$code,$msg){

        //日志类型
        $logType = array(
            "info"=>Logger::INFO,
            "error"=>Logger::ERROR,
            "notice"=>Logger::NOTICE,
            "warning"=>Logger::WARNING,
            "critical"=>Logger::CRITICAL,
            "alert"=>Logger::ALERT,
            "emergency"=>Logger::EMERGENCY,
            "debug"=>Logger::DEBUG);
        if(!in_array($type,array_keys($logType))){
            return false;
        }

        $msgContents =   "||" . $code . "||" . $_SERVER["REQUEST_URI"] . "||" . $msg. "||" ;
        $logFile = storage_path() . "/logs/custom-" . date("YmdH", time()) . '.log';
        //写日志
        $log = new Logger('shixian');
        $log->pushHandler(new StreamHandler($logFile, $logType[$type]));
        $log->$type($msgContents);
        return true;
    }

    /**
     * POST请求
     * @param $remote_server
     * @param $post_string
     * @return array
     */
    public static function requestByCurl($remote_server, $post_string){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "POST" );
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
        curl_setopt($ch,CURLOPT_TIMEOUT,5);
        $data = curl_exec($ch);
        //self::writeLogs('info',22222,$data);
        $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($code != 200){
            return false;
        }
        return $data;
    }

    //GET请求
    public static function getRequestByCurl($url){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * 目录路径
     * @param $path
     * @return mixed
     */
    public static function mkPathDir($path){
        if (!is_dir($path)) {
            mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
        }
        return $path;
    }

    /**
     * 等比例生成缩略图
     * @param $imgSrc
     * @param $resize_width
     * @param $resize_height
     * @param $isCut
     * @author james.ou 2011-11-1
     */
    public static function reSizeImg($imgSrc, $resize_width, $resize_height, $isCut = false) {
        //图片的类型
        $type = substr(strrchr($imgSrc, "."), 1);
        //初始化图象
        if ($type == "jpg") {
            $im = imagecreatefromjpeg($imgSrc);
        }
        if ($type == "gif") {
            $im = imagecreatefromgif($imgSrc);
        }
        if ($type == "png") {
            $im = imagecreatefrompng($imgSrc);
        }
        //目标图象地址
        $full_length = strlen($imgSrc);
        $type_length = strlen($type);
        $name_length = $full_length - $type_length;
        $name = substr($imgSrc, 0, $name_length - 1);
        $dstimg = $name . "_" . $resize_width . 'x' . $resize_height . '.' . $type;

        $width = imagesx($im);
        $height = imagesy($im);

        //生成图象
        //改变后的图象的比例
        $resize_ratio = ($resize_width) / ($resize_height);
        //实际图象的比例
        $ratio = ($width) / ($height);
        if (($isCut) == 1) { //裁图
            if ($ratio >= $resize_ratio) { //高度优先
                $newimg = imagecreatetruecolor($resize_width, $resize_height);
                imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, $resize_height, (($height) * $resize_ratio), $height);
                ImageJpeg($newimg, $dstimg);
            }
            if ($ratio < $resize_ratio) { //宽度优先
                $newimg = imagecreatetruecolor($resize_width, $resize_height);
                imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, $resize_height, $width, (($width) / $resize_ratio));
                ImageJpeg($newimg, $dstimg);
            }
        } else { //不裁图
            if ($ratio >= $resize_ratio) {
                $newimg = imagecreatetruecolor($resize_width, ($resize_width) / $ratio);
                imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, ($resize_width) / $ratio, $width, $height);
                ImageJpeg($newimg, $dstimg);
            }
            if ($ratio < $resize_ratio) {
                $newimg = imagecreatetruecolor(($resize_height) * $ratio, $resize_height);
                imagecopyresampled($newimg, $im, 0, 0, 0, 0, ($resize_height) * $ratio, $resize_height, $width, $height);
                ImageJpeg($newimg, $dstimg);
            }
        }
        ImageDestroy($im);
    }

    //多维数组排序
    public static function arrayMultisort($data,$sortOrderField,$sortOrder=SORT_ASC,$sortType=SORT_NUMERIC){
        foreach($data as $val){
            $key_arrays[] = $val[$sortOrderField];
        }
        array_multisort($key_arrays,$sortOrder,$sortType,$data);
        return $data;
    }


    public static function getTimeArray($time,$minTime = '08:30:00',$maxTime = '22:00:00'){

        $startNowTime = $time + 5400;
        if(date('Y-m-d') != date('Y-m-d',$time)){
            $startNowTime = $time + 1800;
        }
        $endtime = strtotime(date('Y-m-d' . ' ' . $maxTime,$time));
        $timeList = array();
        while ($startNowTime < $endtime){
            $t = $startNowTime + 7200;

            $key = date('Y-m-d H:i',$startNowTime) . ' - ' . date('Y-m-d H:i',$t);
            $timeList[$key] = date('H:i',$startNowTime) . '-' . date('H:i',$t);  //$dateText . ' ' .
            $startNowTime += 1800;
            if($t > $endtime){
                break;
            }
        }

        $minTime = strtotime(date('Y-m-d',$time) . ' ' . $minTime);
        $maxTime = strtotime(date('Y-m-d',$time) . ' ' . $maxTime);
        $timeListArray = array();
        foreach($timeList AS $k=>$v){
            $tempData = explode(' - ',$k);
            $sntime = strtotime($tempData[0].':00');
            $entime = strtotime($tempData[1].':00');
            if($sntime > $minTime && $entime < $maxTime){
                $timeListArray[] = $v;
            }
        }

        return $timeListArray;
    }

    /**
     * 价格格式化输出
     * @param $price
     */
    public static function price_format($price){
        return number_format($price,2,".","");
    }

    /**
     * 递归获取所有的子分类的ID
     * @param $array
     * @param $id
     * @param string $fieldName
     * @return array
     */
    public static function getAllChild($array,$id,$fieldName = 'parent_id'){
        $arr = array();
        foreach($array as $v){
            if($v[$fieldName] == $id){
                $arr[] = $v['id'];
                $arr = array_merge($arr,self::getAllChild($array,$v['id']));
            }
        }
        return $arr;
    }

    /**
     * 递归列表输出
     * @param $arr
     * @param $id
     * @param $step
     * @return array
     */
    public static function generateTree($arr,$id,$step,$pfieldName = 'parent_id'){
        static $tree = [];
        foreach($arr as $key=>$val) {
            if($val[$pfieldName] == $id) {
                $tree[] = $val;
                self::generateTree($arr , $val['id'] ,$step + 1,$pfieldName);
            }
        }
        return $tree;
    }

    /**
     * 数字转成文本
     * @param ini $n;
     */
    public static function numToText($n){

        $text = array('零','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十');
        return $text[$n];
    }

    /**
     * 产生随机数串
     * @param integer $len 随机数字长度
     * @return string
     */
    public static function randomKeys($length)
    {
        $key='';
        $pattern='1234567890';
        for($i=0;$i<$length;++$i) {
            $key .= $pattern{mt_rand(0,9)};    // 生成php随机数
        }
        return $key;
    }


    public static function getLastTime($time)
    {
        // 当天最大时间
        $time = strtotime($time);
        $todayLast = strtotime(date('Y-m-d 23:59:59'));
        $agoTimeTrue = time() - $time;
        $agoTime = $todayLast - $time;
        $agoDay = floor($agoTime / 86400);

        if ($agoTimeTrue < 60) {
        $result = '刚刚';
        } elseif ($agoTimeTrue < 3600) {
        $result = (ceil($agoTimeTrue / 60)) . '分钟前';
        } elseif ($agoTimeTrue < 3600 * 12) {
        $result = (ceil($agoTimeTrue / 3600)) . '小时前';
        } elseif ($agoDay == 1) {
        $result = '昨天 ';
        } elseif ($agoDay == 2) {
        $result = '前天 ';
        } else {
            $format = date('Y') != date('Y', $time) ? "Y-m-d" : "m-d";
            $result = date($format, $time);
        }
        return $result;
    }


    public static function arrayToObject($arr) {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)self::arrayToObject($v);
            }
        }

        return (object)$arr;
    }

    public static function objectToArray($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)self::objectToArray($v);
            }
        }

        return $obj;
    }


    /**
     * 消息推存
     * @param $tag
     * @param $title
     * @param $msg
     * @param array $option
     * @return array
     */
    public static function pushMsg($tag,$title,$msg,$option=array()){
        try {
            $client = new JPush(Config::get("weixin.jiguang_appkey"), Config::get("weixin.jiguang_secret"),storage_path() . '/logs/push.log');
            if($tag != 'all_msg') { //单发调用
                $response = $client->push()->setPlatform(array('android'))
                    ->addAlias($tag)
                    ->addTag($tag)//多发调用
                    ->setNotificationAlert($title)
                    ->androidNotification($msg, array(
                        'title' => $title,
                        'extras' => $option,
                        //                    'extras' => array(
                        //                        'type' => 0,  //=0 公共信息通知
                        //                        'url'  => ''  //刷新页面
                        //                    ),
                    ))->options(array(
                        'apns_production' => false,
                    ))->send();
            }else{ //多发调用
                $response = $client->push()->setPlatform(array('android'))
                    ->addTag($tag)//多发调用
                    ->setNotificationAlert($title)
                    ->androidNotification($msg, array(
                        'title' => $title,
                        'extras' => $option,
                        //                    'extras' => array(
                        //                        'type' => 0,  //=0 公共信息通知
                        //                        'url'  => ''  //刷新页面
                        //                    ),
                    ))->options(array(
                        'apns_production' => false,
                    ))->send();
            }

            return $response;
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            print $e;
            return false;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            print $e;
            return false;
        }

    }


    public static function dwzcreated($url){

        $aHeader = array(
            'Content-Type:application/json; charset=UTF-8',
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Host:dwz.cn',
            'Origin:https://dwz.cn',
            'Referer:https://dwz.cn/',
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36'
        );

        $api = Config::get("app.dwz_api");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('url'=>$url)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "POST" );
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE );

        //执行cURL会话
        $ret = curl_exec($ch);
        $retInfo = curl_getinfo($ch);
        if($retInfo['http_code'] == 200){
            $data = json_decode($ret, true);
            if($data['Code'] != 0){
                return $url;
            }else{
                return $data['ShortUrl'];
            }
        }else{
            return $url;
        }
    }

    public static function getDistance($lat1, $lng1, $lat2, $lng2){

        //将角度转为狐度

        $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度

        $radLat2=deg2rad($lat2);

        $radLng1=deg2rad($lng1);

        $radLng2=deg2rad($lng2);

        $a = $radLat1 - $radLat2;

        $b = $radLng1 - $radLng2;

        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;

        return $s;

    }



}
