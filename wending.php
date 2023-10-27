<?php
require "vendor/workerman/workerman/Autoloader.php";
require "vendor/autoload.php";
require "urlconfig.php";

use Workerman\Worker;
use Workerman\Lib\Timer;

$worker = new Worker();
$worker->count = 1;
$url = URL1;
$worker->onWorkerStart = function () use ($url) {
//        //初始化建仓
//    Timer::add(2, function () use ($url) {
//        file_get_contents($url . "open");
//    });

    Timer::add(60, function () use ($url) {
        file_get_contents($url . "by_news");
    });
//是否达标盈利
    Timer::add(1, function () use ($url) {
        file_get_contents($url . "high");
    });
//        //是否达标补仓
    Timer::add(1, function () use ($url) {
        file_get_contents($url . "low");
    });
    Timer::add(60, function () use ($url) {
        file_get_contents($url . "opendown");
    });
    //更新价格
    Timer::add(10, function () use ($url) {
        file_get_contents($url . "init_price");
    });
    //更新开盘价
    Timer::add(60, function () use ($url) {
        file_get_contents($url . "init_open_day");
    });



    Timer::add(10, function ()use($url) {
        file_get_contents($url . "transfer");
    });
    Timer::add(10, function ()use($url) {
        file_get_contents($url . "imputation");
    });

    Timer::add(10, function ()use($url) {
        file_get_contents($url . "withdraw");
    });

    Timer::add(5, function ()use($url) {
        file_get_contents($url . "profit");
    });

//    Timer::add(5, function () use($url){
//        file_get_contents($url . "market_profit");
//    });

//    Timer::add(5, function () use($url){
//        file_get_contents($url . "user_profit");
//    });

//    Timer::add(5, function () use($url){
//        file_get_contents($url . "user_ranking");
//    });

    Timer::add(600, function () use($url){
        file_get_contents($url . "day_open_price");
    });



//
//    //        //初始化建仓
//    Timer::add(5, function ()use($url) {
//        file_get_contents($url . "hfirst");
//    });
////        //购买
//    Timer::add(1, function ()use($url) {
//        file_get_contents($url . "hbuy");
//    });
////        //更新持仓
//    Timer::add(1, function ()use($url) {
//        file_get_contents($url . "hitem");
//    });
////        //卖出
//    Timer::add(1, function ()use($url) {
//        file_get_contents($url . "hsell");
//    });
////是否达标盈利
//    Timer::add(1, function () use($url){
//        file_get_contents($url . "hhigh");
//    });

};

Worker::runAll();
