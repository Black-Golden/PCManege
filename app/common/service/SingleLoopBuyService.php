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
use app\common\model\SingleLoopBuy;
use app\common\model\SingleLoopSell;

/**
 * 交易买入管理-服务类
 * @author 测试
 * @since: 2022/11/16
 * Class SingleLoopBuyService
 * @package app\adminapi\service
 */
class SingleLoopBuyService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new SingleLoopBuy();
    }

    public $list = [];

    public function next($setup_id, $sell_token, $point_number, $rounds = 0)
    {
        if ($rounds == 0) {
            $info = SingleLoopBuy::where(["setup_id" => $setup_id, "is_deal" => 2, "is_sell" => 0])->order("rounds", "asc")->find();
        } else {
            $info = SingleLoopBuy::where(["setup_id" => $setup_id, "is_deal" => 2, "is_sell" => 0, "rounds" => $rounds])->order("rounds", "asc")->find();
        }
        if ($info) {
            $info = $info->toArray();
            $info["is_sell"] = 0;
            $info["sell_token"] = $sell_token;
            $info["do_last_qb_token"] = getBcRound($info["last_qb_token"] - $sell_token, $point_number);
            if ($info["do_last_qb_token"] <= 0) {
                $info["do_last_qb_token"] = 0;
                $info["is_sell"] = 1;
                $this->next($setup_id, abs($info["do_last_qb_token"]), $point_number, $info["rounds"] + 1);
            }
            $this->list[] = $info;
        }
    }


    public function next_desc($setup_id, $sell_token, $point_number, $rounds = -1)
    {
        if ($rounds == -1) {
            $info = SingleLoopBuy::where(["setup_id" => $setup_id, "is_deal" => 2, "is_sell" => 0])->order("rounds", "desc")->find();
        } else {
            $info = SingleLoopBuy::where(["setup_id" => $setup_id, "is_deal" => 2, "is_sell" => 0, "rounds" => $rounds])->order("rounds", "desc")->find();
        }


        if ($info && $rounds != 0) {
            $info = $info->toArray();
            $info["is_sell"] = 0;
            $info["sell_token"] = $sell_token;
            $info["do_last_qb_token"] = getBcRound($info["last_qb_token"] - $sell_token, $point_number);
            if ($info["do_last_qb_token"] <= 0) {
                $info["do_last_qb_token"] = 0;
                $info["is_sell"] = 1;
                $this->next_desc($setup_id, abs($info["do_last_qb_token"]), $point_number, $info["rounds"] - 1);
            }
            $this->list[] = $info;
        }
    }

    public function buy_next_data($setup_id, $sell_token, $point_number, $rounds = 0)
    {
        $this->list = [];
        $this->next($setup_id, $sell_token, $point_number, $rounds);
        return $this->list;
    }

    public function buy_next_data_desc($setup_id, $sell_token, $point_number, $rounds = -1)
    {
        $this->list = [];
        $this->next_desc($setup_id, $sell_token, $point_number, $rounds);
        return $this->list;
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
        $order_id = isset($param['order_id']) ? trim($param['order_id']) : '';
        if ($order_id) {
            $map[] = ['order_id', 'like', "%{$order_id}%"];
        }
        // 平台名称
        $user_name = isset($param['user_name']) ? trim($param['user_name']) : '';
        if ($user_name) {
            $ids = Member::where('username', 'like', "%{$user_name}%")->column('id');
            $map[] = ['user_id', 'in', $ids];
        }
        // 是否成交
        $is_deal = isset($param['is_deal']) ? (int)$param['is_deal'] : 0;
        if ($is_deal) {
            $map[] = ['is_deal', '=', $is_deal];
        }
        // 是否成交
        $platform_id = isset($param['platform_id']) ? (int)$param['platform_id'] : 0;
        if ($platform_id) {
            $map[] = ['platform_id', '=', $platform_id];
        }

        // 是否成交
        $open_type = isset($param['open_type']) ? (int)$param['open_type'] : 0;
        if ($open_type) {
            $map[] = ['open_type', '=', $open_type];
        }

        // 是否卖出
        $is_sell = isset($param['is_sell']) ? (int)$param['is_sell'] : 0;
        if ($is_sell) {
            $map[] = ['is_sell', '=', $is_sell];
        }

        return parent::getList($map); // TODO: Change the autogenerated stub
    }


    //交易记录
    public function transaction($user_id)
    {
        $param = $this->input;
        // 查询条件
        $map = [];

        // 是否成交
        $symbol = isset($param['symbol']) ? (int)$param['symbol'] : 0;
        if ($symbol) {
            $map[] = ['symbol', '=', $symbol];
        }
        // 是否成交
        $setup_id = isset($param['setup_id']) ? (int)$param['setup_id'] : 0;
        if ($setup_id) {
            $map[] = ['setup_id', '=', $setup_id];
        }


        $page = isset($param['page']) ? (int)$param['page'] : 1;
        $SingleLoopBuy = new SingleLoopBuy();
        $SingleLoopSell = new SingleLoopSell();
        $bcount = $SingleLoopBuy->where('user_id', $user_id)->where($map)->where('is_deal', 2)->where('is_sell', 0)->count();
        $scount = $SingleLoopSell->where('user_id', $user_id)->where($map)->count();
        $count = $bcount > $scount ? $bcount : $scount;

        $blist = $SingleLoopBuy->where('user_id', $user_id)->where($map)->page($page)->where('is_deal', 2)->where('is_sell', 0)->order('id desc')->limit(5)->select();
        $blist = $blist ? $blist->toArray() : [];
        foreach ($blist as &$key) {
            $key['type'] = 'buy';
        }
        unset($row);
        $slist = $SingleLoopSell->where('user_id', $user_id)->where($map)->page($page)->order('id desc')->limit(5)->select();
        $slist = $slist ? $slist->toArray() : [];
        foreach ($slist as &$key1) {
            $key1['type'] = 'sell';
        }
        unset($key1);
        $list = array_merge($blist, $slist);


        if ($list) {
            foreach ($list as $key2 => $row) {
                $volume[$key2] = $row['create_time'];

            }
            array_multisort($volume, SORT_DESC, $list);

        }

        if ($setup_id) {
            $money = $SingleLoopSell->where('user_id', $user_id)->where($map)->sum('profit_usdt');
            return ['code' => 0, 'data' => $list, 'count' => $count, 'sum' => ['profit_usdt' => $money, 'num' => $bcount + $scount]];
        }
        return ['code' => 0, 'data' => $list, 'count' => $count];
    }


    public function next_desc1($setup_id, $sell_token, $point_number, $rounds = -1)
    {
        if ($rounds == -1) {
            $info = SingleLoopBuy::where(["setup_id" => $setup_id, "is_deal" => 2, "is_sell" => 0])->order("rounds", "desc")->find();
        } else {
            $info = SingleLoopBuy::where(["setup_id" => $setup_id, "is_deal" => 2, "is_sell" => 0, "rounds" => $rounds])->order("rounds", "desc")->find();
        }


        if ($info && $rounds != 0) {
            $info = $info->toArray();
            $info["is_sell"] = 0;
            $info["sell_token"] = $sell_token;
            $info["do_last_qb_token"] = getBcRound($info["last_qb_token"] - $sell_token, $point_number);
            if ($info["do_last_qb_token"] <= 0) {
                $info["do_last_qb_token"] = 0;
                $info["is_sell"] = 1;
                $this->next_desc($setup_id, abs($info["do_last_qb_token"]), $point_number, $info["rounds"] - 1);
            }
            $this->list[] = $info;
        }
    }

//    public function sell_every($setup_id, $sell_token, $point_number, $order = "asc", &$data = [])
//    {
//        $list = $this->model->where(["setup_id" => $setup_id, "is_deal" => 2, "is_sell" => 0])->order('rounds', $order)->select();
//        if ($list) {
//            //循环列表
//            foreach ($list->toArray() as $key => $value) {
//                $sell_token = getBcRound($sell_token, $point_number);
//                //把扣除前总量先保存 方便查看
//                $next = $sell_token;
//                $is_sell = 0;
//                //获取剩余
//                $last_qb_token_temp = getBcRound($sell_token - $value['last_qb_token'], $point_number);
//                if ($last_qb_token_temp > 0) {
//                    //all大于零 证明该单卖完
//                    $last_qb_token = 0;
//                    $is_sell = 1;
//                } else {
//                    //否则小于等于零  剩余 清零
//                    $last_qb_token_temp = 0;
//                    $last_qb_token = getBcRound($value['last_qb_token'] - $next,$point_number);
//                }
//                $info = $value;
//                $info["sell_token"] = $next;
//                $info["do_last_qb_token"] = $last_qb_token;
//                $info["is_sell"] = $is_sell;
//                $data[] = $info;
////                $data[$value['id']] = [
////                    'id' => $value['id'],//id
////                    'last_qb_token' => $value['last_qb_token'],//原订单last_qb_token
////                    'do_last_qb_token' => $last_qb_token,//剩余last_qb_token
////                    'last_qb_token_temp' => $last_qb_token_temp,//剩余总量
////                    'next' => $next,//扣除前总量
////                ];
//            }
//        }
//        return $data;
//
//    }



    public function sell_every($setup_id,$sell_token, $order = "asc", &$data = []){
        $list = $this->model->where(["setup_id" => $setup_id, "is_deal" => 2, "is_sell" => 0])->order('rounds', $order)->select();
        if($list){
            //循环列表
            //$sell_token = getBcRound($sell_token, $point_number);
            foreach ($list->toArray() as $value){
                $is_sell = 0;

                //把扣除前总量先保存 方便查看
                $next = $sell_token;
                //获取剩余
                $sell_token = $sell_token -$value['last_qb_token'];
                if($sell_token>=0){
                    //all大于零 证明该单卖完
                    $is_sell = 1;
                    $last_qb_token= 0;
                }else{
                    //否则小于等于零  剩余 清零
                    $sell_token =0;
                    $last_qb_token= $value['last_qb_token']-$next;
                }
                $info = $value;
                $info["is_sell"] = $is_sell;
                $info["do_last_qb_token"] = $last_qb_token;
                $info["sell_token"] = $next;
                $data[] = $info;
            }
        }
        return $data;

    }

    public function test($sell_token,&$data=[]){
        $list = $this->model->where('symbol','BTC/USDT')->where('open_type',2)->where('is_sell',0)->order('rounds DESC')->select();
        if($list){

            //循环列表
            foreach ($list as $key){
                //把扣除前总量先保存 方便查看
                $next = $sell_token;
                //获取剩余
                $sell_token = $sell_token -$key['last_qb_token'];
                if($sell_token>0){
                    //all大于零 证明该单卖完
                    $last_qb_token= 0;
                }else{
                    //否则小于等于零  剩余 清零
                    $sell_token =0;
                    $last_qb_token= $key['last_qb_token']-$next;
                }
                $data[$key['id']]=[
                    'id'=> $key['id'],//id
                    'last_qb_token1'=> $key['last_qb_token'],//原订单last_qb_token
                    'last_qb_token'=> $last_qb_token,//剩余last_qb_token
                    //'all'=> $all,//剩余总量
                    'next'=> $next,//扣除前总量
                ];
            }
        }
        return $data;

    }

}
