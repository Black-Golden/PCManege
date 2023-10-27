<?php
declare (strict_types=1);

namespace app\common\cli\setting;

use app\common\middleware\InitApp;
use app\common\service\ConsultService;
use app\common\service\QuantTransferService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use app\common\service\SymbolService;

class InitMinuteRechargePrice extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('init_minute_recharge_price')->addArgument('action', Argument::OPTIONAL, "")
            ->setDescription('init_minute_recharge_price');
    }

    protected function execute(Input $input, Output $output)
    {
        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();
        $transfer = new QuantTransferService();
        $action = trim($input->getArgument('action'));
        if ($action == "transfer") {
            //获取充值金额
            $transfer->init();
        }
        if ($action == "imputation") {
            //归集
            $transfer->imputation();
        }
        if ($action == "withdraw") {
            //转账给用户
            $transfer->withdraw();
        }
        if ($action == "consult") {
            $service = new ConsultService();
            $service->by(1);
        }
    }
}
