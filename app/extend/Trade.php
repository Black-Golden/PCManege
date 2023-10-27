<?php

namespace app\extend;

use app\common\model\QuantSymbol;
use app\common\service\MemberService;

class Trade
{
    public function exchange($user_id, $platform_id)
    {
        $member_service = new MemberService();
        $exchange = $member_service->sign($user_id, $platform_id);
        return $exchange;
    }


    public function now($param)
    {
        $exchange = $this->exchange($param["user_id"], $param["platform_id"]);
        return $exchange->fetch_positions();
    }

    //获取我的余额1
    public function get_balance($param)
    {
        $arr = [
            "free" => "0",
            "used" => "0",
            "total" => "0",
        ];
        $exchange = $this->exchange($param["user_id"], $param["platform_id"]);
        if ($exchange) {
            try {
                if ($param["platform_id"] == 2) {
                    return $exchange->fetch_balance()["USDT"];
                } else {
                    if ($param["platform_type"]) {
                        //return $exchange->fetch_balance(["type" => "future"]);
                      //  $total = $exchange->fetch_balance(["type" => "future"])["USDT"];

                        foreach ($exchange->fapiPrivateV2GetBalance() as $key => $value) {
                            if ($value["asset"] == "USDT") {
                                return [
                                    "free" => "0",
                                    "used" => "0",
                                    "total" => $value["balance"],
                                ];
                            }
                        }
                        //return $exchange->fapiPrivateV2GetBalance();
                    } else {
                        return $exchange->fetch_balance()["USDT"];
                    }
                }
            } catch (\Exception $e) {
//                echo $e->getMessage();
//                exit;
                return [
                    "free" => "0",
                    "used" => "0",
                    "total" => "0",
                ];
            }
        }
        return $arr;
    }

//获取订单信息
    public function order_id($param)
    {
        $exchange = $this->exchange($param["user_id"], $param["platform_id"]);
        $list = $exchange->fetch_order($param["order_id"],"ETHUSDT",["type"=>"future"]);
        print_r($list);
    }

    //获取订单信息
    public function order($param)
    {
        $exchange = $this->exchange($param["user_id"], $param["platform_id"]);
        if ($param["platform_id"] == 2) {
            $arr = [];
            $list = $exchange->fetch_positions();
            if ($list) {
                foreach ($list as $key => $value) {
                    if ($value["side"] == "short") {
                        $info["open_type"] = 3;
                    } else {
                        $info["open_type"] = 2;
                    }
                    $info["platform_id"] = 2;
                    $info["qb_token"] = $value["contracts"] * $value["contractSize"];
                    $info["qb_usdt"] = $value["notional"];
                    $info["buy_price"] = abs($value["entryPrice"]);
                    $info["symbol"] = str_replace("/USDT:USDT", "/USDT", $value["symbol"]);
                    $arr[$info["symbol"] . "-" . $info["open_type"]] = $info;
                }
            }
            return $arr;
        } else {
            $list = $exchange->fapiPrivateGetAccount()["positions"];
            if ($list) {
                foreach ($list as $key => $value) {
                    if ($value["initialMargin"] > 0) {
                        if ($value["positionSide"] == "LONG") {
                            $info["open_type"] = 2;
                        } else {
                            $info["open_type"] = 3;
                        }
                        $info["platform_id"] = 3;
                        $info["qb_token"] = abs($value["positionAmt"]);
                        $info["qb_usdt"] = abs($value["notional"]);
                        $info["buy_price"] = abs($value["entryPrice"]);
                        $info["symbol"] = str_replace("USDT", "/USDT", $value["symbol"]);
                        $arr[$info["symbol"] . "-" . $info["open_type"]] = $info;
                    }
                }
            }
            return $arr;
        }
    }


    //保存类型
    public function set_position_mode($param)
    {
        $exchange = $this->exchange($param["user_id"], $param["platform_id"]);
        try {
            if ($param["platform_id"] == 2) {
                $exchange->set_position_mode("long_short_mode");
            } else if ($param["platform_id"] == 3) {
                $exchange->fapiPrivatePostPositionSideDual(["dualSidePosition" => "true"]);
            }
            return true;
        } catch (\Exception $e) {
            // echo $e->getMessage();
            return false;
        }
    }

    public function set_leverage($param)
    {
        $exchange = $this->exchange($param["user_id"], $param["platform_id"]);
        if ($param["platform_id"] == 2) {
            //$exchange->set_position_mode("long_short_mode");
            $symbol = str_replace("/", "-", $param["symbol"]) . "-SWAP";
            try {
                //$exchange->publicGetAccountSetLeverage
                //查询是否大于倍数
                $symbol_data = QuantSymbol::where(["title" => $param["symbol"]])->find();
                if ($param["lever"] > $symbol_data["oh_max_lever"]) {
                    return [
                        "code" => 1,
                        "msg" => "最大倍数是" . $symbol_data["oh_max_lever"]
                    ];
                }
                $data = $exchange->set_leverage($param["lever"], $symbol, [
                    "mgnMode" => "cross"]);

                if (!$data) {
                    return [
                        "code" => 1,
                        "msg" => "倍数错误"
                    ];
                }
            } catch (\Exception $e) {
                return [
                    "code" => 1,
                    "msg" => $e->getMessage()
                ];
            }
        } else {
            $symbol = str_replace("/", "", $param["symbol"]);
            try {
                $data = $exchange->fapiPrivateGetLeverageBracket([
                    "symbol" => $symbol
                ]);
                if ($param["lever"] > $data[0]["brackets"][0]["initialLeverage"]) {
                    return [
                        "code" => 1,
                        "msg" => "最大倍数是" . $data[0]["brackets"][0]["initialLeverage"]
                    ];
                }
//                [0] => Array
//                (
//                    [symbol] => BTCUSDT
//                    [brackets] => Array
//                (
//                    [0] => Array
//                    (
//                        [bracket] => 1
//                            [initialLeverage] => 125
//                            [notionalCap] => 50000
//                            [notionalFloor] => 0
//                            [maintMarginRatio] => 0.004
//                            [cum] => 0.0

                $data1 = $exchange->fapiPrivatePostLeverage([
                    "symbol" => $symbol,
                    "leverage" => $param["lever"]
                ]);
                if (!$data1) {
                    return [
                        "code" => 1,
                        "msg" => "倍数错误1"
                    ];
                }

            } catch (\Exception $e) {
                //user_log(1, $e->getMessage());

                return [
                    "code" => 1,
                    "msg" => $e->getMessage()
                ];
            }
        }

        return true;
    }

    public function test_trade($param)
    {
        $symbol = $param["symbol"];
        //默认等于购买的USDT
        $amount = $param['qb_source'];
        $now_price = 0;
        if ($param["side"] == "buy") {
            if ($param["platform_id"] == 3 && $param["platform_type"] == 2) {
                $now_price = $param["bhprice"];
            }
            if ($param["platform_id"] == 3 && $param["platform_type"] == 1) {
                $now_price = $param["bprice"];
            }
            if ($param["platform_id"] == 2 && $param["platform_type"] == 2) {
                $now_price = $param["ohprice"];
            }
            if ($param["platform_id"] == 2 && $param["platform_type"] == 1) {
                $now_price = $param["oprice"];
            }
            $param["now_price"] = $now_price;
        }
        $param["go_amount"] = $amount;
        return [
            "result" => [
                "id" => date('mdHis') . str_pad(mt_rand(1, 99999), 5)
            ],
            "order_result" => [
                "cost" => $param['qb_source'],
                "amount" => $param['qb_source'] / $param["now_price"],
                "price" => $now_price,
                "fee" => [
                    "cost" => 0,
                ]
            ],
            "param" => $param,
        ];

    }

    public function sbuy($param)
    {

        $trade_param = [
            "user_id" => $param["user_id"],
            "symbol" => $param["symbol"],
            "platform_id" => $param["platform_id"],
            "qb_token" => $param["qb_token"],
            "side" => "buy",
            "open_type" => $param["open_type"],
        ];
        $result = $this->single_trade($trade_param);
        if (isset($result["result"]["id"])) {
            $update_buy_data = [
                "qb_usdt" => $result["order_result"]["cost"],
                "is_deal" => 2,
                "order_id" => $result["result"]["id"],
            ];
            if ($param["platform_id"] == 2) {
                $update_buy_data["qb_fee"] = $result["order_result"]['fee']['cost'];
            } else {
                $update_buy_data["qb_fee"] = $update_buy_data["qb_usdt"] * 0.005;
            }
            $update_buy_data["param"] = json_encode($result["param"]);
            $update_buy_data["price"] = $result["order_result"]["price"];
            $result["update_buy_data"] = $update_buy_data;
        }
        return $result;
    }

    public function ssell($param)
    {
        $trade_param = [
            "user_id" => $param["user_id"],
            "symbol" => $param["symbol"],
            "platform_id" => $param["platform_id"],
            "qb_token" => $param["qb_token"],
            "side" => "sell",
            "open_type" => $param["open_type"],
        ];
        $result = $this->single_trade($trade_param);
        if (isset($result["result"]["id"])) {
            $update_sell_data = [
                "order_id" => $result["result"]["id"],
                "qb_usdt" => $result["order_result"]['cost'],
                "is_deal" => 2,
            ];
            $update_sell_data["price"] = $result["order_result"]['price'];
            if ($param["platform_id"] == 2) {
                $update_sell_data["qb_fee"] = $result["order_result"]['fee']['cost'];
            }
            if ($param["platform_id"] == 3) {
                $update_sell_data["qb_fee"] = $result["order_result"]['cost'] * 0.0005;
            }

            $result["update_sell_data"] = $update_sell_data;
        }
        return $result;
    }

//    public function lock_single_trade($param){
//
//
//    }


    //新交易(只合约)
    public function single_trade($param)
    {

        $symbol = $param["symbol"];
        $param["platform_type"] = 2;
        //默认等于购买的USDT
        $amount = $param['qb_token'];
        $now_price = symbol_price($param["platform_id"], $symbol);
        $symbol_info = symbol_info($symbol);
        if ($param["side"] == "buy") {
            if ($param["platform_id"] == 3) {
                $amount = $param['qb_token'];
            }
            if ($param["platform_id"] == 2 && $param["platform_type"] == 2) {
                $amount = $param["qb_token"] / $symbol_info["ct_val"];
                //user_log("buy" . $amount, 1);
                if ($amount <= 0) {
                    $amount = 1;
                }
            }
            $param["now_price"] = $now_price;
        } else {
            if ($param["platform_id"] == 2 && $param["platform_type"] == 2) {
                //只有类型是合约和欧易的时候需要qb_to 取item里的
                $amount = $param['qb_token'] / $symbol_info["ct_val"];
                //user_log("sell" . $amount, 1);

            }
        }
        $param["go_amount"] = $amount;
        if ($param["platform_type"] == 2 && $param["platform_id"] == 2) {
            $symbol = str_replace("/", "-", $param["symbol"]) . "-SWAP";
        }


        if ($param["side"] == "buy") {
            if ($param["platform_type"] == 2) {
                if ($param["open_type"] == 3) {
                    $result = $this->buy_short($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
                } else {
                    $result = $this->buy_long($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
                }
            } else {
                $result = $this->buy($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
            }
        } else {
            if ($param["platform_type"] == 2) {
                if ($param["open_type"] == 3) {
                    $result = $this->sell_short($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
                } else {
                    $result = $this->sell_long($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
                }
            } else {
                $result = $this->sell($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
            }
        }
        $order_result = [];
        if (isset($result["id"])) {
            try {
                if ($param["platform_id"] == 3 && $param["open_type"] != 0) {
                    $order_result = $this->exchange($param["user_id"], $param["platform_id"])->fetch_order($result["id"], $symbol, [
                        "type" => "future"
                    ]);
                } else {
                    $order_result = $this->exchange($param["user_id"], $param["platform_id"])->fetch_order($result["id"], $symbol);
                }
            } catch (\Exception $e) {
                $order_result = [
                    "message" => $e->getMessage()
                ];
            }
        }
        return [
            "result" => $result,
            "order_result" => $order_result,
            "param" => $param,
        ];
    }


    public function trade($param)
    {
        $symbol = $param["symbol"];
        //默认等于购买的USDT
        $amount = $param['qb_source'];

        if ($param["side"] == "buy") {
            if ($param["platform_id"] == 3 && $param["platform_type"] == 2) {
                $now_price = $param["bhprice"];
            }
            if ($param["platform_id"] == 3 && $param["platform_type"] == 1) {
                $now_price = $param["bprice"];
            }
            if ($param["platform_id"] == 2 && $param["platform_type"] == 2) {
                $now_price = $param["ohprice"];
            }

            if ($param["platform_id"] == 2 && $param["platform_type"] == 1) {
                $now_price = $param["oprice"];
            }

            if ($param["platform_id"] == 3) {
                $param["decimal"] = 3;
                $amount = getBcRound($param['qb_source'] / $now_price, $param["decimal"]);
                if ((float)$amount < (float)$param["bh_min_price"]) {
                    $amount = getBcRound($param["bh_min_price"], $param["decimal"]);
                }
            }
            if ($param["platform_id"] == 2 && $param["platform_type"] == 2) {
                $amount = round($param['qb_source'] / ($now_price * $param["ct_val"]));
            }
            $param["now_price"] = $now_price;
        } else {
            if ($param["platform_id"] == 2 && $param["platform_type"] == 2) {
                //只有类型是合约和欧易的时候需要qb_to 取item里的
                $amount = $param['qb_to'] / $param["ct_val"];
            }
        }
        $param["go_amount"] = $amount;
        if ($param["platform_type"] == 2 && $param["platform_id"] == 2) {
            $symbol = str_replace("/", "-", $param["symbol"]) . "-SWAP";
        }
        if ($param["side"] == "buy") {
            if ($param["platform_type"] == 2) {
                if ($param["open_type"] == 3) {
                    $result = $this->buy_short($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
                } else {
                    $result = $this->buy_long($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
                }
            } else {
                $result = $this->buy($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
            }
        } else {
            if ($param["platform_type"] == 2) {
                if ($param["open_type"] == 3) {
                    $result = $this->sell_short($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
                } else {
                    $result = $this->sell_long($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
                }
            } else {
                $result = $this->sell($param["user_id"], $param["platform_id"], $param["symbol"], $amount);
            }
        }
        $order_result = [];
        if (isset($result["id"])) {
            try {
                if ($param["platform_id"] == 3 && $param["open_type"] != 0) {
                    $order_result = $this->exchange($param["user_id"], $param["platform_id"])->fetch_order($result["id"], $symbol, [
                        "type" => "future"
                    ]);
                } else {
                    $order_result = $this->exchange($param["user_id"], $param["platform_id"])->fetch_order($result["id"], $symbol);
                }
            } catch (\Exception $e) {
                $order_result = [
                    "message" => $e->getMessage()
                ];
            }
        }
        return [
            "result" => $result,
            "order_result" => $order_result,
            "param" => $param,
        ];
    }

    //买
    public function buy($user_id, $platform_id, $symbol, $amount)
    {
        try {
            if ($platform_id == 2) {
                //amount = 以USDT去购买
                $result = $this->exchange($user_id, $platform_id)->create_market_buy_order($symbol, $amount, ["tgtCcy" => "quote_ccy"]);
            } else {
                //amount = USDT / $now_price
                $result = $this->exchange($user_id, $platform_id)->create_market_buy_order($symbol, $amount);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }

    //卖
    public function sell($user_id, $platform_id, $symbol, $amount)
    {
        try {
            $result = $this->exchange($user_id, $platform_id)->create_market_sell_order($symbol, $amount);
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }

    //买多
    public function buy_long($user_id, $platform_id, $symbol, $amount)
    {
        try {
            if ($platform_id == 2) {
                $symbol = str_replace("/", "-", $symbol) . "-SWAP";
                $result = $this->exchange($user_id, $platform_id)->create_market_order($symbol, "buy", $amount, 0, [
                    "posSide" => "long", "tdMode" => "cross"
                ]);
            } else {
                $result = $this->exchange($user_id, $platform_id)->create_market_order($symbol, "buy", $amount, null, [
                    "positionSide" => "long",
                    "type" => "future"
                ]);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }


    //买空
    public function buy_short($user_id, $platform_id, $symbol, $amount)
    {

        try {
            if ($platform_id == 2) {
                $symbol = str_replace("/", "-", $symbol) . "-SWAP";
                $result = $this->exchange($user_id, $platform_id)->create_market_order($symbol, "sell", $amount, 0, [
                    "posSide" => "short", "tdMode" => "cross"
                ]);
            } else {
                $result = $this->exchange($user_id, $platform_id)->create_market_order($symbol, "sell", $amount, null, [
                    "positionSide" => "short",
                    "type" => "future"
                ]);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;

    }

    //卖多
    public function sell_long($user_id, $platform_id, $symbol, $amount)
    {
        try {
            if ($platform_id == 2) {
                $symbol = str_replace("/", "-", $symbol) . "-SWAP";
                $result = $this->exchange($user_id, $platform_id)->create_market_order($symbol, "sell", $amount, 0, [
                    "posSide" => "long", "tdMode" => "cross"
                ]);
            } else {
                $result = $this->exchange($user_id, $platform_id)->create_market_order($symbol, "sell", $amount, null, [
                    "positionSide" => "long",
                    "type" => "future"
                ]);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }

    //卖空
    public function sell_short($user_id, $platform_id, $symbol, $amount)
    {
        try {
            if ($platform_id == 2) {
                $symbol = str_replace("/", "-", $symbol) . "-SWAP";
                $result = $this->exchange($user_id, $platform_id)->create_market_order($symbol, "buy", $amount, 0, [
                    "posSide" => "short", "tdMode" => "cross"
                ]);
            } else {
                $result = $this->exchange($user_id, $platform_id)->create_market_order($symbol, "buy", $amount, null, [
                    "positionSide" => "short",
                    "type" => "future"
                ]);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }


    public function batch_buy_long($user_id, $platform_id, $data)
    {

        try {
            if ($platform_id == 2) {
                $arr = [];
                foreach ($data as $key => $val) {
                    $val["symbol"] = str_replace("/", "-", $val["symbol"]) . "-SWAP";
                    $arr[] = [
                        "instId" => $val["symbol"],
                        "tdMode" => "cross",
                        "side" => "buy",
                        "posSide" => "long",
                        "ordType" => "market",
                        "sz" => $val["amount"]
                    ];
                }
                $result = $this->exchange($user_id, $platform_id)->privatePostTradeBatchOrders($arr);
            } else {
                $arr = [];
                foreach ($data as $key => $val) {
                    $val["symbol"] = str_replace("/", "", $val["symbol"]) . "-SWAP";
                    $arr[] = [
                        "symbol" => $val["symbol"],
                        "tdMode" => "cross",
                        "side" => "BUY",
                        "positionSide" => "LONG",
                        "type" => "MARKET",
                        "quantity" => $val["amount"]
                    ];
                }
                $result = $this->exchange($user_id, $platform_id)->fapiPrivatePostBatchOrders($arr);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }


    public function batch_buy_short($user_id, $platform_id, $data)
    {

        try {
            if ($platform_id == 2) {
                $arr = [];
                foreach ($data as $key => $val) {
                    $val["symbol"] = str_replace("/", "-", $val["symbol"]) . "-SWAP";
                    $arr[] = [
                        "instId" => $val["symbol"],
                        "tdMode" => "cross",
                        "side" => "sell",
                        "posSide" => "short",
                        "ordType" => "market",
                        "sz" => $val["amount"]
                    ];
                }
                $result = $this->exchange($user_id, $platform_id)->privatePostTradeBatchOrders($arr);
            } else {
                $arr = [];
                foreach ($data as $key => $val) {
                    $val["symbol"] = str_replace("/", "", $val["symbol"]) . "-SWAP";
                    $arr[] = [
                        "symbol" => $val["symbol"],
                        "tdMode" => "cross",
                        "side" => "SELL",
                        "positionSide" => "SHORT",
                        "type" => "MARKET",
                        "quantity" => $val["amount"]
                    ];
                }
                $result = $this->exchange($user_id, $platform_id)->fapiPrivatePostBatchOrders($arr);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }


    public function batch_sell_long($user_id, $platform_id, $data)
    {

        try {
            if ($platform_id == 2) {
                $arr = [];
                foreach ($data as $key => $val) {
                    $val["symbol"] = str_replace("/", "-", $val["symbol"]) . "-SWAP";
                    $arr[] = [
                        "instId" => $val["symbol"],
                        "tdMode" => "cross",
                        "side" => "sell",
                        "posSide" => "long",
                        "ordType" => "market",
                        "sz" => $val["amount"]
                    ];
                }
                $result = $this->exchange($user_id, $platform_id)->privatePostTradeBatchOrders($arr);
            } else {
                $arr = [];
                foreach ($data as $key => $val) {
                    $val["symbol"] = str_replace("/", "", $val["symbol"]) . "-SWAP";
                    $arr[] = [
                        "symbol" => $val["symbol"],
                        "tdMode" => "cross",
                        "side" => "SELL",
                        "positionSide" => "LONG",
                        "type" => "MARKET",
                        "quantity" => $val["amount"]
                    ];
                }
                $result = $this->exchange($user_id, $platform_id)->fapiPrivatePostBatchOrders($arr);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }


    public function batch_sell_short($user_id, $platform_id, $data)
    {

        try {
            if ($platform_id == 2) {
                $arr = [];
                foreach ($data as $key => $val) {
                    $val["symbol"] = str_replace("/", "-", $val["symbol"]) . "-SWAP";
                    $arr[] = [
                        "instId" => $val["symbol"],
                        "tdMode" => "cross",
                        "side" => "buy",
                        "posSide" => "short",
                        "ordType" => "market",
                        "sz" => $val["amount"]
                    ];
                }
                $result = $this->exchange($user_id, $platform_id)->privatePostTradeBatchOrders($arr);
            } else {
                $arr = [];
                foreach ($data as $key => $val) {
                    $val["symbol"] = str_replace("/", "", $val["symbol"]) . "-SWAP";
                    $arr[] = [
                        "symbol" => $val["symbol"],
                        "tdMode" => "cross",
                        "side" => "BUY",
                        "positionSide" => "SHORT",
                        "type" => "MARKET",
                        "quantity" => $val["amount"]
                    ];
                }
                $result = $this->exchange($user_id, $platform_id)->fapiPrivatePostBatchOrders($arr);
            }
        } catch (\Exception $e) {
            $result = [
                "message" => $e->getMessage()
            ];
        }
        return $result;
    }


}

