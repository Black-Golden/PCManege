<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service;


//处理定时器
use app\common\model\QuantSymbol;

class CronService extends BaseService
{
    public function get_cache_symbol()
    {
        $list = QuantSymbol::where(["is_online" => 1])->select();
        $symbol_service = new SymbolService();
        $symbol_service->init_cache_symbol_binance($list);
        $symbol_service->init_cache_symbol_okex($list);
    }
    //获取ok现货和合约行情
    public function get_second_okex()
    {
        $list = QuantSymbol::where(["is_online" => 1])->column("title", "id");
        $symbol_service = new SymbolService();
        $symbol_service->init_second_okex($list);
        return true;
    }
    //获取币安现货和合约行情
    public function get_second_binance()
    {
        $list = QuantSymbol::where(["is_online" => 1])->column("title", "id");
        $symbol_service = new SymbolService();
        $symbol_service->init_second_binance($list);
        return true;
    }


//    //获取初始化建仓数据
//    public function first()
//    {
//        $fp = fopen("lock.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantMarketItemService();
//            $service->first();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    //获取购买标准
//    public function buy()
//    {
//        $fp = fopen("lock.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantMarketItemService();
//            $service->buy("pro");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    //获取持仓
//    public function item()
//    {
//        $fp = fopen("lock.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantMarketItemService();
//            $service->item();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//
//    }
//
//    //获取卖出标准
//    public function sell()
//    {
//        $fp = fopen("lock.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantMarketItemService();
//            $service->sell("pro");
//        }
//        fclose($fp);
//    }
//
//    //获取是否盈利卖出
//    public function high()
//    {
//
//        $fp = fopen("lock.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantMarketItemService();
//            $service->goup("high");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//
//    }
//
//    //获取是否亏损补仓
//    public function low()
//    {
//        $fp = fopen("lock.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantMarketItemService();
//            $service->goup("low");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    //hedge
//    public function hedge_first()
//    {
//        $fp = fopen("lock1.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantHedgeItemService();
//            $service->first();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    public function hedge_buy()
//    {
//        $fp = fopen("lock1.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantHedgeItemService();
//            $service->buy("pro");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    public function hedge_sell()
//    {
//        $fp = fopen("lock1.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantHedgeItemService();
//            $service->sell("pro");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    public function hedge_item()
//    {
//        $fp = fopen("lock1.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantHedgeItemService();
//            $service->item();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    public function hedge_high()
//    {
//        $fp = fopen("lock1.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantHedgeItemService();
//            $service->goup("high");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//
//    public function loop_first()
//    {
//        $fp = fopen("lock2.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantLoopItemService();
//            $service->first();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    public function loop_dir()
//    {
//        $fp = fopen("lock2.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantLoopItemService();
//            $service->dir();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    public function loop_buy()
//    {
//        $fp = fopen("lock2.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantLoopItemService();
//            $service->buy("test");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//
//    public function loop_sell()
//    {
//        $fp = fopen("lock2.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantLoopItemService();
//            $service->sell("test");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    public function loop_item()
//    {
//        $fp = fopen("lock2.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantLoopItemService();
//            $service->item();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    //对冲策略
//    public function loop_low()
//    {
//        $fp = fopen("lock2.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantLoopItemService();
//            $service->goup("low");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    //对冲策略
//    public function loop_high()
//    {
//        $fp = fopen("lock2.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new QuantLoopItemService();
//            $service->goup("high");
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }




    //用户充值
    public function transfer(){
        $fp = fopen("transfer.txt", "w+");
        if (flock($fp, LOCK_EX)) {
            $service = new QuantTransferService();
            $service->init();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    //归集金额
    public function imputation(){
        $fp = fopen("imputation.txt", "w+");
        if (flock($fp, LOCK_EX)) {
//            $service = new QuantTransferService();
//            $service->imputation();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
    //用户提现
    public function withdraw(){
        $fp = fopen("withdraw.txt", "w+");
        if (flock($fp, LOCK_EX)) {
            $service = new QuantTransferService();
            $service->withdraw();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
    //组合策略收益
    public function profit(){
        $fp = fopen("profit.txt", "w+");
        if (flock($fp, LOCK_EX)) {
            $service = new TradeProfitService();
            $service->set_profit();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
    //马丁策略收益
//    public function market_profit(){
//        $fp = fopen("market_profit.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new TradeProfitService();
//            $service->set_market_profit();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }
//
//    //用户收益
//    public function user_profit(){
//        $fp = fopen("user_profit.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new TradeProfitService();
//            //$service->get_user_profit();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }

    //用户排行榜
//    public function user_ranking(){
//        $fp = fopen("user_ranking.txt", "w+");
//        if (flock($fp, LOCK_EX)) {
//            $service = new TradeProfitService();
//           // $service->ranking();
//            flock($fp, LOCK_UN);
//        }
//        fclose($fp);
//    }


    public function day_open_price(){
        $fp = fopen("day_open_price.txt", "w+");
        if (flock($fp, LOCK_EX)) {
            $service = new SymbolService();
            //$service->init_open_day();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }


}
