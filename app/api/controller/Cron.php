<?php

namespace app\api\controller;

use app\common\service\ConsultService;
use app\common\service\CronService;
use app\common\service\SingleConfigService;
use app\common\service\SingleNewSetService;
use app\common\service\SymbolService;
use app\common\service\QuantTransferService;
use app\common\service\SingleSetService;
use app\v2\controller\Single;

class Cron extends Backend
{
    public function test()
    {

    }

    //抓取新闻行情
    public function by_news()
    {
        $consult = new ConsultService();
        $consult->by();
        return return_json();
    }

    //设置购买等级
    public function lever_buy_config()
    {
        $service = new SingleConfigService();
        $service->lever_buy_config();
        return return_json();
    }

    public function open()
    {
        $param = input();
        if (isset($param["user_id"])) {

        }
        $fp = fopen("lock/open", "w+");
        if (flock($fp, LOCK_EX)) {
            //$service = new SingleSetService();
            //$service->open();
            return return_json();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return return_json();

    }


    public function high()
    {

        $param = input();
        if (isset($param["user_id"])) {

        }
        $fp = fopen("lock/high", "w+");
        if (flock($fp, LOCK_EX)) {
            $service = new SingleSetService();
            $service->high();
            return return_json();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return return_json();
    }

    //获取是否亏损补仓
    public function low()
    {
        $param = input();
        if (isset($param["user_id"])) {

        }
        $fp = fopen("lock/low", "w+");
        if (flock($fp, LOCK_EX)) {
            $service = new SingleSetService();
            $service->low();
            return return_json();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return return_json();
    }

    //恢复补仓
    public function opendown()
    {
        $service = new SingleSetService();
        $service->auto_open_down();
        return return_json();
    }

    //更新价格
    public function init_price()
    {
        $service = new SymbolService();
        $service->init_price();
        return return_json();
    }

//    //每天获取一下欧易最小获取值
    public function get_second_okex()
    {
        $cron_service = new CronService();
        return return_json($cron_service->get_second_okex());
    }
//
//    //获取每秒币安行情价
//    public function get_second_binance()
//    {
//        $cron_service = new CronService();
//        return return_json($cron_service->get_second_binance());
//    }
//
//    //获取缓存价格
//    public function get_cache_symbol()
//    {
//        $cron_service = new CronService();
//        return return_json($cron_service->get_cache_symbol());
//    }

    //初始化每天的开盘价
    public function init_open_day()
    {
        $cron_service = new SymbolService();
        $cron_service->init_open_day();
        return return_json([]);
    }

    //获取充值的数据
    public function init_minute_recharge_price()
    {
        QuantTransferService::init();
        return return_json([]);
    }

//    //获取每日盈利的和获取奖励
//    public function init_second_profit()
//    {
//        QuantProfitService::get_daily();
//        //计算BE
//        QuantProfitService::get_hedge_daily();
//        QuantProfitService::get_profit();
//        return return_json([]);
//    }

    //用户充值
    public function transfer()
    {
        $service = new CronService();
        return return_json($service->transfer());
    }

    //归集金额
    public function imputation()
    {
        $service = new CronService();
        //return return_json($service->imputation());
    }

    //用户提现
    public function withdraw()
    {
        $service = new CronService();
        return return_json($service->withdraw());
    }

    //
    public function profit()
    {
        $service = new CronService();
        return return_json($service->profit());
    }

//    //马丁策略收益
//    public function market_profit()
//    {
//        $service = new CronService();
//        return return_json($service->market_profit());
//    }
//
//    //用户收益
//    public function user_profit()
//    {
//        $service = new CronService();
//        return return_json($service->user_profit());
//    }
//
//    //用户排行榜
//    public function user_ranking()
//    {
//        $service = new CronService();
//        return return_json($service->user_ranking());
//    }
//
//    //用户排行榜
//    public function day_open_price()
//    {
//        $service = new CronService();
//        return return_json($service->day_open_price());
//    }
}
