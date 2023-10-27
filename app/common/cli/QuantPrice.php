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

class QuantPrice extends Command
{


    protected function configure()
    {
        // 修改行情价
        $this->setName('price')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {

        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();
        //模拟创建交易
        $title = "TRX/USDT";
        //涨幅
        $type ="-";
        $num = 0.005;
        $one = QuantSymbol::where(["title" => $title])->find();
        if($type =="+"){
            $price = $one["oprice"] * $num + $one["oprice"];
        }else{
            $price = $one["oprice"] - $one["oprice"] * $num;
        }
        echo "当前价".$price."\r\n";
        QuantSymbol::where(["title" => $title])->update(["oprice"=>$price]);
    }
}
