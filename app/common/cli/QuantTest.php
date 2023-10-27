<?php
declare (strict_types=1);

namespace app\common\cli;

use app\common\middleware\InitApp;
use app\common\model\ConfigSc;
use app\common\model\QuantSetup;
use app\common\model\QuantSymbol;
use app\common\service\MemberService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\service\GenerateService;
use app\common\service\QuantMarketItemService;
use think\facade\Db;
use PDO;

class QuantTest extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('quant1')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {

        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();
        //模拟创建交易
        $member_service =new MemberService();
        $platform_id = 2;
        $exchange = $member_service->sign(4,$platform_id);

        $order_result = $exchange->fetch_order("410898995479986182", "TRX-USDT-SWAP");

        if($platform_id ==2){
            $update_buy_data["qb_to"] = get_round($order_result['amount']* 1000 - $order_result['fee']['cost'], 6);
        }else{
            $update_buy_data["qb_to"] = get_round($order_result['amount'] - $order_result['fee']['cost'], 6);
        }

        $update_buy_data["qb_source"] = $order_result["cost"];
        //实际成本
        $update_buy_data["price"] = $order_result["cost"] / $update_buy_data["qb_to"];
        $update_buy_data["is_deal"] = 2;//进行交易中
        $update_buy_data["qb_fee"] = $order_result['fee']['cost'];


        //if ($value["platform_type"] == 2) {
        //计算保证金
        $update_buy_data["bail"] = $update_buy_data["qb_source"] / 5;
        //}

        print_r($update_buy_data);
        exit;

        //处理详情
        $item->item();
//        //然后再卖出
        //$item->sell("test");
//        //最后处理涨跌
        //$item->goup("high",$output);
//
        //$item->goup("low",$output);


        exit;

    }
}
