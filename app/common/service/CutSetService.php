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


use app\common\model\Member;
use app\common\model\PlatformMember;
use app\common\model\QuantSymbol;
use app\common\model\SingleLeverConfig;
use app\common\model\SingleLoopBuy;
use app\common\model\SingleLoopSell;
use app\common\model\SingleLoopSetup;
use app\common\model\SymbolBuyConfig;
use app\common\model\TradeLog;
use app\extend\Trade;
use think\cache;

/**
 * 对冲交易详情管理-服务类
 * @author 测试
 */
class CutSetService extends BaseService
{

//砍仓处理
    public function cut_all($sell_id)
    {
        //查询卖出盈利的信息
        $sell_info = SingleLoopSell::where(["id" => $sell_id, "is_deal" => 2, "is_cut" => 0])->find();
        if (!$sell_info) {
            //user_log("盈利的少1", 1);
            return [];
        }
        //查询盈利的50%
        $per = rand(3, 5) * 0.1;
        $sell_qbusdt = $sell_info["profit_usdt"] * $per;
        if ($sell_qbusdt < 0) {
            //user_log("盈利的少2", 1);
            return [];
        }
        //查询浮亏最多的仓位
        //if($sell_info[""])
        $list = SingleLoopBuy::where(["user_id" => $sell_info["user_id"], "platform_id" => $sell_info["platform_id"], "is_sell" => 0, "is_deal" => 2])->order("rounds", "asc")->select()->toArray();
        $is_singke_cut = $is_double_cut = $is_other_cut = $cut = $check_cut_loss = [];
        foreach ($list as $key => $value) {
            $setup_info = SingleLoopSetup::where(["id" => $value["setup_id"], "is_run" => 1])->find();
            if ($setup_info) {
                $loss = symbol_price_loss($value["platform_id"], $value["symbol"], $value["price"], $value["last_qb_token"], $value["open_type"]);
                //查询支持单向斩仓的条件
                if ($setup_info["is_singke_cut"] == 1 && $value["symbol"] == $sell_info["symbol"] && $value["open_type"] == $sell_info["open_type"]) {
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["cut_type"] = 1;
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["loss"][] = $loss;
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["setup_id"] = $setup_info["id"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["min_token"] = $setup_info["min_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["qb_token"] = $value["last_qb_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["last_qb_token"][] = $value["last_qb_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["buy_price"][] = $value["price"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["platform_id"] = $value["platform_id"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_singke_cut"] = $setup_info["is_singke_cut"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_double_cut"] = $setup_info["is_double_cut"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_other_cut"] = $setup_info["is_other_cut"];
                }
                //查询支持双向斩仓的条件
                if ($setup_info["is_double_cut"] == 1 && $value["symbol"] == $sell_info["symbol"] && $value["open_type"] != $sell_info["open_type"]) {
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["cut_type"] = 2;
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["loss"][] = $loss;
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["setup_id"] = $setup_info["id"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["min_token"] = $setup_info["min_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["qb_token"] = $setup_info["qb_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["last_qb_token"][] = $value["last_qb_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["buy_price"][] = $value["price"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["platform_id"] = $value["platform_id"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_singke_cut"] = $setup_info["is_singke_cut"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_double_cut"] = $setup_info["is_double_cut"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_other_cut"] = $setup_info["is_other_cut"];
                }
                //查询支持跨币种斩仓的条件
                if ($setup_info["is_other_cut"] == 1 && $value["symbol"] != $sell_info["symbol"]) {
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["cut_type"] = 3;
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["loss"][] = $loss;
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["setup_id"] = $setup_info["id"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["min_token"] = $setup_info["min_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["qb_token"] = $setup_info["qb_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["last_qb_token"][] = $value["last_qb_token"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["buy_price"][] = $value["price"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["platform_id"] = $value["platform_id"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_singke_cut"] = $setup_info["is_singke_cut"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_double_cut"] = $setup_info["is_double_cut"];
                    $check_cut[$value["symbol"] . "-" . $value["open_type"]]["is_other_cut"] = $setup_info["is_other_cut"];
                }
            }
        }
        if (!$check_cut) {
            //user_log("没有可砍仓的", 1);
            return [];
        }
        foreach ($check_cut as $key => &$value) {
            $sum = array_sum($value["loss"]);

            $last_qb_token = array_sum($value["last_qb_token"]);
            $buy_price = getBcRound(array_sum($value["buy_price"]) /count($value["buy_price"]),6);
            if ($sum > 0) {
                unset($check_cut[$key]);
            }
            //$value = [];
            $data = explode("-", $key);
            $value["loss"] = abs($sum);
            $value["last_qb_token"] = $last_qb_token;
            $value["buy_price"] = $buy_price;
            $value["symbol"] = $data[0];
            $value["open_type"] = $data[1];
        }
        //这里还有一层判断
        if (!$check_cut) {
            //user_log("没有可砍仓的", 1);
            return [];
        }
        $check_cut = arraySort($check_cut, "loss", "desc");
        //查询是否存在
        if (!$list) {
            //user_log("没有可砍仓的", 1);
            return [];
        }


        $check_cut = array_values($check_cut);
        //取第一个数据
        $max_check_one = $check_cut[0];
        $max_check_one["price"] = symbol_price($max_check_one["platform_id"], $max_check_one["symbol"]);

        $max_check_one["one_loss"] = abs($max_check_one["loss"]) / ($max_check_one["last_qb_token"] / $max_check_one["min_token"]);
        $max_check_one["one_price"] = $max_check_one["price"] * $max_check_one["min_token"];
        $point_number = getLen($max_check_one["min_token"]);
        $max_check_one["sell_qbusdt"] = $sell_qbusdt;
        $max_check_one["sell_token"] = getBcRound(bcdiv($sell_qbusdt, $max_check_one["one_price"], 8) * $max_check_one["min_token"], $point_number);


        //查询
        if($max_check_one["open_type"] ==2){
            $max_check_one["price_per"] = $max_check_one["price"] -$max_check_one["buy_price"];
        }else{
            $max_check_one["price_per"] = $max_check_one["buy_price"] - $max_check_one["price"];
        }


        $max_check_one["sell_token1"] =getBcRound(bcdiv($sell_qbusdt,abs($max_check_one["price_per"]),8) * $max_check_one["min_token"],$point_number);

        print_r($max_check_one);
        exit;


        //求公式
        //x *y - z*y = v //已知 x z v 求y
        //$max_check_one["sell_token"] = getBcRound(round($sell_qbusdt / $max_check_one["one_loss"]) * $max_check_one["min_token"], $point_number);



        $max_check_one["price1"]  =  $max_check_one["buy_price"] * $max_check_one["sell_token"];
        $max_check_one["price2"]  =  $max_check_one["price"] * $max_check_one["sell_token"];

        if($max_check_one["open_type"] ==2){
            $max_check_one["price3"] = $max_check_one["price2"] -$max_check_one["price1"];
        }else{
            $max_check_one["price3"] = $max_check_one["price1"] -$max_check_one["price2"];
        }


        print_r($max_check_one);
        exit;
        if ($max_check_one["sell_token"] <= 0) {
            return [];
        }
        $buy_service = new SingleLoopBuyService();
        //获取买的数据并且修改
        $buy_list = $buy_service->buy_next_data($max_check_one["setup_id"], $max_check_one["sell_token"], $point_number);
        //user_log("砍仓的当前价" . $max_check_one["price"] . "\r\n砍仓的交易对" . $max_check_one['symbol'] . "\r\n卖的交易对是" . $sell_info["symbol"] . "\r\n盈利了" . $sell_info["profit_usdt"] . "\r\n要亏损" . $sell_qbusdt . "\r\n亏损百分比是" . $per . "\r\n要卖的数量是" . $max_check_one["sell_token"] . "\r\n每个token亏损" . $max_check_one["one_loss"], "cut-" . $sell_info["user_id"]);
        if (!$buy_list) {
            //user_log("没有可砍仓的", 1);
            return [];
        }

        $model = new SingleLoopSell();

        if ($max_check_one["cut_type"] == 1) {
            $cut_type_name = "单向斩仓卖出成功,";
        }
        if ($max_check_one["cut_type"] == 2) {
            $cut_type_name = "双向斩仓卖出成功,";
        }
        if ($max_check_one["cut_type"] == 3) {
            $cut_type_name = "跨币种斩仓卖出成功,";
        }
        $message = $cut_type_name;
        //获取均价
        foreach ($buy_list as $key => $value) {
            $sell_all_arr[] = $value["qb_token"];
            $sell_all_qb_usdt_arr[] = $value["qb_usdt"];
            $sell_all_price_arr[] = $value["price"];
            $sell_all_id[] = $value["id"];
            //进入卖出
            $message .= " 第" . $value["rounds"] . "层剩余数量" . $value["do_last_qb_token"];
        }
        $sell_data = [
            'user_id' => $sell_info["user_id"],
            'symbol' => $max_check_one['symbol'],
            'buy_qb_token' => array_sum($sell_all_arr),
            'qb_token' => $max_check_one["sell_token"],//卖的数据
            'platform_id' => $max_check_one['platform_id'],
            "setup_id" => $max_check_one["setup_id"],
            'open_type' => $max_check_one["open_type"],
            'buy_qb_usdt' => array_sum($sell_all_qb_usdt_arr),
            'buy_price' => getBcRound(array_sum($sell_all_price_arr) / count($sell_all_price_arr), 8),
            // "buy_ids" => $sell_all_id,
            "is_cut" => 1,
            "cut_sell_id" => $sell_id,
            "cut_type_id" => $max_check_one["cut_type"]
        ];

    }

}
