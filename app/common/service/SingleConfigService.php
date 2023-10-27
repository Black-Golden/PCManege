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


use app\common\model\CustomPolicy;
use app\common\model\Member;
use app\common\model\PlatformMember;
use app\common\model\QuantSymbol;
use app\common\model\SingleLeverConfig;
use app\common\model\SingleLoopBuy;
use app\common\model\SingleLoopSell;
use app\common\model\SingleLoopSetup;
use app\common\model\SymbolBuyConfig;
use app\extend\Trade;

/**
 * 对冲交易详情管理-服务类
 * @author 测试
 */
class SingleConfigService extends BaseService
{

    //获取我要购买的配置信息
    public function get_config($user_id)
    {

        $validate = getValidate([
            'platform_id' => 'require',
            'symbol' => 'require',
            //'balance' => 'require',//测试传值
        ], [
            'platform_id.require' => '请选择平台',
            'symbol.require' => '请选择交易品种',
            //'balance.require' => '请选择金额',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }

        $lever = input("lever", 10);

        $balance = getBcRound(input("balance", 0), 2);
        $symbol_info = QuantSymbol::where(["title" => $this->input["symbol"]])->find();
        //$sy
        //$info = SymbolBuyConfig::where(["lever" => $lever, "title" => $this->input["symbol"]])->find();

        $new_lever_select = [];
        $lever_select = SymbolBuyConfig::where(["title" => $this->input["symbol"]])->column("buy_per", "lever");
        if (isset($lever_select[$lever])) {
            $per = $lever_select[$lever];
        } else {
            $per = 0.001;
        }
        if ($this->input["platform_id"] == 2) {
            $min_token = $symbol_info["ct_val"];
        } else {
            $min_token = $symbol_info["bh_min_price"];
        }
        $price = symbol_price($this->input["platform_id"], $this->input["symbol"]);
        $point_number = getLen($min_token);

        $now_bail = getBcRound($price * $min_token / $lever, 4);
        $usable_balance = getBcRound($balance * $per, 3);
        $max_token = 0;
        $re = 0;
        if ($price) {
            $max_token = bcdiv($usable_balance * $lever, $price, $point_number);
            $re = bcmod((string)$max_token, (string)$min_token, $point_number);
        }
        $max_token = $max_token - $re;

        foreach ($lever_select as $key => $value) {
            $new_lever_select[] = [
                "value" => $value,
                "lable" => $key,
                "name" => $key . "倍"
            ];
        }
        //查询最多购买金额
        $results = [
            'token' => $symbol_info['token'],
            "title" => "最多使用" . $balance . " x " . ($per * 100) . "% = " . $usable_balance . "USDT",
            "balance" => $balance,
            "per" => $per,
            "min_token" => $min_token,
            "max_usdt" => $usable_balance,
            "max_token" => $max_token,
            "now_bail" => $now_bail,
            "price" => $price,
            "lever" => $lever,
            "point_number" => $point_number,
            "lever_list" => $new_lever_select
        ];
        return $results;
    }

    public function update_set($user_id)
    {

        $validate = getValidate([
            "setup_id" => 'require',
            'buy_token' => 'require',
            'lever' => 'require',
            'type_id' => 'require',
            'is_singke_cut' => 'require',
            'is_double_cut' => 'require',
            'is_other_cut' => 'require',
            'rounds' => 'require',
        ], [
            "setup_id.require" => '请选择平台',
            'buy_token.require' => '请选择token数量',
            'lever.require' => '请选择等级',
            'type_id.require' => '请选择开仓类型',
            'is_singke_cut.require' => '请选择',
            'is_double_cut.require' => '请选择',
            'is_other_cut.require' => '请选择',
            'rounds.require' => '请选择',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }

        $info = SingleLoopSetup::where(["id" => $this->input["setup_id"]])->find();
        if ($info["qb_token"] !== $this->input["buy_token"] && $info["is_run"] != 0) {
            return [
                "code" => 1,
                "msg" => "策略已经开启，暂时不可更改，请先平仓卖出"
            ];
        }
        if ($info["lever"] !== $this->input["lever"] && $info["is_run"] != 0) {
            return [
                "code" => 1,
                "msg" => "策略已经开启，暂时不可更改，请先平仓卖出"
            ];
        }
        if ($info["type_id"] !== $this->input["type_id"] && $info["is_run"] != 0) {
            return [
                "code" => 1,
                "msg" => "策略已经开启，暂时不可更改，请先平仓卖出"
            ];
        }

        if ($info["lever"] !== $this->input["lever"]) {
            $trade = new Trade();
            $balance = $trade->get_balance(["user_id" => $user_id, "platform_id" => $this->input["platform_id"], "platform_type" => 2]);
            if ($balance["total"] <= 0) {
                return [
                    "code" => 1,
                    "msg" => "余额不足"
                ];
            }
        }
        SingleLoopSetup::where(["id" => $this->input["setup_id"], "user_id" => $user_id])->update(
            [
                "lever" => $this->input["lever"],
                "qb_token" => $this->input["buy_token"],
                "type_id" => $this->input["type_id"],//购买赔率
                "is_singke_cut" => $this->input["is_singke_cut"],
                "is_double_cut" => $this->input["is_double_cut"],
                "is_other_cut" => $this->input["is_other_cut"],
                "rounds" => $this->input["rounds"],
            ]
        );
        return true;
    }

    public function set_setup($user_id)
    {

        $validate = getValidate([
            "setup_id" => 'require',
            'is_singke_cut' => 'require',
            'is_double_cut' => 'require',
            'is_other_cut' => 'require',
            'lever' => 'require',
            'type_id' => 'require',
        ], [
            "setup_id.require" => '请选择平台',
            'is_singke_cut.require' => '请选择',
            'is_double_cut.require' => '请选择',
            'is_other_cut.require' => '请选择',
            'lever.require' => '请选择',
            'type_id.require' => '请选择',

        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }

        $info = SingleLoopSetup::where(["id" => $this->input["setup_id"]])->find();
        if ($info["is_run"] != 0) {
            return [
                "code" => 1,
                "msg" => "策略已经开启，暂时不可更改，请先平仓卖出"
            ];
        }
        if ($this->input['type_id'] == 5) {
            $validate = getValidate([
                "config" => 'require',

            ], [
                "config.require" => '请选择添加自定义策略详情',
            ]);
            if ($validate['code'] == 1) {
                return $validate;
            }

            //获取用户给策略的自定义策略
            $cus = CustomPolicy::where(['user_id' => $user_id, 'symbol' => $info['symbol']])->find();
            if ($cus) {
                $list = [
                    'config' => json_encode($this->input['config']),
                    'lever' => count($this->input['config']),
                ];
                CustomPolicy::where(['id' => $cus['id']])->update($list);
            } else {
                $list = [
                    'config' => json_encode($this->input['config']),
                    'lever' => count($this->input['config']),
                    'user_id' => $user_id,
                    'symbol' => $info['symbol']
                ];
                CustomPolicy::insert($list);
            }
        }

        SingleLoopSetup::where(["id" => $this->input["setup_id"], "user_id" => $user_id])->update(
            [
                "is_singke_cut" => $this->input["is_singke_cut"],
                "is_double_cut" => $this->input["is_double_cut"],
                "is_other_cut" => $this->input["is_other_cut"],
                "lever" => $this->input["lever"],
                "type_id" => $this->input["type_id"],
            ]
        );
        return true;
    }

    //初始化
    public function init($user_id)
    {
        $validate = getValidate([
            'platform_id' => 'require',
            'symbol' => 'require',
            'open_type' => 'require',
            'buy_token' => 'require',
            'lever' => 'require',
            'type_id' => 'require',
            'is_singke_cut' => 'require',
            'is_double_cut' => 'require',
            'is_other_cut' => 'require',
            'rounds' => 'require',
        ], [
            'platform_id.require' => '请选择平台',
            'symbol.require' => '请选择交易品种',
            'open_type.require' => '请选择方式',
            'buy_token.require' => '请选择token数量',
            'lever.require' => '请选择等级',
            'type_id.require' => '请选择开仓类型',
            'is_singke_cut.require' => '请选择',
            'is_double_cut.require' => '请选择',
            'is_other_cut.require' => '请选择',
            'rounds.require' => '请选择层级',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }

        //查询余额够不够
        if (!Member::check_usdt($user_id, 1)) {
            return [
                "code" => 1,
                "msg" => "您的服务费余额不足，无法开仓"
            ];
        }

        $data_member = PlatformMember::where(["user_id" => $user_id, 'platform_id' => $this->input['platform_id']])->find();
        if (!$data_member) {
            return [
                "code" => 1,
                "msg" => "请先设置API"
            ];
        }
        $trade = new Trade();
        $leverage = $trade->set_leverage([
            "user_id" => $user_id,
            "platform_id" => $this->input["platform_id"],
            "symbol" => $this->input["symbol"],
            "lever" => $this->input["lever"],
        ]);
        if ($leverage["code"] == 1) {
            return $leverage;
        }
        $balance = $trade->get_balance(["user_id" => $user_id, "platform_id" => $this->input["platform_id"], "platform_type" => 2]);
        if ($balance["total"] <= 0) {
            return [
                "code" => 1,
                "msg" => "余额不足"
            ];
        }
        if ($this->input['type_id'] == 5) {
            $validate = getValidate([
                "config" => 'require',

            ], [
                "config.require" => '请选择添加自定义策略详情',
            ]);
            if ($validate['code'] == 1) {
                return $validate;
            }

            //获取用户给策略的自定义策略
            $cus = CustomPolicy::where(['user_id' => $user_id, 'symbol' => $this->input['symbol']])->find();

            if ($cus) {
                $list = [
                    'config' => json_encode($this->input['config']),
                    'lever' => count($this->input['config']),

                ];
                CustomPolicy::where(['id' => $cus['id']])->update($list);
            } else {
                $list = [
                    'config' => json_encode($this->input['config']),
                    'lever' => count($this->input['config']),
                    'user_id' => $user_id,
                    'symbol' => $this->input['symbol']

                ];
                CustomPolicy::insert($list);
            }
        }
        $setservice = new SingleSetService();
        //查询当前开仓的数量


        //查询这个策略是否已经存在
        $info = SingleLoopSetup::where(["user_id" => $user_id, "symbol" => $this->input['symbol'], "open_type" => $this->input["open_type"]])->find();
        if (!$info) {
            $setup_count = SingleLoopSetup::where(["is_run" => 1, "user_id" => $user_id])->count();
            if ($setup_count >= 16) {
                return [
                    "code" => 1,
                    "msg" => "为了您的资金安全，最多开启16个仓位"
                ];
            }
        }
        if ($this->input["open_type"] == 1) {
            $results = $setservice->setup([
                "user_id" => $user_id,
                "lever" => $this->input["lever"],
                "buy_token" => $this->input["buy_token"],
                "type_id" => $this->input["type_id"],
                "platform_id" => $this->input["platform_id"],
                "open_type" => 2,
                "symbol" => $this->input["symbol"],
                "is_singke_cut" => $this->input["is_singke_cut"],
                "is_double_cut" => $this->input["is_double_cut"],
                "rounds" => $this->input["rounds"],
                "is_other_cut" => $this->input["is_other_cut"],
                "balance" => $balance
            ]);
            $results = $setservice->setup([
                "user_id" => $user_id,
                "lever" => $this->input["lever"],
                "buy_token" => $this->input["buy_token"],
                "type_id" => $this->input["type_id"],
                "platform_id" => $this->input["platform_id"],
                "open_type" => 3,
                "symbol" => $this->input["symbol"],
                "is_singke_cut" => $this->input["is_singke_cut"],
                "is_double_cut" => $this->input["is_double_cut"],
                "is_other_cut" => $this->input["is_other_cut"],
                "rounds" => $this->input["rounds"],
                "balance" => $balance
            ]);
        } else {
            $results = $setservice->setup([
                "user_id" => $user_id,
                "lever" => $this->input["lever"],
                "buy_token" => $this->input["buy_token"],
                "type_id" => $this->input["type_id"],
                "platform_id" => $this->input["platform_id"],
                "open_type" => $this->input["open_type"],
                "symbol" => $this->input["symbol"],
                "is_singke_cut" => $this->input["is_singke_cut"],
                "is_double_cut" => $this->input["is_double_cut"],
                "is_other_cut" => $this->input["is_other_cut"],
                "rounds" => $this->input["rounds"],
                "balance" => $balance
            ]);
        }


        return $results;
    }

    //策略配置
    public function config($user_id)
    {
        $validate = getValidate([
            'setup_id' => 'require',
            'type_id' => 'require',
            'value' => 'require|in:0,1,2,3',
        ], [
            'setup_id.require' => '请选择平台',
            'type_id.require' => '请选择',
            'value.require' => '请选择',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $query = SingleLoopSetup::where(["user_id" => $user_id, "id" => $this->input["setup_id"]]);
        if ($this->input["type_id"] == 1) {
            $results = $query->update(["is_loop" => $this->input["value"]]);
        }
        if ($this->input["type_id"] == 2) {
            $results = $query->update(["is_open_down" => $this->input["value"]]);
        }
        if ($this->input["type_id"] == 3) {
            $results = $query->update(["is_run" => $this->input["value"]]);
        }
        return true;
    }


    //设置倍数百分比配置项
    public function lever_config()
    {
        for ($i = 1; $i <= 50; $i++) {
            if ($i <= 10) {
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 6])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 10, "type_id" => 6, "up_stop_per" => 0.2, "down_stop_per" => 0.3
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 6])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 20, "type_id" => 6, "up_stop_per" => 0.4, "down_stop_per" => 0.6
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 6])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 50, "type_id" => 6, "up_stop_per" => 1, "down_stop_per" => 1.5
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 6])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 100, "type_id" => 6, "up_stop_per" => 2, "down_stop_per" => 3
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 4])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 10, "type_id" => 4, "up_stop_per" => 0.2, "down_stop_per" => 0.3
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 4])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 20, "type_id" => 4, "up_stop_per" => 0.4, "down_stop_per" => 0.6
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 4])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 50, "type_id" => 4, "up_stop_per" => 1, "down_stop_per" => 1.5
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 4])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 100, "type_id" => 4, "up_stop_per" => 2, "down_stop_per" => 3
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 2])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 10, "type_id" => 2, "up_stop_per" => 0.2, "down_stop_per" => 0.2
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 2])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 20, "type_id" => 2, "up_stop_per" => 0.4, "down_stop_per" => 0.4
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 2])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 50, "type_id" => 2, "up_stop_per" => 1, "down_stop_per" => 1
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 2])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 100, "type_id" => 2, "up_stop_per" => 2, "down_stop_per" => 2
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 1])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 10, "type_id" => 1, "up_stop_per" => 0.1, "down_stop_per" => 0.1
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 1])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 20, "type_id" => 1, "up_stop_per" => 0.2, "down_stop_per" => 0.2
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 1])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 50, "type_id" => 1, "up_stop_per" => 0.5, "down_stop_per" => 0.5
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 1])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 100, "type_id" => 1, "up_stop_per" => 1, "down_stop_per" => 1
                    ]);
                }


            } else {

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 6])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 10, "type_id" => 6, "up_stop_per" => 0.2, "down_stop_per" => 0.4
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 6])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 20, "type_id" => 6, "up_stop_per" => 0.4, "down_stop_per" => 0.8
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 6])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 50, "type_id" => 6, "up_stop_per" => 1, "down_stop_per" => 2
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 6])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 100, "type_id" => 6, "up_stop_per" => 2, "down_stop_per" => 4
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 4])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 10, "type_id" => 4, "up_stop_per" => 0.2, "down_stop_per" => 0.4
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 4])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 20, "type_id" => 4, "up_stop_per" => 0.4, "down_stop_per" => 0.8
                    ]);
                }
                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 4])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 50, "type_id" => 4, "up_stop_per" => 1, "down_stop_per" => 2
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 4])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 100, "type_id" => 4, "up_stop_per" => 2, "down_stop_per" => 4
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 2])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 10, "type_id" => 2, "up_stop_per" => 0.2, "down_stop_per" => 0.3
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 2])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 20, "type_id" => 2, "up_stop_per" => 0.4, "down_stop_per" => 0.6
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 2])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 50, "type_id" => 2, "up_stop_per" => 1, "down_stop_per" => 1.5
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 2])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 100, "type_id" => 2, "up_stop_per" => 2, "down_stop_per" => 3
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 1])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 10, "type_id" => 1, "up_stop_per" => 0.1, "down_stop_per" => 0.2
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 1])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 20, "type_id" => 1, "up_stop_per" => 0.2, "down_stop_per" => 0.4
                    ]);
                }


                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 1])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 50, "type_id" => 1, "up_stop_per" => 0.5, "down_stop_per" => 1
                    ]);
                }

                $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 1])->find();
                if (!$info) {
                    SingleLeverConfig::create([
                        "rounds" => $i, "lever" => 100, "type_id" => 1, "up_stop_per" => 1, "down_stop_per" => 2
                    ]);
                }


            }


            $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 10, "type_id" => 3])->find();
            if (!$info) {
                SingleLeverConfig::create([
                    "rounds" => $i, "lever" => 10, "type_id" => 3, "up_stop_per" => 0.2, "down_stop_per" => 0.3
                ]);
            }

            $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 20, "type_id" => 3])->find();
            if (!$info) {
                SingleLeverConfig::create([
                    "rounds" => $i, "lever" => 20, "type_id" => 3, "up_stop_per" => 0.4, "down_stop_per" => 0.6
                ]);
            }


            $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 50, "type_id" => 3])->find();
            if (!$info) {
                SingleLeverConfig::create([
                    "rounds" => $i, "lever" => 50, "type_id" => 3, "up_stop_per" => 1, "down_stop_per" => 1.5
                ]);
            }


            $info = SingleLeverConfig::where(["rounds" => $i, "lever" => 100, "type_id" => 3])->find();
            if (!$info) {
                SingleLeverConfig::create([
                    "rounds" => $i, "lever" => 100, "type_id" => 3, "up_stop_per" => 2, "down_stop_per" => 3
                ]);
            }

        }
    }

    //设置交易对买卖限制
    public function lever_buy_config()
    {
        $buy_per = 0.003;
        $list = QuantSymbol::select();
        foreach ($list as $key => $val) {
            $info = SymbolBuyConfig::where(["lever" => 10, "title" => $val["title"]])->find();
            if (!$info) {
                SymbolBuyConfig::insert([
                    "lever" => 10,
                    "buy_per" => 0.005,
                    "title" => $val["title"]
                ]);
            }
            $info = SymbolBuyConfig::where(["lever" => 20, "title" => $val["title"]])->find();
            if (!$info) {
                SymbolBuyConfig::insert([
                    "lever" => 20,
                    "buy_per" => 0.005,
                    "title" => $val["title"]
                ]);
            }
//            $info = SymbolBuyConfig::where(["lever" => 30, "title" => $val["title"]])->find();
//            if (!$info) {
//                SymbolBuyConfig::insert([
//                    "lever" => 30,
//                    "buy_per" => 0.003,
//                    "title" => $val["title"]
//                ]);
//            }

            $info = SymbolBuyConfig::where(["lever" => 50, "title" => $val["title"]])->find();
            if (!$info) {
                SymbolBuyConfig::insert([
                    "lever" => 50,
                    "buy_per" => 0.002,
                    "title" => $val["title"]
                ]);
            }
            $info = SymbolBuyConfig::where(["lever" => 100, "title" => $val["title"]])->find();
            if (!$info) {
                SymbolBuyConfig::insert([
                    "lever" => 100,
                    "buy_per" => 0.002,
                    "title" => $val["title"]
                ]);
            }
        }
    }
}
