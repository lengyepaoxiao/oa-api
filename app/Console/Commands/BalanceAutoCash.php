<?php

//用户提现打款到微信余额
//设定1小时处理一次

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\WeixinApiModel;
use App\Models\CashlogModel;
use App\Libs\Common;

class BalanceAutoCash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:BalanceAutoCash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $_hour = 1;  //一分钟

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

        $result = CashlogModel::lists();
        $lists = $result->toArray();
        if(!$lists){
            return;
        }

        //调用企业付款功能
        foreach ($lists as $key=>$val){
            $pkey = $val['pkey'];
            $id = $val['id'];
            $app = WeixinApiModel::appPayment($pkey);

            if($val['amount'] <= 5000) {// 小于10块可以提现到帐
                $result = $app->transfer->toBalance([
                    'partner_trade_no' => Common::getOrderNo(), // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
                    'openid' => $val['openid'],
                    'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
                    'amount' => $val['amount'], // 企业付款金额，单位为分
                    'desc' => '推广佣金提现', // 企业付款操作说明信息。必填
                ]);

                if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                    $param['status'] = 1;
                }else{
                    $param['status'] = 2;
                }
                CashlogModel::where('id','=',$id)->where('status','=',0)->update($param);
                var_dump($result);
            }
        }

    }
}
