<?php
require "vendor/workerman/workerman/Autoloader.php";
require "vendor/autoload.php";
use Workerman\Worker;
use Workerman\Lib\Timer;
$worker = new Worker();
$worker->count=3;
$url = "http://api.wending.7w1.top/api/cron/";
$worker->onWorkerStart = function () use($url){
//        //初始化建仓
    Timer::add(1, function ()use($url) {
        file_get_contents($url . "hfirst");
    });
//        //购买
    Timer::add(1, function ()use($url) {
        file_get_contents($url . "hbuy");
    });
//        //更新持仓
    Timer::add(1, function ()use($url) {
        file_get_contents($url . "hitem");
    });
//        //卖出
    Timer::add(1, function ()use($url) {
        file_get_contents($url . "hsell");
    });
//是否达标盈利
    Timer::add(1, function () use($url){
        file_get_contents($url . "hhigh");
    });
};

Worker::runAll();
