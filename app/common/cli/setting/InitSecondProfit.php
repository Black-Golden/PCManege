<?php
declare (strict_types=1);

namespace app\common\cli\setting;

use app\common\middleware\InitApp;
use app\common\service\TradeProfitService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class InitSecondProfit extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('init_second_profit')->addArgument('action', Argument::OPTIONAL, "")
            ->setDescription('init_second_profit');
    }

    protected function execute(Input $input, Output $output)
    {
        define('DB_PREFIX', env('database.prefix', ''));
        $action = trim($input->getArgument('action'));
        $init = new InitApp();
        $init->initSystemConstant();
        $profit = new TradeProfitService();
        //处理组合策略收益
        if ($action == "hedge_profit") {
            $profit->set_hedge_profit();
        } else if ($action == "market_profit") {
            //处理马丁策略收益
            $profit->set_market_profit();
        } else if ($action == "user_profit") {
            //处理用户计算
            $profit->get_user_profit();
        }else if ($action == "user_ranking") {
            //处理用户排行
            $profit->ranking(1, 1);
        }
    }
}
