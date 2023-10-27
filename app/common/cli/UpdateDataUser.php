<?php
declare (strict_types=1);

namespace app\common\cli;

use app\common\middleware\InitApp;
use app\common\model\ConfigSc;
use app\common\service\GoodsService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\service\GenerateService;
use think\facade\Db;

class UpdateDataUser extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('update_user')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {
        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();


        $arr = ConfigSc::update1();

        // 指令输出
        $output->writeln('hello');
    }
}
