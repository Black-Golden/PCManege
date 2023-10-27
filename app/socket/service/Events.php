<?php

namespace app\socket\service;

use app\common\model\Member;
use app\common\model\QuantDay;
use app\common\model\QuantMarketItem;
use app\common\model\QuantMarketSell;
use app\common\model\QuantProfit;
use app\common\model\QuantRank;
use app\common\model\QuantSymbol;
use app\common\model\QuantTransfer;
use app\common\model\QuantWallet;
use app\common\service\QuantProfitService;
use ccxt\Exchange;
use GatewayWorker\Lib\Gateway;
use think\facade\Db;
use \Workerman\Lib\Timer;
use \Workerman\Connection\AsyncTcpConnection;
use app\common\service\QuantMarketItemService;
use ccxt\okex5;
use ccxt\binance;

class Events
{

    private $test = 30000;
    private $redis = null;


    public static function oprice($symbol_list)
    {
        $con = new AsyncTcpConnection('ws://ws.okx.com:8443/ws/v5/public');
        foreach ($symbol_list as $key => $value) {
            $symbol = str_replace("/", "-", $value) . "-SWAP";
            $data1[] = [
                "channel" => "tickers",
                "instId" => $symbol,
            ];
        }
        foreach ($symbol_list as $key => $value) {
            $symbol = str_replace("/", "-", $value);
            $data2[] = [
                "channel" => "tickers",
                "instId" => $symbol,
            ];
        }
        $args = array_merge($data1, $data2);
        // 设置以ssl加密方式访问，使之成为wss
        $con->transport = 'ssl';
        $con->onConnect = function ($con) use ($args) {
            $data = json_encode([
                "op" => "subscribe",
                "args" => $args
            ]);

            $con->send($data);
        };
        $con->onMessage = function ($con, $data) {

            //$con->send("ping");
            //$con->send("pong");
            if ($data == "ping") {
                $con->send("pong");
            }
            $data = json_decode($data, true);
            //print_r($data);
            if (isset($data["arg"]) && isset($data["data"])) {
                cache("O/" . str_replace("-", "/", $data["arg"]["instId"]), ["price" => $data["data"][0]["last"], "high" => $data["data"][0]["high24h"], "low" => $data["data"][0]["low24h"], "o" => $data["data"][0]["sodUtc0"],"n24" => $data["data"][0]["volCcy24h"]]);
            }
        };
        $con->onClose = function ($con) {
            $con->reconnect(30);
        };
        $con->connect();
    }

    public static function bhprice()
    {
        $con = new AsyncTcpConnection('ws://fstream.binance.com/ws/!miniTicker@arr');
        // 设置以ssl加密方式访问，使之成为wss
        $con->transport = 'ssl';
        $con->onConnect = function ($con) {

        };
        $con->onMessage = function ($con, $data) {
            if ($data == "ping") {
                $con->send("pong");
            }
            $b = json_decode($data, true);
            if (is_array($b)) {
                foreach ($b as $val) {
                    cache("B/" . str_replace("USDT", "/USDT", $val["s"]) . "/SWAP", ["price" => $val["c"], "high" => $val["c"], "low" => $val["l"],"o" => $val["o"], "n24" => $val["v"]]);
                }
            }

            cache("binance-line-swap", $data);
        };
        $con->onClose = function ($con) {
            $con->reConnect(60);
        };
        $con->connect();
    }

    public static function bprice()
    {
        $con = new AsyncTcpConnection('ws://stream.binance.com/ws/!miniTicker@arr');
        // 设置以ssl加密方式访问，使之成为wss
        $con->transport = 'ssl';
        $con->onConnect = function ($con) {

        };
        $con->onMessage = function ($con, $data) {
            if ($data == "ping") {
                $con->send("pong");
            }
            $b = json_decode($data, true);
            if (is_array($b)) {
                foreach ($b as $val) {
                    cache("B/" . str_replace("USDT", "/USDT", $val["s"]), ["price" => $val["c"], "high" => $val["c"], "low" => $val["l"]]);


                }
            }
            cache("binance-line", $data);
        };
        $con->onClose = function ($con) {
            $con->reConnect(60);
        };
        $con->connect();
    }

    public static function onWorkerStart($businessWorker)
    {
        $list = QuantSymbol::where(["is_online" => 1])->column("title", "id");
        //http://api.quant.yrnet.top/api/cron/get_second_okex
        //获取行情信息
        if ($businessWorker->id == 0) {
            self::bhprice();
        }
        if ($businessWorker->id == 1) {
            self::bprice();
        }


        if ($businessWorker->id == 2) {
            self::oprice($list);
        }


        if ($businessWorker->id == 3) {
            Timer::add(1, function () {
                //file_get_contents("http://api1.dev.quant.yrnet.top/api/cron/get_cache_symbol");
            });
        }
//            Timer::add(3, function () {
//               file_get_contents("http://api.quant.yrnet.top/api/cron/get_second_okex");
//            });
        // ssl需要访问443端口
        //Timer::add(3, function () {
        //file_get_contents("http://api.quant.yrnet.top/api/cron/get_second_binance");
        //});

//        //判断是否充值进来
//        Timer::add(10, function () {
//            $member = Member::where("id", "<>", 0)->field("id,addr_recharge")->select();
//            self::init($member);
//        });
//
//
//        //奖励获取
//        Timer::add(2, function () {
//            QuantProfitService::get_profit();
//        });
//        //获取每日盈利的
//        Timer::add(10, function () {
//            QuantProfitService::get_daily();
//        });

//

    }


    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {

    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message)
    {

    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {

    }
}
