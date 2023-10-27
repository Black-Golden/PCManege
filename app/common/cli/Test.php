<?php
declare (strict_types=1);

namespace app\common\cli;

use app\common\middleware\InitApp;
use app\common\model\BaseModel;
use app\common\model\ConfigSc;
use app\common\model\Invate;
use app\common\model\Member;
use app\common\model\QuantSymbol;
use app\common\model\SingleLoopBuy;
use app\common\service\CutSetService;
use app\common\service\QuantTransferService;
use app\common\service\SingleLoopBuyService;
use app\common\service\SingleNewSetService;
use app\common\service\SingleSetService;
use app\common\service\TeamService;
use app\common\service\TradeProfitService;
use app\extend\Trade;
use app\extend\TronTools;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\service\GenerateService;
use think\facade\Db;
use PDO;

class Test extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('test')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {


        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();

        $pro = new SingleSetService();
        $buy_service = new SingleLoopBuyService();


        $trade =new TradeProfitService();

        $a = $trade->get_user_profit();
        print_r($a);
        exit;


        $trade = new Trade();
        $balance = $trade->get_balance([
            "user_id" => 1,
            "platform_id" => 3,
            "platform_type"=>1
        ]);
        print_r($balance);



        exit;
//        print_r($buy_service->test(0.015));
//        exit;
        $list =  $buy_service->sell_every2(3, 0.012, 0.001);
        print_r($list);
       $list =  $buy_service->sell_every2(3, 0.012, 0.001,"desc");
       print_r($list);
       exit;


        $list = SingleLoopBuy::where([ "is_sell" => 0, "is_deal" => 2])->where("rounds",">",1)->group("setup_id")->column("setup_id");

        print_r($list);
        exit;

        $pro = new TradeProfitService();
        $pro->sync();
        exit;

        $service = new CutSetService();
        $service->cut_all(1567);

        exit;
        $service = new SingleSetService();

        //$service->cut_all_test(674);
        //$service->sync();

        exit;

        $user_id = 10;
        $platform_id = 2;
        $arr = $trade->order([
            "user_id" => $user_id,
            "platform_id" => $platform_id,
        ]);
        foreach ($arr as $key => $value) {
            $result = $trade->ssell([
                "user_id" => $user_id,
                "symbol" => $value["symbol"],
                "platform_id" => $value["platform_id"],
                "qb_token" => $value["qb_token"],
                "open_type" => $value["open_type"],
            ]);
            print_r($result);
        }

        //谨慎操作


        //print_r($arr);
//        $arr = $trade->order([
//            "user_id" => 1104623,
//            "platform_id" => 2,
//        ]);
//        print_r($arr);
        exit;


        // $service->cut_all(464);


        exit;

        $service = new TeamService();
        $trade = new Trade();
        $arr = $trade->set_leverage([
            "user_id" => 1104623,
            "platform_id" => 3,
            "symbol" => "EOS/USDT",
            "lever" => 50,
        ]);

        print_r($arr);
        exit;

        $top = $service->per_top(1104625);
        print_r($top);
        exit;
        echo numToWord(51234);
        exit;


        $trade = new Trade();
        $arr = $trade->get_balance([
            //"symbol"=>"BTCUSDT",
            "user_id" => 5,
            "platform_id" => 3,
            "platform_type" => 2,
        ]);
        print_r($arr);
        exit;

        $service = new QuantTransferService();
        $a = $service->url_request("TCENSBAMnDExAFU7KGPSixmoRBgPSAhfaw");
        print_r($a);


        exit;
        // $test = new TestMember();


//        $test = $test->select();
//
//        foreach ($test as $k =>$v){
//
//           // $secret = $this->ga->createSecret();
//            $tts = new TronTools();
//            $res = $tts->create();
//            $data = [
//                "id"=>$v["uid"],
//                'username' => $v["uid"],
//                'nickname' => $v["uid"],
//                'password' => md5(sha1("123456")),
//                'tradepassword' => md5(sha1("123456")),
//                'google_key' => '',
//                'rank' => 0,
//                'recode' => get_reccode(),
//                'headimg' => '/images/person.png',
//                'reg_time' => time(),
//                'addr_cash' => null,
//                'addr_recharge' => $res['address'],
//                'private_key' => $res['private_key'],
//                'qb_usdt' => 0,
//                "referee_id"=>$v["referee_id"]
//            ];
//            $user_id = Member::create($data);
//        }
//
//        exit;

        $sigle = SingleLoopBuy::where(["is_sell" => 0, "lever" => 20, "is_deal" => 2])->select();
        foreach ($sigle as $key => $value) {
            if ($value["up_stop_per"] === "0.1") {
                // echo ":asd";

                $up_stop_per = 0.4 / $value["lever"];
                $down_stop_per = 0.6 / $value["lever"];
                if ($value["open_type"] == 2) {
                    $up_stop_price = $value["price"] + $value["price"] * $up_stop_per;
                    $down_stop_price = $value["price"] - $value["price"] * $down_stop_per;
                } else {
                    $up_stop_price = $value["price"] - $value["price"] * $up_stop_per;
                    $down_stop_price = $value["price"] + $value["price"] * $down_stop_per;
                }
                SingleLoopBuy::where(["id" => $value["id"]])->update(["up_stop_price" => $up_stop_price, "down_stop_price" => $down_stop_price, "up_stop_per" => 0.4, "down_stop_per" => 0.6]);
            }
        }

        exit;


        echo rand(2, 5) * 0.1;
        exit;

        $service = new TeamService();
//
//
        print_r($service->per_top(5));
//        print_r($service->team(1100674));
        exit;


        $service = new SingleSetService();

        $service->cut1(11);
        exit;

        //echo bcmod("1.3","0.2",3);
        //exit;
//        echo symbol_price(2,"BTC/USDT");
//        exit;
        $trade = new Trade();
//        $arr  = $trade->get_balance([
//            //"symbol"=>"BTCUSDT",
//            "user_id"=>50,
//            "platform_id"=>2
//        ]);
//        print_r($arr);

//        foreach ($arr as $k=>$v){
//            if($v[""])
//
//        }

//        exit;
//
//        print_r($trade->set_position_mode([
//            "user_id"=>50,
//            "platform_id"=>2
//        ]));
//        exit;


        $arr = $trade->set_leverage([
            "user_id" => 1104623,
            "platform_id" => 3,
            "symbol" => "BTC/USDT",
            "lever" => 50,
        ]);

        print_r($arr);
        exit;
        exit;

        $list = QuantSymbol::whereIn("id", [3, 5, 8, 20, 23, 24, 4, 6, 15, 10, 11])->select();
        foreach ($list as $value) {
            $trade->set_leverage([
                "user_id" => 5,
                "platform_id" => 3,
                "symbol" => $value["title"],
                "lever" => 50,
            ]);
            print_r($service->setup([
                "buy_price" => 500,
                "user_id" => 5,
                "lever" => 50,
                "buy_token" => $value["ct_val"] * 10,
                "type_id" => 2,
                "platform_id" => 2,
                "open_type" => 2,
                "symbol" => $value["title"]
            ]));
            print_r($service->setup([
                "buy_price" => 500,
                "user_id" => 5,
                "lever" => 50,
                "buy_token" => $value["ct_val"],
                "type_id" => 2,
                "platform_id" => 2,
                "open_type" => 3,
                "symbol" => $value["title"]
            ]));
        }
        //print_r();


        //

        exit;

        $table = new SingleLoopSetupConfig();
        $table->where(["setup_id" => 1, "date" => date("Ymd")])->inc("count", 1)->update();
        exit;

        $ga = new \PHPGangsta_GoogleAuthenticator();


        // 创建新的"安全密匙secret"
        $secret = $ga->createSecret();
//        var_dump($secret);die;
        //指定用户名
        $name = "17864371838";
        //第一个参数是"标识",第二个参数为"安全密匙SecretKey" 生成二维码信息


        $qrCodeUrl = $ga->getQRCodeGoogleUrl($name, $secret);
        echo $secret . "\r\n";
        //返回二维码地址
        echo $qrCodeUrl;
        exit;

//        $a = new Invate;
//        return $a->check_num();
//        exit;
//
//        $service= new QuantHedgeItemService();
//       // $service->buy("high");
//            $service->goup("high");
//
//            $service->sell("pro");

//        $list = QuantSymbol::where(["is_online"=>1])->column("symbol","id");
//        print_r($list);


//
//
//        $sum_data = [
//            "xhzj" => 1000,
//            "fkzj" => 1000,
//            "xssl" => 0.13,
//            "fpsl" => 0.13,
//        ];
//        $sum_total_data = sum_order_price($sum_data);
//
//        print_r($sum_total_data);
//        exit;
//
//
//        exit;
//
//        $arr =sum_add_price([
//            "mall_price"=>0,//商城单价
//            "amount"=>10,//数量
//            "discount_rate"=>0,//折扣率
//
//            "new_price"=>0,//采购单价  最新
//            "sale_rate"=>0,//销售税率
//            "put_price"=>0,//入库单价
//            "freight_estimate"=>0,
//            "cost_estimate"=>0,
//            "purchase_rebate"=>0,
//            "invoice_rate_name"=>0//发票税率
//        ]);
//        print_r($arr);
        //ConfigSc::update1();
        exit;


//
//        $server = new \app\common\service\GenerateService();
//        $server->input =[
//            "name"=>"customer",
//            "comment"=>"进项认证"
//        ];
//
//        $list =  $server->generateconfig();
    }
}
