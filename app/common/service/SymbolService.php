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
use app\common\model\QuantMarketBuy;
use app\common\model\QuantMarketItem;
use app\common\model\QuantMarketSell;
use app\common\model\QuantSetup;
use app\common\model\QuantSymbol;
use app\common\model\TradeProfit;
use ccxt\Exchange;
use ccxt\okex5;
use ccxt\binance;

/**
 * 代币种类管理-服务类
 * @author 测试
 * @since: 2022/01/23
 * Class QuantSymbolService
 * @package app\adminapi\service
 */
class SymbolService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new QuantSymbol();
    }

    /**
     * 获取数据列表
     * @return array
     * @since 2022/01/23
     * @author 测试
     */
    public function getList()
    {
        $param = $this->input;

        // 查询条件
        $map = [];


        return parent::getList($map); // TODO: Change the autogenerated stub
    }

    //初始化price
    public function init_price()
    {
        $list = $this->model->select();
        foreach ($list as $key => $value) {

            if (symbol_price(2, $value["title"])) {
                $this->model->where(["id" => $value["id"]])->update(["ohprice" => symbol_price(2, $value["title"]), "o_24" => symbol_24(2, $value["title"]), "o_open_price" => symbol_open_price(2, $value["title"])]);
            }
            if (symbol_price(3, $value["title"])) {
                $this->model->where(["id" => $value["id"]])->update(["bhprice" => symbol_price(3, $value["title"]), "b_24" => symbol_24(3, $value["title"]), "b_open_price" => symbol_open_price(3, $value["title"])]);
            }
        }
    }


    //public function


    public function init_cache_symbol_okex($list)
    {
        foreach ($list as $key => $value) {

            $ohtitle = "O/" . $value["title"] . "/SWAP";
            $otitle = "O/" . $value["title"];
//            if (cache($ohtitle)) {
//                if (cache($ohtitle) > 0) {
//                    QuantSymbol::where(["id" => $value["id"]])->update(["ohprice" => cache($ohtitle)]);
//                }
//            }
//            if (cache($otitle)) {
//                if (cache($otitle) > 0) {
//                    QuantSymbol::where(["id" => $value["id"]])->update(["oprice" => cache($otitle)]);
//                }
//            }
        }
    }

    public function init_cache_symbol_binance($list)
    {
        $b = json_decode(cache("binance-line"), true);
        $bh = json_decode(cache("binance-line-swap"), true);
        $return_b = $return_bh = [];

        if (is_array($b)) {
            foreach ($b as $val) {
                $return_b[$val["s"]] = $val["c"];
            }
        }
        if (is_array($bh)) {
            foreach ($bh as $val) {
                $return_bh[$val["s"]] = $val["c"];
            }
        }
//        foreach ($list as $key => $value) {
//            $symbol = str_replace("/", "", $value["title"]);
//            if (isset($return_b[$symbol])) {
//                if ($return_b[$symbol] >= 0) {
//                    QuantSymbol::where(["id" => $value["id"]])->update(["bprice" => $return_b[$symbol]]);
//                }
//            }
//            if (isset($return_bh[$symbol])) {
//                if ($return_bh[$symbol] >= 0) {
//                    QuantSymbol::where(["id" => $value["id"]])->update(["bhprice" => $return_bh[$symbol]]);
//                }
//            }
//        }
    }


    //初始化处理欧易的数据
    public function init_open_day()
    {
        $market = new okex5();
        $results = $market->publicGetMarketTickers([
            "instType" => "SPOT"
        ]);
        //处理当日开盘价
        foreach ($results["data"] as $key => $val) {
            $symbol = str_replace("-", "/", $val["instId"]);
            $this->model->where(["title" => $symbol])->update(["o_open_price" => $val["sodUtc8"]]);
        }
        //顺便处理币安的最小购买数量
        $market = new binance();
        $symbol = [];
        $results = $market->fapiPublicGetExchangeInfo();
        foreach ($results["symbols"] as $key => $val) {
            $symbol[$val["symbol"]] = $val;
        }
        $list = QuantSymbol::select();
        foreach ($list as $key => $value) {
            $title = str_replace("/", "", $value["title"]);
            if (isset($symbol[$title])) {
                $data = $symbol[$title];
                //print_r($data);
                if ($value["bh_min_price"] === 0) {
                    QuantSymbol::where(["id" => $value["id"]])->update(["bh_min_price" => $data["filters"][1]["minQty"], "decimal" => $data["quotePrecision"]]);
                }
            }
        }
    }


    //初始化处理欧易的数据
    public function init_second_okex()
    {
        $market = new okex5();
//        $results = $market->fetch_tickers();
//        foreach ($results as $key => $val) {
//            if (in_array($key, $list)) {
//                QuantSymbol::where(["title" => $key])->update(["oprice" => $val["close"]]);
//            }
//        }

        $list = QuantSymbol::where(["is_online" => 1])->column("title", "id");
        $market = new okex5();
        //,fetch_markets_by_type
        if (env("x-simulated-trading") == 1) {
            $market->headers = [
                "x-simulated-trading" => "1"
            ];
        }
        //publicGetPublicInstruments

        $results = $market->publicGetPublicInstruments(["instType" => "SWAP"]);
//            print_r($results);
//            exit;
        foreach ($results["data"] as $key => $val) {
            $symbol = str_replace("-", "/", $val["instId"]);
            $symbol = str_replace("/SWAP", "", $symbol);
            // $symbol = str_replace("-", "/", $val["instId"]);
            if (in_array($symbol, $list)) {
                QuantSymbol::where(["title" => $symbol])->update(["oh_max_lever" => $val["lever"]]);
            }
        }
        $results = $market->fetch_tickers_by_type("SWAP");
        //print_r($results);
        //exit;
        foreach ($results as $key => $val) {
            $symbol = explode(":", $key);
            if (in_array($symbol[0], $list)) {
                QuantSymbol::where(["title" => $symbol[0]])->update(["ct_val" => $val["vwap"]]);
                $info = QuantSymbol::where(["title" => $symbol[0]])->find();
                cache($symbol[0], $info->toArray());
            }
        }
    }

    //初始化币安数据
    public function init_second_binance($list)
    {
        //处理币安现货价格
        $market = new binance();
        $results = $market->fetch_tickers();
        foreach ($results as $key => $val) {
            if (in_array($key, $list)) {
                QuantSymbol::where(["title" => $key])->update(["bprice" => $val["close"]]);
            }
        }
        //处理币安合约价格
        $market = new binance();
        $results = $market->fetch_tickers();
        $market->options = [
            "defaultType" => "future",
            "adjustForTimeDifference" => ""
        ];
        foreach ($results as $key => $val) {
            if (in_array($key, $list)) {
                QuantSymbol::where(["title" => $key])->update(["bhprice" => $val["close"]]);
            }
        }
    }


    /**
     * 首页随机展示10条代币类型
     */
    public function round()
    {
        $param = $this->input;
        //总条数
        $map = [];
        // 平台名称
        $title = isset($param['title']) ? trim($param['title']) : '';
        if ($title) {
            $title = strtoupper($title);
            $map[] = ['title', 'like', "%{$title}%"];
        }

        $platform_id = isset($param['platform_id']) ? (int)$param['platform_id'] : 2;

        $platform_type = isset($param['platform_type']) ? (int)$param['platform_type'] : 0;
        if ($platform_id == 2) {
            $map[] = ['ohprice', '>', 0];
            $map[] = ['o_24', '>', 0];
        } else {
            $map[] = ['bhprice', '>', 0];
            $map[] = ['b_24', '>', 0];
        }

        $result = $this->model->where($map)->order('order desc')->page(PAGE, PERPAGE)->column("id");

        $list = [];

        if (is_array($result)) {
            foreach ($result as $val) {
                $info = $this->model->getInfo($val);
                $list[] = $info;
            }
        }
        //获取数据总数
        $count = $this->model->where($map)->count();

        if ($list) {
            foreach ($list as &$value) {

                $value['num'] = getBcRound($value['o_open_price'] * 1000, 0);

                $value['round_price'] = 0;
                $value['rete'] = 0;
                if ($platform_id == 2) {
                    if ($value['o_open_price'] > 0) {
                        $value['round_price'] = round(($value['ohprice'] - $value['o_open_price']) / $value['o_open_price'] * 100, 4);
                        $value['rete'] = round($value['ohprice'] * get_config('exchange_rate', 1), 6);
                    }

                } else {

                    if ($value['b_open_price'] > 0) {
                        $value['round_price'] = round(($value['bhprice'] - $value['b_open_price']) / $value['b_open_price'] * 100, 4);
                        $value['rete'] = round($value['bhprice'] * get_config('exchange_rate', 1), 6);
                    }
                }
            }
        }
        return $message = array(
            "msg" => '操作成功',
            "code" => 0,
            "data" => $list,
            "count" => $count ?? 0,
        );
    }

    //获取行情
    public function getApiList($userId)
    {
        $validate = getValidate([
            'platform_id' => 'require',
            'platform_type' => 'require'
        ], [
            'platform_id.require' => '请选择平台',
            'platform_type.require' => '请选择类别',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $arr_result = [];
        $param = $this->input;
        $query = QuantSymbol::where(["is_online" => 1])->field("id,title,oprice,ohprice,bprice,bhprice");
        if (isset($param["keyword"])) {
            $query->where("title", "like", '%' . strtoupper($param['keyword']) . '%');
        }

        //if($this->input[""])
        $this->input['type'] = isset($this->input['type']) ? $this->input['type'] : "all";
        $list = $query->select()->toArray();
//        var_dump($list);die;
        foreach ($list as $key => $value) {
            $item = $this->stock_item($value["title"], $userId, $this->input["platform_id"], $this->input["platform_type"], $value);
//            var_dump($item);die;
            //全部
            if ($this->input['type'] == 'all') {
                array_push($arr_result, $item);
            }
            // 策略循环
            if ($this->input['type'] == 'loop' && $item['qb_source'] > 0 && $item['is_loop'] == 1) {
                array_push($arr_result, $item);
            }
            // 单次策略
            if ($this->input['type'] == 'single' && $item['qb_source'] > 0 && $item['is_loop'] == 0) {
                array_push($arr_result, $item);
            }
            // 停止补单
            if ($this->input['type'] == 'stop' && $item['qb_source'] > 0 && $item['is_run'] == 0) {
                array_push($arr_result, $item);
            }
            // 我的策略
            if ($this->input['type'] == 'self' && $item['qb_source'] > 0) {
                array_push($arr_result, $item);
            }
        }
        if ($arr_result) {
            foreach ($arr_result as $key => $row) {
                $volume[$key] = $row['is_run'];
            }
            array_multisort($volume, SORT_DESC, $arr_result);
        }

        return $arr_result;
    }

    private function stock_item($symbol, $userId, $platform_id, $platform_type, $price)
    {
        $result = [
            'symbol' => $symbol,
            'qb_source' => 0,
            'qb_to' => '0.000000',
            'qb_float' => '0.0000',//浮亏
            'per_float' => 0,
            'is_loop' => 0,
            'is_run' => 0,
            'strategy_id' => 0,//策略类型
            'platform_type' => 0,//现货或者U本位
            'open_type' => 0,//开仓方向 只有合约需要
            'lever' => 0,//合约倍数
            'up_back_scale' => 0,//止损比例
        ];
        $data_market_item = QuantMarketItem::where('symbol', $symbol)->where('user_id', $userId)->where(["platform_id" => $platform_id, "platform_type" => $platform_type])->where('is_sale', 0)->find();
//        var_dump($data_market_item);die;
        if (empty($data_market_item)) {
            return $result;
        }
        $data_setup = QuantSetup::where('symbol', $symbol)->where('user_id', $userId)->where(["platform_id" => $platform_id, "platform_type" => $platform_type])->find();
        //是否开启
        $result['is_run'] = $data_setup['is_run'];
        //是否轮询
        $result['is_loop'] = $data_setup['is_loop'];
        //USDT数量
        $result['qb_source'] = $data_market_item['qb_source'];
        //购买数量
        $result['qb_to'] = $data_market_item['qb_to'];

        if ($data_market_item["platform_id"] == 2) {
            if ($data_market_item["platform_type"] == 2) {
                $now_price = $price["ohprice"];
            } else {
                $now_price = $price["oprice"];
            }
        } else {
            if ($data_market_item["platform_type"] == 2) {
                $now_price = $price["bhprice"];
            } else {
                $now_price = $price["bprice"];
            }
        }


        //一共花了多少U
        $result['qb_usdt'] = $result['qb_to'] * $now_price;

        //浮亏
        $result['qb_float'] = $result['qb_usdt'] - $result['qb_source'];
        //涨幅
        $result['per_float'] = $result['qb_float'] * 100 / $result['qb_source'];
        $result['per_float'] = getBcRound($result['per_float'], 2);


        if ($data_setup["platform_type"] == 2) {
            if ($data_setup["open_type"] != 3) {
                $qb_float = $now_price - $data_market_item['price'];
                $result['per_float'] = getBcRound($qb_float * 100 / $data_market_item['price'] * $data_market_item['lever'], 2); //;
                $result['qb_float'] = $data_market_item['bail'] * $result['per_float'] / 100;
            } else {
                $qb_float = $data_market_item['price'] - $now_price;
                //$result['qb_float'] = $qb_float / $data_market_item['lever'];
                $result['per_float'] = getBcRound($qb_float * 100 / $now_price * $data_market_item['lever'], 2); //* $data_market_item['lever'];
                $result['qb_float'] = $data_market_item['bail'] * $result['per_float'] / 100;
            }
        }


        $result['qb_float'] = getBcRound($result['qb_float'], 2);


        $result['strategy_id'] = $data_market_item['strategy_id'];//策略类型
        $result['platform_type'] = $data_market_item['platform_type'];//现货或者U本位
        $result['open_type'] = $data_market_item['open_type'];//开仓方向 只有合约需要
        $result['lever'] = $data_market_item['lever'];//合约倍数
        $result['up_back_scale'] = $data_market_item['up_back_scale'];//止损比例
        return $result;
    }

    //获取持仓详情
    public function stock_load($userId)
    {

        $validate = getValidate([
            'setup_id' => 'require',
        ], [
            'setup_id.require' => '请选择策略',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $user_qb_usdt = Member::where('id', $userId)->value('qb_usdt');

        ///当前策略
        $setup = QuantSetup::where('id', $this->input['setup_id'])->find();
        //获取当前的币种
        $symbol = QuantSymbol::where('title', $setup['symbol'])->field("id,title,oprice,ohprice,bprice,bhprice")->find();

        if ($setup["platform_id"] == 2) {
            if ($setup["platform_type"] == 2) {
                $now_price = $symbol["ohprice"];
            } else {
                $now_price = $symbol["oprice"];
            }
        } else {
            if ($setup["platform_type"] == 2) {
                $now_price = $symbol["bhprice"];
            } else {
                $now_price = $symbol["bprice"];
            }
        }
        $result = [
            'qb_source' => 0,//持仓金额
            'qb_to' => 0,//购买数量
            'price' => 0, //持仓均价
            'start_price' => 0, //开仓价格 合约
            'stop_price' => 0, //平仓价格 合约
            'rounds' => 0,//补仓单数
            'now_price' => $now_price,//当前价格
            'per_float' => 0,//当前盈利率
            'qb_float' => 0,//当前盈利率
            'qb_price' => 0,//当前盈利率
            'symbol' => $setup['symbol'],//
            'create_time' => $setup['create_time'],//
            'is_run' => $setup['is_run'],//
            'lever' => $setup['lever'],//
            'sell' => 0,//交易数量
            'bail' => 0,//保证金 合约才有
            'usdt' => 0//总收益
        ];

        //查询详情
        $data_market_item = QuantMarketItem::where(["setup_id" => $this->input['setup_id']])->find();
        if (!$data_market_item) {
            $result['now_price'] = round($now_price, 4);
            return $result;
        }
        $result = $data_market_item;
        //如果已卖出
        if ($data_market_item['is_sale'] == 1) {
            $price = QuantMarketSell::where('is_deal', '=', "2")->where(["setup_id" => $this->input['setup_id']])->order('id', 'desc')->value('price');
            $result['stop_price'] = $price;
        }
        //获取持仓次数
        $rounds = QuantMarketBuy::where('is_deal', '<', "4")->where(["setup_id" => $this->input['setup_id']])->order('rounds', 'desc')->value('rounds');
        $result['rounds'] = 0;
        if ($rounds) {
            $result['rounds'] = $rounds - 1;
        }
        //获取交易次数
        $sell = QuantMarketBuy::where(["setup_id" => $this->input['setup_id']])->count();
        $result['sell'] = 0;
        if ($sell) {
            $result['sell'] = $sell;
        }

        //获取交易次数
        $usdt = TradeProfit::where(["setup_id" => $this->input['setup_id']])->sum('profit_usdt');
        $result['usdt'] = 0;
        if ($usdt) {
            $result['usdt'] = $usdt;
        }


        if ($data_market_item["platform_type"] == 1) {
            $qb_usdt = $data_market_item['qb_to'] * $now_price;
            $qb_float = $qb_usdt - $data_market_item['qb_source'];
            $result['per_float'] = getBcRound($qb_float * 100 / $data_market_item['qb_source'], 2);
            $result['qb_float'] = getBcRound($data_market_item['bail'] * $result['per_float'] / 100, 2);

        } else {
            if ($data_market_item["open_type"] != 3) {

                $qb_float = $now_price - $data_market_item['price'];
                $result['per_float'] = getBcRound($qb_float * 100 / $data_market_item['price'] * $data_market_item['lever'], 2);// ;
                $result['qb_float'] = getBcRound($data_market_item['bail'] * $result['per_float'] / 100, 2);
            } else {
                $qb_float = $data_market_item['price'] - $now_price;
                $result['per_float'] = getBcRound($qb_float * 100 / $now_price * $data_market_item['lever'], 2);
                $result['qb_float'] = getBcRound($data_market_item['bail'] * $result['per_float'] / 100, 2);
            }
            //合约算法
        }
        $result['now_price'] = getBcRound($now_price, 4);
        $result['start_price'] = $data_market_item['price'];
        $result['user_qb_usdt'] = $user_qb_usdt;
        $result['is_run'] = $setup['is_run'];
        $result['qb_source'] = sprintf("%.2f", $result['qb_source']);
        return $result;
    }


}
