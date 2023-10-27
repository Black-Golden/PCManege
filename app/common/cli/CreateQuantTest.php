<?php
declare (strict_types=1);

namespace app\common\cli;

use app\common\middleware\InitApp;
use app\common\model\ConfigSc;
use app\common\model\QuantSetup;
use app\common\model\QuantSymbol;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\service\GenerateService;
use app\common\service\QuantMarketItemService;
use think\facade\Db;
use PDO;

class CreateQuantTest extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('quant_test')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {

        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();
        //模拟创建交易
        //$list = QuantSymbol::where(["is_online"=>1])->column("symbol","id");

        $user_id = 4;
        $symbol = "TRX/USDT";
        $data = QuantSetup::where(["user_id" => $user_id, "symbol" => $symbol])->find();
        if (!$data) {
            QuantSetup::create([
                "user_id" => $user_id,
                "symbol" => $symbol,
                "rounds" => 7,
                "qb_first" => 10,
                "is_double" => 0,
                "down_stop_per" => "0.04",
                "down_back_per" => "0.004",
                "up_stop_per" => "0.013",
                "up_back_per" => "0.0030",
                "is_run" => 1,
                "is_loop" => 0
            ]);
        }
        $item = new QuantMarketItemService();
        //第一次运行
        $item->first();
        //去购买
        $item->buy("test");
        //处理详情
        $item->item();
//        //然后再卖出
        $item->sell("test");
//        //最后处理涨跌
        $item->goup("high",$output);
//
        $item->goup("low",$output);


        exit;

    }
}
