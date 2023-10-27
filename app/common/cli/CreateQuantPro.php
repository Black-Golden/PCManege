<?php
declare (strict_types=1);

namespace app\common\cli;

use app\common\middleware\InitApp;
use app\common\model\ConfigSc;
use app\common\model\QuantMarketItem;
use app\common\model\QuantMarketSell;
use app\common\model\QuantSetup;
use app\common\model\QuantSymbol;
use app\common\service\QuantProfitService;
use ccxt\Exchange;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\service\GenerateService;
use app\common\service\QuantMarketItemService;
use app\common\service\SymbolService;
use think\facade\Db;
use PDO;
use ccxt\okex5;
use ccxt\binance;
use Workerman\Lib\Timer;

class CreateQuantPro extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('quant_pro')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {

        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();

       // });
        //exit;
        $item = new QuantMarketItemService();
        //第一次运行
       // $item->first();
        //去购买
        $item->sell("pro");
        //处理详情
       // $item->item();
////        //然后再卖出
//        $item->sell("pro");
////        //最后处理涨跌（）
//        $item->goup("high",$output);
////
//        $item->goup("low",$output);
        exit;

    }
}
