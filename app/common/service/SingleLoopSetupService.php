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
use app\common\model\TradeLog;
use app\extend\Trade;
use think\cache;

/**
 * 对冲交易详情管理-服务类
 * @author 测试
 */
class SingleLoopSetupService extends BaseService
{

    public $type = [
        5 => '自定义策略',
        1 => '高频',
        2 => '激进',
        3 => '保守',
        4 => '超级保守',
        6 => '系统默认',
        0 => '',
    ];
    public $arr = ['暂未创建层级', '第一层级', '第二层级', '第三层级', '第四层级', '第五层级', '第六层级', '第七层级', '第八层级', '第九层级', '第十层级', '第十一层级', '第十二层级', '第十三层级', '第十四层级', '第十五层级', '第十六层级', '第十七层级', '第十八层级', '第十九层级', '第二十层级',
        '第二十一层级', '第二十二层级', '第二十三层级', '第二十四层级', '第二十五层级', '第二十六层级', '第二十七层级', '第二十八层级', '第二十九层级', '第三十层级',
        '第三十一层级', '第三十二层级', '第三十三层级', '第三十四层级', '第三十五层级', '第三十六层级', '第三十七层级', '第三十八层级', '第三十九层级', '第四十层级',
        '第四十一层级', '第四十二层级', '第四十三层级', '第四十四层级', '第四十五层级', '第四十六层级', '第四十七层级', '第四十八层级', '第四十九层级', '第五十层级',
        '第五十一层级', '第五十二层级', '第五十三层级', '第五十四层级', '第五十五层级', '第五十六层级', '第五十七层级', '第五十八层级', '第五十九层级', '第六十层级',
        '第六十一层级', '第六十二层级', '第六十三层级', '第六十四层级', '第六十五层级', '第六十六层级', '第六十七层级', '第六十八层级', '第六十九层级', '第七十层级',
        '第七十一层级', '第七十二层级', '第七十三层级', '第七十四层级', '第七十五层级', '第七十六层级', '第七十七层级', '第七十八层级', '第七十九层级', '第八十层级',
        '第八十一层级', '第八十二层级', '第八十三层级', '第八十四层级', '第八十五层级', '第八十六层级', '第八十七层级', '第八十八层级', '第八十九层级', '第九十层级',
    ];

    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new SingleLoopSetup();
    }


    /**
     * 获取数据列表
     * @return array
     * @since 2022/11/16
     * @author 测试
     */
    public function getList()
    {
        $param = $this->input;
        // 查询条件
        $map = [];
        if(isset($param['start_time']) && isset($param['end_time'])){
            $start = strtotime($param['start_time']);
            $end = strtotime($param['end_time']);
            $map[] = ['create_time', 'BETWEEN', [$start,$end]];
        }
        // 平台名称
        $symbol = isset($param['symbol']) ? trim($param['symbol']) : '';
        if ($symbol) {
            $symbol = strtoupper($symbol);
            $map[] = ['symbol', 'like', "%{$symbol}%"];
        }
        // 平台名称
        $user_name = isset($param['user_name']) ? trim($param['user_name']) : '';
        if ($user_name) {

            $ids = Member::where('username', 'like', "%{$user_name}%")->column('id');
            $map[] = ['user_id', 'in', $ids];
        }
        // 是否成交
        $is_run = isset($param['is_run']) ? (int)$param['is_run'] : 0;
        if ($is_run) {
            $map[] = ['is_run', '=', $is_run];
        }
        // 是否成交
        $platform_id = isset($param['platform_id']) ? (int)$param['platform_id'] : 0;
        if ($platform_id) {
            $map[] = ['platform_id', '=', $platform_id];
        }

        // 是否成交
        $is_run = isset($param['is_run']) ? (int)$param['is_run'] : 0;
        if ($is_run) {
            $map[] = ['is_run', '=', $is_run];
        }

        // 是否结算
        $type_id = isset($param['type_id']) ? (int)$param['type_id'] : 0;
        if ($type_id) {
            $map[] = ['type_id', '=', $type_id];
        }
        // 是否结算
        $open_type = isset($param['open_type']) ? (int)$param['open_type'] : 0;
        if ($open_type) {
            $map[] = ['open_type', '=', $open_type];
        }
        // 是否结算
        $is_open_down = isset($param['is_open_down']) ? (int)$param['is_open_down'] : 0;
        if ($is_open_down) {
            $map[] = ['is_open_down', '=', $is_open_down];
        }


        return parent::getList($map); // TODO: Change the autogenerated stub
    }


    public function lossPoll($user_id)
    {
        $param = ['loss' => 0, 'count' => 0];

        $map[] = ['is_run', "=", 1];

        $list = parent::getList($map, [], 0, 1);
        if ($list["data"]) {
            $param['count'] = $list['count'];
            foreach ($list["data"] as &$value) {
                $value['loss'] = 0;
                $value['rounds'] = 1;
                $open = $value['open_type'] == 2 ? '多' : '空';
                $value['title'] = $value['symbol'] . '永续' . $open . '全仓';
//                //获取所有未卖出的层级
                $list1 = SingleLoopBuy::where('setup_id', $value['id'])->where('is_sell', 0)->where('is_deal', 2)->order('rounds DESC')->select();
                $price = symbol_price($value['platform_id'], $value['symbol']);
                if ($list1) {
                    foreach ($list1 as &$key) {
                        $value['rounds'] = $value['rounds'] > $key['rounds'] ? $value['rounds'] : $key['rounds'];
                        $key['lever'] = numToWord($key['rounds']);

                        $key['total'] = round($key['last_qb_token'] * $key['price'], 6);
                        $key['increase'] = 0;
                        if ($key['price']) {
                            $key['increase'] = getBcRound(symbol_price_per($key["platform_id"], $key["symbol"], $key["price"], $key["rounds"], $key["open_type"]), 2);
                        }

                        if ($key['open_type'] == 2) {
                            $key['lever_increase'] = $price * $key['last_qb_token'] - $key['total'];
                        } else {
                            $key['lever_increase'] = $key['total'] - $price * $key['last_qb_token'];
                        }
                        $key['lever_increase'] = round($key['lever_increase'], 2);

                        $value['loss'] += $key['lever_increase'];
                    }
                }

                $param['loss'] += $value['loss'];
                $value['loss'] = getBcRound($value['loss'], 2);

            }
//            $param['count'] =$list['count'];
        }
        $param['loss'] = getBcRound($param['loss'], 2);
        $list['param'] = $param;
        return $list;

    }

    public function setupInfo($user_id)
    {
        $param = $this->input;
        $validate = getValidate([
            "symbol" => "require",
            "platform_id" => "require",
            "open_type" => "require",
        ], [
            "symbol.require" => '请选择币种',
            "platform_id.require" => '请选择平台',
            "open_type.require" => '请选择开仓方向',
        ]);
        if ($validate["code"] == 1) {
            return $validate;
        }
        $info = $this->model->where($param)->where('user_id', $user_id)->find();
        $info['type'] = $this->type[$info['type_id']];
        $list = [];
        $rounds = [];
        if ($info) {
            $buy = SingleLoopBuy::where('setup_id', $info['id'])->where('is_sell', 0)->where('is_deal', 2)->select()->toArray();
            $list['total'] = 0;//持仓总额
            $list['sum'] = 0;//持仓总量
            $list['rounds'] = 1;//层级
            $list['price'] = 0;//均价
            $list['profit'] = 0;//盈利
            $list['increase'] = 0;//整仓涨幅
            //当前币价
            $list['new_price'] = $price = symbol_price($param['platform_id'], $param['symbol']);

            foreach ($buy as $key) {
                $key['total'] = round($key['last_qb_token'] * $key['price'], 6);
                $list['total'] += $key['last_qb_token'] * $key['price'];
                $list['sum'] += $key['last_qb_token'];
                $list['price'] += $key['price'];
                $list['rounds'] = $list['rounds'] > $key['rounds'] ? $list['rounds'] : $key['rounds'];
                if ($key['open_type'] == 2) {
                    $key['lever_increase'] = $price * $key['last_qb_token'] - $key['last_qb_token'] * $key['price'];
                } else {
                    $key['lever_increase'] = $key['last_qb_token'] * $key['price'] - $price * $key['last_qb_token'];
                }

                $list['profit'] += $key['lever_increase'];
            }
            $list['profit'] = getBcRound($list['profit'], 2);
            if (count($buy)) {
                $list['price'] = round($list['price'] / count($buy), 6);
                if ($list['price']) {
                    $list['increase'] = getBcRound(symbol_price_per($info["platform_id"], $info["symbol"], $list['price'], $info["lever"], $info["open_type"]), 2);

                }
            }


            $list['sum'] = round($list['sum'], 6);
            $list['total'] = round($list['total'], 6);

            $list['ensure'] = round($list['total'] / $info['lever'], 6);
//            if ($info['open_type'] == 2) {
//                $list['profit'] = $list['total'] - $price;
//            } elseif ($info['open_type'] == 3) {
//                $list['profit'] = $price - $list['total'];
//            }
            $list['open_type'] = $info['open_type'];
            $list['lever'] = $info['lever'];
            $list['symbol'] = $param['symbol'];
            $list['platform_id'] = $param['platform_id'];
//获取最高层级
            $last = SingleLoopBuy::where('setup_id', $info['id'])->where('is_sell', 0)->where('is_deal', 2)->order('rounds DESC')->find();
            $rounds['lever'] = numToWord(0);
            $rounds['last_qb_token'] = 0;
            $rounds['total'] = 0;
            $rounds['increase'] = 0;
            $rounds['price'] = 0;
            $rounds['up_stop_per'] = 0;
            $rounds['down_stop_per'] = 0;
            $rounds['up_stop_price'] = 0;
            $rounds['down_stop_price'] = 0;

            if ($last) {
                $rounds['lever'] = numToWord($list['rounds']);

                $rounds['last_qb_token'] = $last['last_qb_token'];
                $rounds['total'] = round($last['last_qb_token'] * $last['price'], 6);
                $rounds['increase'] = 0;


                if ($last["price"]) {
                    $rounds['increase'] = getBcRound(symbol_price_per($last["platform_id"], $last["symbol"], $last["price"], $last["lever"], $last["open_type"]), 2);
                }


                $rounds['price'] = $last['price'];
                $rounds['up_stop_per'] = $last['up_stop_per'] * 100;
                $rounds['down_stop_per'] = $last['down_stop_per'] * 100;
                $rounds['up_stop_price'] = get_round($last['up_stop_price'], 6);
                $rounds['down_stop_price'] = get_round($last['down_stop_price'], 6);
//                $rounds['down_stop_price'] = $last['down_stop_price'];
            }


        }

        return $setupInfo = [
            'info' => $info,
            'list' => $list,
            'rounds' => $rounds,
        ];
    }


    public function round()
    {

        $map = [];
        $param = $this->input;
        //设置查询类型  如果单词循环 添加is_run = 1  停止补单is_run =  0/2
//        $is_run = isset($param['is_run']) ? (int)$param['is_run'] : 0;
//        if ($is_run) {
//            $map[] = ['is_run', 'in', [0, 2]];
//        }
//            $is_loop = getter($param, 'is_loop');
//        if ($is_loop === 0 || $is_loop === '0' || $is_loop) {
//            $map[] = ['is_run', '=', 1];
//        }

        if ($param['is_run'] == 2) {
            $map[] = ['is_run', 'in', [0, 2]];
        } else {
            $map[] = ['is_run', 'in', [1]];
        }


        $list = parent::getList($map, [], 0, 1);


        if ($list["data"]) {
            foreach ($list["data"] as &$value) {
                $value['token'] = QuantSymbol::where('title', $value['symbol'])->value('token');//持仓总额
                $buy = SingleLoopBuy::where('setup_id', $value['id'])->where('is_sell', 0)->where('is_deal', 2)->select()->toArray();
                $value['total'] = 0;//持仓总额
                $value['sum'] = 0;//持仓总量
                $value['level'] = 1;//层级
                $value['price'] = 0;//均价
                $value['all'] = 0;//整仓涨幅

                foreach ($buy as $key) {
                    $value['total'] += $key['last_qb_token'] * $key['price'];
                    $value['sum'] += $key['last_qb_token'];
                    $value['price'] += $key['price'];
                    $value['level'] = $value['level'] > $key['rounds'] ? $value['level'] : $key['rounds'];
                }
                if (count($buy)) {
                    $value['price'] = round($value['price'] / count($buy), 6);
                    if ($value["price"]) {
                        $value['all'] = getBcRound(symbol_price_per($value["platform_id"], $value["symbol"], $value["price"], $value["lever"], $value["open_type"]), 2);
                    }

                }

                $value['total'] = round($value['total'], 6);
                $value['sum'] = round($value['sum'], 6);
                $value['new_price'] = $price = symbol_price($value['platform_id'], $value['symbol']);


//                }

            }
        }
        return $list;
    }

    //层级浮亏记录
    public function floating($user_id)
    {
        $param = $this->input;
        $validate = getValidate([
            "setup_id" => "require",
        ], [
            "setup_id.require" => '请选择策略',
        ]);
        if ($validate["code"] == 1) {
            return $validate;
        }
        $info = $this->model->where('id', $param['setup_id'])->find();
        //获取所有未卖出的层级
        $list = SingleLoopBuy::where('setup_id', $param['setup_id'])->where('is_deal', 2)->where('is_sell', 0)->order('rounds ASC')->select();
        $price = symbol_price($info['platform_id'], $info['symbol']);
        if ($list) {
            $sum = 0;
            $count = count($list);
            foreach ($list as &$key) {

                $key['total'] = round($key['last_qb_token'] * $key['price'], 6);

                $key['increase'] = 0;
                if ($key["price"]) {

                    $key['increase'] = getBcRound(symbol_price_per($key["platform_id"], $key["symbol"], $key["price"], $key["lever"], $key["open_type"]), 2);
                }
                $key['lever'] = numToWord($key['rounds']);

                if ($key['open_type'] == 2) {
                    $key['lever_increase'] = $price * $key['last_qb_token'] - $key['total'];
                } else {
                    $key['lever_increase'] = $key['total'] - $price * $key['last_qb_token'];
                }

                $key['lever_increase'] = round($key['lever_increase'], 2);
                $key['up_stop_per'] = $key['up_stop_per'] * 100;
                $key['down_stop_per'] = $key['down_stop_per'] * 100;
                $key['up_stop_price'] = get_round($key['up_stop_price'], 6);
                $key['down_stop_price'] = get_round($key['down_stop_price'], 6);

                $sum += $key['lever_increase'];
            }
        }
        return ['list' => $list, 'data' => ['sum' => getBcRound($sum, 2), 'count' => $count]];
    }

//策略详情
    public function getInfo()
    {
        $param = $this->input;

        //获取当前策略
        $list = $this->model->getInfo($param['setup_id']);
        if ($list) {
            if ($list['type_id'] == 5) {
                //获取自定义策略
                $lever_config = CustomPolicy::where(["user_id" => $list["user_id"], "symbol" => $list["symbol"]])->find();
                $list['type'] = json_decode($lever_config['config'], true);

            }
            $list['token'] = QuantSymbol::where(["title" => $list["symbol"]])->value('token');
            $setup = $this->model->where(['user_id' => $list['user_id'], 'symbol' => $list['symbol'], 'platform_id' => $list['platform_id']])->select();


//            if($setup){
//
//                foreach ($setup as $value){
//                    $buy = SingleLoopBuy::where('setup_id',$value['id'])->where('is_sell',0)->select()->toArray();
//                    $value['total']=0;//持仓总额
//                    $value['sum']=0;//持仓总量
//                    $value['rounds'] = 1;//层级
//                    $value['price'] = 0;//均价
//
//                    foreach ($buy as $key){
//                        $value['total'] += $key['last_qb_token']*$key['price'];
//                        $value['sum'] += $key['last_qb_token'];
//                        $value['price'] += $key['price'];
//                        $value['rounds'] = $value['rounds']>$key['rounds']?$value['rounds']:$key['rounds'];
//                    }
//                    $value['price']=round($value['price'],6);
//                    $value['sum']=round($value['sum'],6);
//                    $value['new_price']= $price = symbol_low_price($value['platform_id'],$value['symbol']);
//                    $value['increase']=0;
//                    if($value['price']){
//                        $value['increase']= (round(($value['price']-$price)/$value['price'],6))*100;
//                    }
//                    $arr[]=[
//                        'total'=>$value['total'],
//                        'sum'=>$value['sum'],
//                        'rounds'=>$value['rounds'],
//                        'increase'=>$value['increase'],
//                        'price'=>$value['price'],
//                        'open_type'=>$value['open_type'],
//                        'lever'=>$value['lever'],
//                        'symbol'=>$value['symbol'],
//                        'is_run'=>$value['is_run'],
//                        'new_price'=>$value['new_price'],
//                    ];
//
//                }
//            }
            $list['setup'] = [];

        }

        return $list;
    }

//代币列表获取策略
    public function getSymbol($user_id)
    {
        $param = $this->input;
        //获取当前策略
        $list = $this->model->where(['user_id' => $user_id, 'symbol' => $param['symbol'], 'platform_id' => $param['platform_id']])->find();

        if ($list) {
            if ($list['type_id'] == 5) {
                //获取自定义策略
                $lever_config = CustomPolicy::where(["user_id" => $list["user_id"], "symbol" => $list["symbol"]])->find();
                $list['type'] = json_decode($lever_config['config'], true);
            }
            $setup = $this->model->where(['user_id' => $list['user_id'], 'symbol' => $list['symbol'], 'platform_id' => $list['platform_id']])->select();
            $arr = [];
            if ($setup) {
                foreach ($setup as $value) {
                    $buy = SingleLoopBuy::where('setup_id', $value['id'])->where('is_sell', 0)->select()->toArray();
                    $value['total'] = 0;//持仓总额
                    $value['sum'] = 0;//持仓总量
                    $value['rounds'] = 1;//层级
                    $value['price'] = 0;//均价

                    foreach ($buy as $key) {
                        $value['total'] += $key['last_qb_token'] * $key['price'];
                        $value['sum'] += $key['last_qb_token'];
                        $value['price'] += $key['price'];
                        $value['rounds'] = $value['rounds'] > $key['rounds'] ? $value['rounds'] : $key['rounds'];
                    }

                    $value['price'] = round($value['price'], 6);
                    $value['sum'] = round($value['sum'], 6);
                    $value['new_price'] = $price = symbol_price($value['platform_id'], $value['symbol']);
                    $value['token'] = QuantSymbol::where('title', $value['symbol'])->value('token');
                    $value['increase'] = 0;
                    $value['price'] = round($value['price'] / count($buy), 6);
                    if ($value['price']) {
                        $value['increase'] = getBcRound(symbol_price_per($value["platform_id"], $value["symbol"], $value["price"], $value["lever"], $value["open_type"]), 2);
                    }
                    $arr[] = [
                        'id' => $value['id'],
                        'total' => $value['total'],
                        'sum' => $value['sum'],
                        'rounds' => $value['rounds'],
                        'increase' => $value['increase'],
                        'price' => $value['price'],
                        'open_type' => $value['open_type'],
                        'lever' => $value['lever'],
                        'symbol' => $value['symbol'],
                        'is_run' => $value['is_run'],
                        'new_price' => $value['new_price'],
                        'platform_id' => $value['platform_id'],
                        'token' => $value['token'],
                    ];

                }
            }
            $list['setup'] = $arr;

        } else {
            return ['code' => 1];
        }

        return $list;
    }


    public function del($user_id)
    {
        // 参数
        $param = $this->input;
        // 记录ID
        $ids = getter($param, "id");
        $run = $this->model->where('id', $ids)->value('is_run');
        if ($run == 2) {
            $count = SingleLoopBuy::where(["is_sell" => 0, "is_deal" => 2, "setup_id" => $ids])->count();
            if ($count > 0) {
                return [
                    "code" => 1,
                    "msg" => "请先平仓卖出"
                ];
            }
        }
        if ($run == 1) {
            return [
                "code" => 1,
                "msg" => "请先平仓卖出"
            ];
        }
        $this->model->where('id', $ids)->where('user_id', $user_id)->delete();

        return true;
    }


    //获取当前策略剩余层级
    public  function getLast(){
        // 参数
        $param = $this->input;
        // 记录ID
        $ids = getter($param, "id");

        $sum  = SingleLoopBuy::where('setup_id', $ids)->where('is_deal', 2)->where('is_sell', 0)->sum('last_qb_token');
        $list = SingleLoopBuy::where('setup_id', $ids)->where('is_deal', 2)->where('is_sell', 0)->order('rounds DESC')->column('last_qb_token');

        if($list){
            foreach ($list as &$k){
                $k = get_round($k,6);
            }
        }

        return $list =['code'=>0,'data'=>['sum'=>$sum,'list'=>$list]];
    }

}
