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
class SingleSetService extends BaseService
{

//一键补仓 数据展示
    public function coverInfo($user_id)
    {
        $validate = getValidate([
            'setup_id' => 'require',
        ], [
            'setup_id.require' => '请选择平台',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $param = $this->input;
        $setup = SingleLoopSetup::where(['id' => $param['setup_id'], "user_id" => $user_id])->find();
        $price = symbol_price($setup['platform_id'], $setup['symbol']);
        $token = QuantSymbol::where('title', $setup['symbol'])->value('token');
        return $return = [
            'qb_token' => $setup['qb_token'],
            'token' => $token,
            'price' => $price,
            'qb_price' => round($setup['qb_token'] * $price, 6),
        ];
    }

    //自动开启补仓
    public function auto_open_down()
    {
        $list = SingleLoopSetup::where(["is_open_down" => 0, "is_run" => 1])->select();
        if ($list) {
            //print_r($list);
            foreach ($list as $key => $value) {
                //查询币种是否还有仓位如果没有仓位的话进行恢复
                $cache_config_key = "open_down_" . $value["id"] . "_" . date("Ymd");;
                $cache_config = cache($cache_config_key);
                $min_max = cache("open_down_setup_config" . $value["id"]);
                // print_r($min_max);
                //var_dump($min_max);
                if (!$min_max && !$cache_config) {
                    $value["type"] = 0;
                    trade_log("自动暂停补仓超过8个小时，已经恢复补仓", $value);
                    SingleLoopSetup::where(["id" => $value["id"]])->update(["is_open_down" => 1]);
                    continue;
                }
                if (!$cache_config && !$min_max) {
                    $value["type"] = 0;
                    SingleLoopSetup::where(["id" => $value["id"]])->update(["is_open_down" => 1]);
                    trade_log("恢复补仓成功", $value);
                }
                $count = SingleLoopBuy::where(["setup_id" => $value["id"], "is_sell" => 0, "is_deal" => 2])->count();
                if ($count == 0) {
                    SingleLoopSetup::where(["id" => $value["id"]])->update(["is_open_down" => 1]);
                    // cache("open_down_setup_config" . $value["id"],"");
                    $up_down_five_key = "up_down_five_" . $value["id"] . "_" . date("Ymd");
                    cache($up_down_five_key, NULL);
                }
            }
        }
    }


    //一键平仓全部
    public function sell_all($user_id)
    {
        //最后处理
        $validate = getValidate([
            'setup_id' => 'require',
            "qb_token" => 'require'
        ], [
            'setup_id.require' => '请选择平台',
            'qb_token.require' => '请选择数量'
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }

        if ($this->input["qb_token"] < 0) {
            return [
                "code" => 1,
                "msg" => "数量错误"
            ];
        }


        //查询最小token数
        $set_info = SingleLoopSetup::where(["id" => $this->input["setup_id"], "user_id" => $user_id])->find();
        $point_number = getLen($set_info["min_token"]);

        //查询当前没有卖的一共有多少个
        $buy_service = new SingleLoopBuyService();
        $buy_all_qb_token = SingleLoopBuy::where(["setup_id" => $this->input["setup_id"], "user_id" => $user_id, "is_deal" => 2, "is_sell" => 0])->sum("qb_token");
        $buy_list = $buy_service->sell_every($this->input["setup_id"], $this->input["qb_token"]);
        if ($buy_list) {
            //获取数量
//            $count = count($buy_list);
//            //$prices_arr = [];
            //获取均价
//            foreach ($buy_list as $key => $value) {
//                $sell_all_qb_usdt_arr[] = $value["qb_usdt"];
//                $sell_all_price_arr[] = $value["price"];
//                $sell_all_id[] = $value["id"];
//            }
            $is_run = 1;
            $this->input["qb_token"] = getBcRound($this->input["qb_token"], $point_number);
            //查询剩余数量和
            if (bccomp($this->input["qb_token"], $buy_all_qb_token, $point_number) == 0) {
                $is_run = 0;
            }
            $message = "手动平仓";
            $buy_list_desc = $buy_service->sell_every($this->input["setup_id"], $this->input["qb_token"], "desc");
            foreach ($buy_list_desc as $key => $value) {
                SingleLoopBuy::where(["id" => $value["id"]])->update(["last_qb_token" => $value["do_last_qb_token"], "is_sell" => $value["is_sell"]]);
                $message .= " 第" . $value["rounds"] . "层剩余数量" . $value["do_last_qb_token"];
                if ($value["do_last_qb_token"] != $value["last_qb_token"] || $value["is_sell"] == 0) {
                    $sell_all_qb_usdt_arr[] = $value["qb_usdt"];
                    $sell_all_price_arr[] = $value["price"];
                }
            }
            $next_data = [
                'user_id' => $user_id,
                'symbol' => $buy_list_desc[0]['symbol'],
                'buy_qb_token' => $this->input["qb_token"],
                'qb_token' => $this->input["qb_token"],//卖的数据
                'platform_id' => $buy_list_desc[0]['platform_id'],
                "setup_id" => $buy_list_desc[0]["setup_id"],
                'open_type' => $buy_list_desc[0]["open_type"],
                'buy_qb_usdt' => array_sum($sell_all_qb_usdt_arr),
                'buy_price' => getBcRound(array_sum($sell_all_price_arr) / count($sell_all_price_arr), 8),
                "is_auto" => 0,
            ];
            $model = new SingleLoopSell();
            if ($is_run == 0) {
                SingleLoopSetup::where(["id" => $this->input["setup_id"]])->update(["is_run" => 0, "is_open_down" => 1]);
            }
            $model->startTrans();
            try {
                $createInfo = SingleLoopSell::create($next_data);
                if ($createInfo) {
                    $buy_list[0]["type"] = 2;

                }
                $model->commit();
                $this->sell_one($this->input["setup_id"], $createInfo["id"], $message);
            } catch (\Exception $e) {
                user_log($e->getMessage(), 1);
                $model->rollback();
            }
        }
        return true;
    }

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

        $per = 3 * 0.1;
        $sell_qbusdt = $sell_info["profit_usdt"] * $per;
        if ($sell_qbusdt < 0) {
            //user_log("盈利的少2", 1);
            return [];
        }
        //查询浮亏最多的仓位
        //if($sell_info[""])

        //查询层级大于两个的

        $setup_id_list = SingleLoopBuy::where(["is_sell" => 0, "is_deal" => 2, "user_id" => $sell_info["user_id"], "platform_id" => $sell_info["platform_id"]])->where("rounds", ">", 1)->group("setup_id")->column("setup_id");
        if (!$setup_id_list) {
            user_log("卖" . $sell_id . "没有仓位大于两个的,过滤斩仓", $sell_id);
            return [];
        }
        $list = SingleLoopBuy::whereIn("setup_id", $setup_id_list)->order("rounds", "asc")->select()->toArray();
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
            $buy_price = getBcRound(array_sum($value["buy_price"]) / count($value["buy_price"]), 6);
            if ($sum > 0) {
                unset($check_cut[$key]);
            }
            //$value = [];
            $data = explode("-", $key);
            $value["buy_price"] = $buy_price;
            $value["loss"] = abs($sum);
            $value["last_qb_token"] = $last_qb_token;
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
        if (in_array($max_check_one["symbol"], ["BTC/USDT", "ETH/USDT"])) {
            $per = 2 * 0.1;
            $sell_qbusdt = $sell_info["profit_usdt"] * $per;
        }
        $max_check_one["price"] = symbol_price($max_check_one["platform_id"], $max_check_one["symbol"]);

        $max_check_one["one_loss"] = abs($max_check_one["loss"]) / ($max_check_one["last_qb_token"] / $max_check_one["min_token"]);
        $max_check_one["one_price"] = $max_check_one["price"] * $max_check_one["min_token"];
        $point_number = getLen($max_check_one["min_token"]);
        $max_check_one["sell_qbusdt"] = $sell_qbusdt;
        //$max_check_one["sell_token"] = getBcRound(bcdiv($sell_qbusdt, $max_check_one["one_price"], 8) * $max_check_one["min_token"], $point_number);
        $max_check_one["sell_token1"] = getBcRound(round($sell_qbusdt / $max_check_one["one_loss"]) * $max_check_one["min_token"], $point_number);

//        if ($max_check_one["sell_token"] <= 0) {
//            return [];
//        }

        if ($max_check_one["open_type"] == 2) {
            $max_check_one["price_per"] = abs($max_check_one["price"] - $max_check_one["buy_price"]);
        } else {
            $max_check_one["price_per"] = abs($max_check_one["buy_price"] - $max_check_one["price"]);
        }
        $max_check_one["sell_token"] = getBcRound(bcdiv($max_check_one["sell_qbusdt"], $max_check_one["price_per"], 8), $point_number);
        if ($max_check_one["sell_token"] <= 0) {
            return [];
        }

        $buy_service = new SingleLoopBuyService();
        //获取买的数据并且修改

        //查询当前setup_id剩余多少没有卖的
//        $buy_count = SingleLoopBuy::where(["setup_id" => $max_check_one["setup_id"], "is_deal" => 2, "is_sell" => 0])->count();
//        if ($buy_count > 1) {
//            $cut_count = 0;
//        } else {
//            $cut_count = 2;
//        }
        $buy_list = $buy_service->sell_every($max_check_one["setup_id"], $max_check_one["sell_token"]);
        user_log("\r\n买入均价" . $max_check_one["buy_price"] . "\r\n砍仓的当前价" . $max_check_one["price"] . "\r\n砍仓的交易对" . $max_check_one['symbol'] . "\r\n卖的交易对是" . $sell_info["symbol"] . "\r\n盈利了" . $sell_info["profit_usdt"] . "\r\n要亏损" . $sell_qbusdt . "\r\n亏损百分比是" . $per . "\r\n现在要卖的数量是" . $max_check_one["sell_token"] . "\r\n历史要卖的数量是" . $max_check_one["sell_token1"] . "\r\n每个token亏损" . $max_check_one["one_loss"], "cut-" . $sell_info["user_id"]);
        if (!$buy_list) {
            //user_log("没有可砍仓的", 1);
            return [];
        }

        $model = new SingleLoopSell();
        $model->startTrans();
        try {
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
                SingleLoopBuy::where(["id" => $value["id"]])->update(["last_qb_token" => $value["do_last_qb_token"], "is_sell" => $value["is_sell"]]);
                $message .= " 第" . $value["rounds"] . "层剩余数量" . $value["do_last_qb_token"];
//                if ($value["do_last_qb_token"] != $value["last_qb_token"] || $value["is_sell"] == 0) {
//                    $sell_all_qb_usdt_arr[] = $value["qb_usdt"];
//                    $sell_all_price_arr[] = $value["price"];
//                }
            }


//            $model->commit();
            $trade_extend = new Trade();
            $trade_param = [
                "user_id" => $sell_info["user_id"],
                "symbol" => $max_check_one["symbol"],
                "platform_id" => $max_check_one["platform_id"],
                "qb_token" => $max_check_one["sell_token"],
                "open_type" => $max_check_one["open_type"],
            ];
            $result = $trade_extend->ssell($trade_param);
            if (isset($result["result"]['id'])) {
                try {
                    $sell_data = [
                        'user_id' => $sell_info["user_id"],
                        'symbol' => $max_check_one['symbol'],
                        'buy_qb_token' => array_sum($sell_all_arr),
                        'qb_token' => $max_check_one["sell_token"],//卖的数据
                        'platform_id' => $max_check_one['platform_id'],
                        "setup_id" => $max_check_one["setup_id"],
                        'open_type' => $max_check_one["open_type"],
                        'buy_qb_usdt' => array_sum($sell_all_qb_usdt_arr),
                        'buy_price' => $max_check_one["buy_price"],//getBcRound(array_sum($sell_all_price_arr) / count($sell_all_price_arr), 8),
                        // "buy_ids" => $sell_all_id,
                        "is_cut" => 1,
                        "cut_sell_id" => $sell_id,
                        "cut_type_id" => $max_check_one["cut_type"]
                    ];

                    if ($max_check_one["open_type"] == 2) {
                        $result["update_sell_data"]["profit_usdt"] = $sell_data["qb_token"] * $result["update_sell_data"]["price"] - $sell_data["qb_token"] * $sell_data["buy_price"];
                    } else {
                        $result["update_sell_data"]["profit_usdt"] = $sell_data["qb_token"] * $sell_data["buy_price"] - $sell_data["qb_token"] * $result["update_sell_data"]["price"];
                    }

                    $sell_data["qb_fee"] = $result["update_sell_data"]["qb_fee"];
                    $sell_data["order_id"] = $result["update_sell_data"]["order_id"];
                    $sell_data["qb_usdt"] = $result["update_sell_data"]["qb_usdt"];
                    $sell_data["price"] = $result["update_sell_data"]["price"];
                    $sell_data["is_deal"] = 2;
                    $sell_data["profit_usdt"] = $result["update_sell_data"]["profit_usdt"];
                    $createInfo = SingleLoopSell::create($sell_data);
                    $max_check_one["type"] = 2;
                    trade_log($message . " 盈利:" . $result["update_sell_data"]["profit_usdt"] . " 成交USDT:" . $result["update_sell_data"]["qb_usdt"] . " 成交数量:" . $sell_data["qb_token"] . " 成交行情价:" . $result["update_sell_data"]["price"], $sell_data);
                    //初始化数据
                    $model->commit();
                    buy_sort($max_check_one["setup_id"]);
                } catch (\Exception $e) {
                    user_log("卖" . $e->getMessage(), "a1");
                    // 事务回滚
                    $model->rollback();
                }
            } else {
                $max_check_one["type"] = 2;
                trade_log($message . " 斩仓失败,错误内容:" . $result["result"]["message"], $max_check_one);
            }
        } catch (\Exception $e) {
            user_log($e->getMessage(), "a2");
            $model->rollback();
            return [];
        }
    }

    //初始化
    public function setup($param)
    {
        //获取要购买的金额
        $buy_per = 0.001;
        $now_price = symbol_price($param["platform_id"], $param["symbol"]);
        if ($now_price == 0) {
            return [
                "code" => 1,
                "msg" => "非法操作"
            ];
        }
        $param["buy_usdt"] = $now_price * $param["buy_token"];

        $balance = $param["balance"];


//        if ($data_member["qb_usdt_point"] < get_config('min_usdt', 1)) {
//            return [
//                "code" => 2,
//                "msg" => "当前点卡额度必须大于" . get_config('min_usdt', 1) . '才可开启机器人'
//            ];
//        }


        $balance = $balance["total"];
        //查询是否有限制倍数购买比例
        $buy_per_info = SymbolBuyConfig::where(["title" => $param["symbol"], "lever" => $param["lever"]])->find();
        if ($buy_per_info) {
            $buy_per = $buy_per_info["buy_per"];
        }
        //最大购买保证金
        $param["buy_balance_max_bail"] = $balance * $buy_per;


        // $re = bcmod((string)$param["buy_token"], (string)$param["min_buy_token"], 4);

        //获取购买的保证金
        // $param["buy_usdt_bail"] = getBcRound($param["buy_usdt"] / $param["lever"], 6);
//        if ($param["buy_usdt_bail"] > $param["buy_balance_max_bail"]) {
//            return [
//                "code" => 1,
//                "msg" => $param["buy_usdt_bail"] . "超出购买金额" . $param["buy_balance_max_bail"]
//            ];
//        }
        //获取当前币种购买限制数
        $symbol = QuantSymbol::where(["title" => $param["symbol"]])->find();
        /*if ($param["platform_id"] == 2) {
            $param["min_buy_usdt"] = round($now_price * $symbol["ct_val"], $symbol["decimal"]);
        } else {
            $param["min_buy_usdt"] = round($now_price * $symbol["bh_min_price"], $symbol["decimal"]);
        }
        if ($param["buy_usdt"] < $param["min_buy_usdt"]) {
            return [
                "code" => 1,
                "msg" => "最小购买" . $param["min_buy_usdt"]
            ];
        }*/
        if ($param["platform_id"] == 2) {
            $param["min_buy_token"] = $symbol["ct_val"];
        } else {
            $param["min_buy_token"] = $symbol["bh_min_price"];
        }

        if ($param["buy_token"] < $param["min_buy_token"]) {
            return [
                "code" => 1,
                "msg" => "最小购买" . $param["min_buy_token"]
            ];
        }

        //余额
        //15*50
        //查询当前余额750 最多购买750
        ;
        //750 / $now_price
        $param["min_buy_token"] = floatval($param["min_buy_token"]);

        //$token_lan = getLen($param["min_buy_token"]);

        $param["max_token"] = getBcRound($balance * $buy_per * $param["lever"] / symbol_price($param["platform_id"], $param["symbol"]), 8);
        $re = bcmod((string)$param["max_token"], (string)$param["min_buy_token"], 4);
        $param["max_token"] = $param["max_token"] - $re;
//        echo $token_lan;
//        echo $param["min_buy_token"];
//        if($token_lan==0){
//            $param["buy_token"] = round(750 / $now_price);
//        }else{
        //$param["buy_token"] = getBcRound($param["buy_price"] / $now_price, $token_lan);
        //echo $param["min_buy_token"];
        //$re = $param["buy_token"]%$param["min_buy_token"];
        //求余处理
        $re = bcmod((string)$param["buy_token"], (string)$param["min_buy_token"], 4);
        $param["buy_token"] = $param["buy_token"] - $re;
        if ($param["buy_token"] > $param["max_token"]) {
            return [
                "code" => 1,
                "msg" => "超出购买token数",
            ];
        }
        $setup = SingleLoopSetup::where(["user_id" => $param["user_id"], "symbol" => $param["symbol"], "open_type" => $param["open_type"], "platform_id" => $param["platform_id"]])->find();
        //print_r($setup);
        if (!$setup) {
            $trade = new Trade();
            $leverage = $trade->set_leverage([
                "user_id" => $param["user_id"],
                "platform_id" => $param["platform_id"],
                "symbol" => $param["symbol"],
                "lever" => $param["lever"],
            ]);
            if ($leverage["code"] == 1) {
                return $leverage;
            }
            SingleLoopSetup::create(
                [
                    "user_id" => $param["user_id"],
                    "symbol" => $param["symbol"],
                    "lever" => $param["lever"],
                    //"qb_usdt" => $param["buy_usdt"],//最小购买数量
                    "qb_token" => $param["buy_token"],
                    "is_run" => 1,
                    "is_loop" => 1,
                    "platform_id" => $param["platform_id"],
                    "open_type" => $param["open_type"],
                    "type_id" => $param["type_id"],//购买赔率
                    "min_token" => $param["min_buy_token"],
                    "is_singke_cut" => $param["is_singke_cut"],
                    "is_double_cut" => $param["is_double_cut"],
                    "is_other_cut" => $param["is_other_cut"],
                    "rounds" => $param["rounds"]
                ]
            );
            // echo $createInfo["id"];
            //$this->buy($createInfo["id"]);
        } else {
            $updateData = [];
            if ($setup["is_run"] != 0) {
                if ($setup["lever"] != $param["lever"]) {
                    return [
                        "code" => 1,
                        "msg" => "策略已启动，请停止后尝试开启策略",
                    ];
                }
            } else {
                $trade = new Trade();
                $leverage = $trade->set_leverage([
                    "user_id" => $param["user_id"],
                    "platform_id" => $param["platform_id"],
                    "symbol" => $param["symbol"],
                    "lever" => $param["lever"],
                ]);
                if ($leverage["code"] == 1) {
                    return $leverage;
                }
            }
            if ($setup["type_id"] != $param["type_id"]) {
                $buy_list = SingleLoopBuy::where(["setup_id" => $setup["id"], "is_sell" => 0, "is_deal" => 2])->select();
                if ($buy_list) {
                    foreach ($buy_list as $key => $value) {
                        $setup_per = setup_per($setup["id"], $value["price"], $value["rounds"], $param["type_id"]);
                        $update["up_stop_per"] = $setup_per["up_stop_per"];
                        $update["down_stop_per"] = $setup_per["down_stop_per"];
                        $update["up_stop_price"] = $setup_per["up_stop_price"];
                        $update["down_stop_price"] = $setup_per["down_stop_price"];
                        SingleLoopBuy::where(["id" => $value["id"]])->update($update);
                    }
                }
            }
            $updateData["type_id"] = $param["type_id"];
            $updateData["rounds"] = $param["rounds"];
            $updateData["is_singke_cut"] = $param["is_singke_cut"];
            $updateData["is_double_cut"] = $param["is_double_cut"];
            $updateData["is_other_cut"] = $param["is_other_cut"];
            $updateData["qb_token"] = $param["buy_token"];
            if ($setup["is_run"] == 0) {
                $updateData["lever"] = $param["lever"];
            }
            SingleLoopSetup::where(["id" => $setup["id"]])->update($updateData);
        }
        return true;
    }


    public function low($user_id = [])
    {
        $query = SingleLoopSetup::where(["is_run" => 1, "is_open_down" => 1]);
        if ($user_id) {
            $query->whereIn("user_id", $user_id);
        }
        $setup_list = $query->select();
        foreach ($setup_list as $key => $value) {
            $now_price = symbol_price($value["platform_id"], $value['symbol']);
            $cache_config_key = "open_down_" . $value["id"] . "_" . date("Ymd");
            $up_down_five_key = "up_down_five_" . $value["id"] . "_" . date("Ymd");
            if (!Member::check_usdt($value["user_id"], 1)) {
                trade_log("您的服务费剩余不足，已停止机器人", $value);
                continue;
            }
            $is_buy = 0;
            $buy_info = SingleLoopBuy::where(["setup_id" => $value["id"], "is_deal" => 0, "is_sell" => 0])->order("rounds", "desc")->find();
            if ($buy_info) {
                $this->buy_one($value["id"], $buy_info["id"], "自动补仓");
            }
            $buy_info = SingleLoopBuy::where(["setup_id" => $value["id"], "is_deal" => 2, "is_sell" => 0])->order("rounds", "desc")->find();
            if ($buy_info) {
                $rounds = $buy_info["rounds"] + 1;
                if ($value['open_type'] == 3 && $now_price >= $buy_info['down_stop_price']) {
                    $is_buy = 1;
                }
                if ($value['open_type'] == 2 && $now_price <= $buy_info['down_stop_price']) {
                    $is_buy = 1;
                }
            } else {
                $rounds = 1;
                $is_buy = 1;
                if ($value["is_loop"] == 0) {
                    continue;
                }
            }
            //user_log($is_buy, str_replace("/", "", $value["symbol"]) . $value["open_type"] . "asd");

            if ($is_buy == 1) {
                buy_sort($value["id"]);
                $cache_config = cache($cache_config_key);
                if ($cache_config && $cache_config >= 3) {
                    cache($cache_config_key, 1, 86400);
                    SingleLoopSetup::where(["id" => $value["id"]])->update(["is_open_down" => 0]);
                    trade_log("因补仓超过3次，暂停补仓", $value);
                    continue;
                }
                $min_max = cache($up_down_five_key);
                $is_open_down = 1;
                if ($min_max) {
                    //判断是否需要暂停补仓8个小时
                    if ($value["open_type"] == 2 && $now_price <= $min_max["min_price"] && $min_max["min_price"] != 0) {
                        $is_open_down = 0;
                    }
                    if ($value["open_type"] == 3 && $now_price >= $min_max["max_price"] && $min_max["max_price"] != 0) {
                        $is_open_down = 0;
                    }
                }
                if ($is_open_down == 0) {
                    SingleLoopSetup::where(["id" => $value["id"]])->update(["is_open_down" => 0]);
                    trade_log("24小时内上下浮动超过5%，暂停策略", $value);
                    user_log("24小时内上下浮动超过5%，暂停策略 当天最高价".$min_max["max_price"].",最低价".$min_max["min_price"],$value["setup_id"]);
                    cache("open_down_setup_config" . $value["id"], 1, 28800);
                    continue;
                }
                $data = [
                    'user_id' => $value['user_id'],
                    'symbol' => $value['symbol'],
                    'qb_token' => $value["qb_token"],
                    'last_qb_token' => $value["qb_token"],
                    'platform_id' => $value['platform_id'],
                    'lever' => $value['lever'], //合约有用
                    'open_type' => $value['open_type'], //开仓方向合约有用
                    "setup_id" => $value["id"],
                    "rounds" => $rounds,
                    "is_auto" => 1,
                ];
                $createInfo = SingleLoopBuy::create($data);
                buy_sort($value["id"]);
                if ($rounds == 1) {
                    $message = "首仓建仓成功";
                } else {
                    $message = "自动补仓成功";
                }
                $this->buy_one($value["id"], $createInfo["id"], $message . "(" . $rounds . ") ");
            }
        }
        return true;
    }


//    public function low1($user_id = [])
//    {
//        $query = SingleLoopSetup::where(["is_run" => 1, "is_open_down" => 1]);
//        if ($user_id) {
//            $query->whereIn("user_id", $user_id);
//        }
//        $setup_list = $query->select();
//        foreach ($setup_list as $key => $value) {
//            $row_buy = SingleLoopBuy::where(["setup_id" => $value["id"], "is_deal" => 2, "is_auto" => 1])->order("id", "desc")->find();
//            if ($row_buy) {
//                $cache_config_key = "open_down_" . $value["id"] . "_" . date("Ymd");
//                $up_down_five_key = "up_down_five_" . $value["id"] . "_" . date("Ymd");
//                if (!Member::check_usdt($value["user_id"], 1)) {
//                    trade_log("您的服务费剩余不足，已停止机器人", $value);
//                    continue;
//                }
//                $now_price = symbol_price($value["platform_id"], $value['symbol']);
//                user_log($now_price, "1");
//                $is_buy = 0;
//                if ($row_buy["is_sell"] == 0) {
//                    $rounds = $row_buy["rounds"] + 1;
//                    if ($value['open_type'] == 3 && $now_price >= $row_buy['down_stop_price']) {
//                        $is_buy = 1;
//                    }
//                    if ($value['open_type'] == 2 && $now_price <= $row_buy['down_stop_price']) {
//                        $is_buy = 1;
//                    }
//                } else {
//                    //取这个没有卖出的上一单
//                    $last_info = SingleLoopBuy::where(["setup_id" => $value["id"], "is_deal" => 2, "is_sell" => 0, "is_auto" => 1])->order("rounds", "desc")->find();
//                    if ($last_info) {
//                        $row_buy['down_stop_price'] = $last_info["down_stop_price"];
//                        if ($value['open_type'] == 3 && $now_price >= $row_buy['down_stop_price']) {
//                            $is_buy = 1;
//                        }
//                        if ($value['open_type'] == 2 && $now_price <= $row_buy['down_stop_price']) {
//                            $is_buy = 1;
//                        }
//                        $rounds = $last_info["rounds"] + 1;
//                    } else {
//                        $is_buy = 1;
//                        $rounds = 1;
//                        if ($value["is_loop"] == 0) {
//                            continue;
//                        }
//                    }
//                    //user_log($rounds,str_replace("/","",$value["symbol"]).$value["open_type"]);
//
//
////                    if ($row_buy["rounds"] > 1) {
////                        if ($value['open_type'] == 3 && $now_price >= $row_buy['down_stop_price']) {
////                            $is_buy = 1;
////                        }
////                        if ($value['open_type'] == 2 && $now_price <= $row_buy['down_stop_price']) {
////                            $is_buy = 1;
////                        }
////                    } else {
////                        $is_buy = 1;
////                        $rounds = 1;
////                        if ($value["is_loop"] == 0) {
////                            continue;
////                        }
////                    }
//                }
//                //user_log($rounds,str_replace("/","",$value["symbol"]).$value["open_type"]);
//                //user_log($row_buy['down_stop_price'],str_replace("/","",$value["symbol"]).$value["open_type"]);
//                //user_log($now_price,str_replace("/","",$value["symbol"]).$value["open_type"]);
//                //user_log($is_buy,str_replace("/","",$value["symbol"]).$value["open_type"]);
//                if ($is_buy == 1) {
//                    buy_sort($value["id"]);
//                    $cache_config = cache($cache_config_key);
//                    if ($cache_config && $cache_config >= 3) {
//                        cache($cache_config_key, 1, 86400);
//                        SingleLoopSetup::where(["id" => $value["id"]])->update(["is_open_down" => 0]);
//                        trade_log("因补仓超过3次，暂停补仓", $value);
//                        continue;
//                    }
//                    $min_max = cache($up_down_five_key);
//                    $is_open_down = 1;
//                    if ($min_max) {
//                        //判断是否需要暂停补仓8个小时
//                        if ($value["open_type"] == 2 && $now_price <= $min_max["min_price"] && $min_max["min_price"] != 0) {
//                            $is_open_down = 0;
//                        }
//                        if ($value["open_type"] == 3 && $now_price >= $min_max["max_price"] && $min_max["max_price"] != 0) {
//                            $is_open_down = 0;
//                        }
//                    }
//                    if ($is_open_down == 0) {
//                        cache("open_down_setup_config" . $value["id"], 1, 28800);
//                        SingleLoopSetup::where(["id" => $value["id"]])->update(["is_open_down" => 0]);
//                        trade_log("24小时内上下浮动超过5%，暂停策略-", $value);
//                        continue;
//                    }
//                    $data = [
//                        'user_id' => $value['user_id'],
//                        'symbol' => $value['symbol'],
//                        'qb_token' => $value["qb_token"],
//                        'last_qb_token' => $value["qb_token"],
//                        'platform_id' => $value['platform_id'],
//                        'lever' => $value['lever'], //合约有用
//                        'open_type' => $value['open_type'], //开仓方向合约有用
//                        "setup_id" => $value["id"],
//                        "rounds" => $rounds,
//                        "is_auto" => 1,
//                    ];
//                    $createInfo = SingleLoopBuy::create($data);
//                    buy_sort($value["id"]);
//                    $this->buy_one($value["id"], $createInfo["id"], "补仓 仓位:" . $rounds . " ");
//                }
//            }
//        }
//        return true;
//    }


    public function high($user_id = [])
    {
        $query = SingleLoopSetup::where(["is_run" => 1]);
        if ($user_id) {
            $query->whereIn("user_id", $user_id);
        }
        $setup_list = $query->column("id");
        if ($setup_list) {
            $query = SingleLoopBuy::where(["is_sell" => 0, "is_deal" => 2])->whereIn("setup_id", $setup_list);
            $list = $query->order("id", "asc")->select();
            foreach ($list as $key => $value) {
                $now_price = symbol_price($value["platform_id"], $value['symbol']);
                if ($now_price == 0) {
                    continue;
                }
                $is_sell = 0;
                if ($value['open_type'] == 3) {
                    if ($value['up_stop_price'] >= $now_price) {
                        $is_sell = 1;
                    }
                } else {
                    if ($value['up_stop_price'] <= $now_price) {
                        $is_sell = 1;
                    }
                }
                if ($is_sell == 1) {
                    $data = [
                        'user_id' => $value['user_id'],
                        'symbol' => $value['symbol'],
                        'buy_qb_token' => $value['qb_token'],
                        'qb_token' => $value['last_qb_token'],//卖的数据
                        'platform_id' => $value['platform_id'],
                        "setup_id" => $value["setup_id"],
                        'open_type' => $value["open_type"],
                        'buy_qb_usdt' => $value['qb_usdt'],
                        'buy_price' => $value['price'],
                        "buy_id" => $value['id']
                    ];
                    //开启事务
                    $model = new SingleLoopSell();
                    $model->startTrans();
                    try {
                        $createSell = SingleLoopSell::create($data);
                        SingleLoopBuy::where(["id" => $value["id"]])->update(["is_sell" => 3]);
                        $value["type"] = 2;
                        $model->commit();
                        $this->sell_one($value["setup_id"], $createSell["id"], "");
                    } catch (\Exception $e) {
                        // 事务回滚
                        $model->rollback();
                    }
                }
            }
        }
    }


    //开仓
    public function open($user_id = [])
    {
//        $query = SingleLoopSetup::where(["is_run" => 1]);
//        if ($user_id) {
//            $query->whereIn("user_id", $user_id);
//        }
//
//        $setup_list = $query->select();
//        if ($setup_list) {
//            foreach ($setup_list as $key => $value) {
//                $row_market = SingleLoopBuy::where(["setup_id" => $value["id"]])->where("is_deal", "<>", 3)->find();
//                if (!$row_market) {
//                    if (Member::check_usdt($value["user_id"], 1)) {
//                        $data = [
//                            'user_id' => $value['user_id'],
//                            'symbol' => $value['symbol'],
//                            'qb_token' => $value['qb_token'],
//                            'last_qb_token' => $value['qb_token'],
//                            'platform_id' => $value['platform_id'],
//                            'lever' => $value['lever'], //合约有用
//                            'open_type' => $value['open_type'], //开仓方向合约有用
//                            "setup_id" => $value["id"]
//                        ];
//                        $createBuy = SingleLoopBuy::create($data);
//                        $this->buy_one($value["id"], $createBuy["id"], "首次开仓");
////                    }
//                    } else {
//                        SingleLoopSetup::where(["id" => $value["id"]])->update(["is_run" => 0]);
//                        //余额不足 记录日志 TODU
//                        trade_log("您的服务费剩余不足，已停止机器人", $value);
//                    }
//                }
//            }
//        }


        return true;
    }

    //一键补仓
    public function cover($user_id)
    {
        $validate = getValidate([
            'setup_id' => 'require',
        ], [
            'setup_id.require' => '请选择平台',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $param = $this->input;
        $setup = SingleLoopSetup::where(['id' => $param['setup_id'], "user_id" => $user_id])->find();

        if ($setup["is_run"] == 0 || $setup["is_run"] == 2) {
            return [
                "code" => 1,
                "msg" => "策略没有开启，请开启策略",
            ];
        }
        //$buy_usdt = $setup["qb_source"];
        //查询是否有上级订单
        $row_buy = SingleLoopBuy::where(["setup_id" => $param["setup_id"], "user_id" => $user_id])->where("is_deal", "=", 2)->whereIn("is_sell", [0])->order("id", "desc")->find();
        if (!$row_buy) {
            $rounds = 1;
        } else {
            $rounds = $row_buy["rounds"] + 1;
        }
        $data = [
            'user_id' => $setup['user_id'],
            'symbol' => $setup['symbol'],
            'qb_token' => $setup['qb_token'],
            'last_qb_token' => $setup['qb_token'],
            'platform_id' => $setup['platform_id'],
            'lever' => $setup['lever'], //合约有用
            'open_type' => $setup['open_type'], //开仓方向合约有用
            "setup_id" => $setup["id"],
            "rounds" => $rounds,
            "is_auto" => 0,
        ];
        $open_down_time_key = "open_down_" . $setup["id"] . "_" . date("Ymd");
        $cache_config = cache($open_down_time_key);
        user_log("当前补仓次数" . $cache_config, $setup["id"]);
        $createBuy = SingleLoopBuy::create($data);
        $this->buy_one($setup["id"], $createBuy["id"], "手动一键补仓");
        return true;
    }


    public function buy_one($setup_id, $id, $message = "")
    {
        //$lock = "lock/lock_buy_" . $setup_id;
        $open_down_time_key = "open_down_" . $setup_id . "_" . date("Ymd");
        //记录缓存（涨幅百分之5 ）
        $up_down_five_key = "up_down_five_" . $setup_id . "_" . date("Ymd");
        $trade_extend = new Trade();
        $return = true;
        //$file = fopen($lock, "w+");
        //if (flock($file, LOCK_EX | LOCK_NB)) {
        $info = SingleLoopBuy::where(["id" => $id])->find();
        $trade_param = [
            "user_id" => $info["user_id"],
            "symbol" => $info["symbol"],
            "platform_id" => $info["platform_id"],
            "qb_token" => $info["qb_token"],
            "open_type" => $info["open_type"],
        ];
        $result = $trade_extend->sbuy($trade_param);
        if (isset($result["result"]["id"])) {
            $model = new SingleLoopBuy();
            $model->startTrans();
            try {
                $setup_per = setup_per($info["setup_id"], $result["update_buy_data"]["price"], $info['rounds']);
                $result["update_buy_data"]["up_stop_per"] = $setup_per["up_stop_per"];
                $result["update_buy_data"]["down_stop_per"] = $setup_per["down_stop_per"];
                $result["update_buy_data"]["up_stop_price"] = $setup_per["up_stop_price"];
                $result["update_buy_data"]["down_stop_price"] = $setup_per["down_stop_price"];
                //记录缓存（涨幅百分之5 ）
                if (!cache($up_down_five_key)) {
                    $max_price = $result["update_buy_data"]["price"] + $result["update_buy_data"]["price"] * 0.05;
                    $min_price = $result["update_buy_data"]["price"] - $result["update_buy_data"]["price"] * 0.05;
                    cache($up_down_five_key, ["max_price" => $max_price, "min_price" => $min_price]);
                }
                SingleLoopBuy::where(["id" => $info["id"]])->update($result["update_buy_data"]);
                $cache_config = cache($open_down_time_key);
                if (!$cache_config) {
                    cache($open_down_time_key, 1);
                } else {
                    cache($open_down_time_key, (int)$cache_config + 1);
                }
                trade_log($message . "，成交USDT:" . $result["update_buy_data"]["qb_usdt"] . " 成交数量:" . $info["qb_token"] . " 成交价格:" . $result["update_buy_data"]["price"], $info);
                $model->commit();
                //初始化数据
                buy_sort($info["setup_id"]);
            } catch (\Exception $e) {
                user_log("买" . $e->getMessage(), $info["setup_id"]);
                // 事务回滚
                $model->rollback();
                $return = false;
            }
        } else {
            SingleLoopSetup::where(["id" => $info["setup_id"]])->update(["is_run" => 2]);
            SingleLoopBuy::where(["id" => $info["id"]])->update(["is_deal" => 3, "error" => $result["result"]["message"], "param" => json_encode($result["param"])]);
            trade_log($message . " 购买失败,错误内容:" . $result["result"]["message"], $info);
            $return = false;
        }

        return $return;

//            flock($file, LOCK_UN);//解锁
//            fclose($file);
//            return $return;
//        } else {
//            fclose($file);
//            return false;
//        }


    }

    public function sell_one($setup_id, $id, $message = "")
    {
        //$lock = "lock/lock_sell_" . $setup_id;
        $open_down_time_key = "open_down_" . $setup_id . "_" . date("Ymd");
        $trade_extend = new Trade();
        $return = true;
        //$file = fopen($lock, "w+");
        //if (flock($file, LOCK_EX | LOCK_NB)) {
        $info = SingleLoopSell::where(["id" => $id])->find();
        $trade_param = [
            "user_id" => $info["user_id"],
            "symbol" => $info["symbol"],
            "platform_id" => $info["platform_id"],
            "qb_token" => $info["qb_token"],
            "open_type" => $info["open_type"],
        ];
        $result = $trade_extend->ssell($trade_param);
        $model = new SingleLoopSell();
        if (isset($result["result"]['id'])) {
            $model->startTrans();
            try {
                if ($info["open_type"] == 2) {
                    $result["update_sell_data"]["profit_usdt"] = $info["qb_token"] * $result["update_sell_data"]["price"] - $info["qb_token"] * $info["buy_price"];
                } else {
                    $result["update_sell_data"]["profit_usdt"] = $info["qb_token"] * $info["buy_price"] - $info["qb_token"] * $result["update_sell_data"]["price"];
                }
                SingleLoopSell::where(["id" => $info["id"]])->update($result["update_sell_data"]);
                if ($info["buy_id"] == 0 && $info["is_cut"] == 0) {
                    //SingleLoopBuy::where(["setup_id" => $info["setup_id"], "is_deal" => 2, "is_sell" => 0])->update(["is_sell" => 1, "last_qb_token" => 0]);
                    $model->commit();
                } else if ($info["is_cut"] == 1) {
                    //如果是砍仓
                    $model->commit();
                } else {
                    //同时修改购买的数据
                    $up_buy_data["is_sell"] = 1;
                    //查询还剩多少
                    $buy_info = SingleLoopBuy::where(["id" => $info["buy_id"], "is_deal" => 2])->find();
                    $sell_qb_token = $buy_info["last_qb_token"] - $info["qb_token"];
                    $up_buy_data["last_qb_token"] = $sell_qb_token;
                    if ($sell_qb_token > 0) {
                        $up_buy_data["is_sell"] = 0;
                        //$end = " 剩余token:" . $up_buy_data["last_qb_token"];
                    }
                    SingleLoopBuy::where(["id" => $info["buy_id"], "is_deal" => 2])->update($up_buy_data);
                    $model->commit();
                    if ($info["is_cut"] == 0) {
                        user_log("执行了斩仓1", $info["setup_id"]);
                        $this->cut_all($info["id"]);
                        user_log("执行了斩仓2", $info["setup_id"]);
                    }
                    $cache_config = cache($open_down_time_key);
                    if ($cache_config) {
                        if ($cache_config == 0) {
                            cache($open_down_time_key, 0);
                        } else {
                            cache($open_down_time_key, (int)$cache_config - 1);
                        }
                    }
                }
                $info["type"] = 2;
                trade_log($message . " 盈利:" . getBcRound($result["update_sell_data"]["profit_usdt"],3) . " 成交USDT:" . $result["update_sell_data"]["qb_usdt"] . " 成交数量:" . $info["qb_token"] . " 成交行情价:" . $result["update_sell_data"]["price"], $info);
                //初始化数据
                buy_sort($info["setup_id"]);
            } catch (\Exception $e) {
                user_log("卖" . $e->getMessage(), $info["setup_id"]);
                // 事务回滚
                $model->rollback();
                $return = false;
            }
        } else {
            SingleLoopSell::where(["id" => $info["id"]])->update([
                "error" => $result["result"]["message"],
                "is_deal" => 3,
                "param" => json_encode($result["param"])
            ]);
            $info["type"] = 2;
            trade_log($message . " 卖出失败,错误内容:" . $result["result"]["message"], $info);
            if ($info["buy_id"] != 0) {
                SingleLoopBuy::where(["id" => $info["buy_id"]])->update(["is_sell" => 4]);
            }
            $return = false;
        }
        return $return;
        //echo 1;
//            flock($file, LOCK_UN);//解锁
//            fclose($file);
//            return $return;
//        } else {
//            fclose($file);
//            return false;
//        }
    }

}
