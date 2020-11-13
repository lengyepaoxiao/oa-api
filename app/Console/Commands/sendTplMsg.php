<?php

//发送消息

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\WeixinApiModel;
use Illuminate\Support\Facades\Cache;

class sendTplMsg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendTplMsg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $sendMsg = storage_path() . '/logs/sendMsg_'. date('Y-m-d') . '.log';
        $pagePath = 'pages/cover/cover';
        $arrayKey = array('wm003'=>'urmsyA09zcuTt7GFIkg_9shiJOwrj8UX6csXdORHZlU','wm004'=>'HVdEMzVtc6EI3O_62ow2eVCjDUcixsF3a5c44rAthKA');
        $title = "你好友发起挑战";
        $desc = "好友准备登顶8888元大奖...";

        foreach($arrayKey as $pkey=>$tplId) {

            //获取用户ID
            $dateStr = date('Ymd', strtotime(" -1 day "));
            $dateStr = date('Ymd');

            $cacheLpushKey = 'ac_api:' . $pkey . '_tpl_lists:' . $dateStr;
            $redisLists = Cache::store('redis')->getRedis()->lrange($cacheLpushKey, 0, -1);
            foreach($redisLists as $openid){

                $cacheFormIdKey = $pkey.'_tpl_formid:' . $openid;
                $fromId = Cache::store('redis')->get($cacheFormIdKey);
                if(empty($fromId)){
                    continue;
                }

                //发送模板
                $app = WeixinApiModel::miniProgram($pkey);
                $param = [
                    'touser' => $openid,
                    'template_id' => $tplId,
                    'page' => $pagePath,
                    'form_id' => $fromId,
                    'data' => [
                        'keyword1' => ['value'=>$title,'color'=>'#e80000'],
                        'keyword2' => $desc,
                    ],
                    'emphasis_keyword' =>'keyword1.DATA'
                ];

                $ret = $app->template_message->send($param);
                if ($ret['errmsg'] == 'ok') {
                    $status = 1;
                    Cache::store('redis')->forget($cacheFormIdKey);
                } else {
                    $status = 0;
                }
                fwrite(fopen($sendMsg,'a+'),$pkey . '|' . $status);
                sleep(1);

            }

        }

        return ;


    }
}
