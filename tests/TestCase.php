<?php

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    
     protected $baseUrl = 'https://api.wuyipai.cn/';
     
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
    
    protected function requestByCurl($remote_server, $post_string)
    { 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $data = curl_exec($ch);
        curl_close($ch);
        return "\n\nresult:\n".$data;
    } 
    
    /**
     * md5签名
     * @param array $paramArray
     * @return string
     */
    protected function md5Sign($paramArray){
        
        //去除sign参数
        if(isset($paramArray["sign"])){
            unset($paramArray["sign"]);
        }
        $appkey = $this->getClientAppKey($paramArray["appid"]);
        $paramString = $this->paramSort($paramArray);//echo $appkey;
        $sign = md5($paramString . $appkey);      
        return $sign;
    }
    
    /**
     * 将key/value参数 a 到 z 的顺序排序
     * @param array $params
     * @return string
     */
    protected function paramSort($paramArray){

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
     * 获得客户端appid appkey
     * @param type $appid
     */
    protected function getClientAppKey($appid){
        
        $appKey = "Q!612@h";
        return $appKey;
    }
}
