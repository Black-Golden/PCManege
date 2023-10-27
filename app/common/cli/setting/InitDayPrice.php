<?php
declare (strict_types=1);

namespace app\common\cli\setting;

use app\common\middleware\InitApp;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\service\SymbolService;

class InitDayPrice extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('init_day_price')
            ->setDescription('init_day_price');
    }

    protected function execute(Input $input, Output $output)
    {
        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();
        $a = new SymbolService();
        $a->init_open_day();
    }
}
