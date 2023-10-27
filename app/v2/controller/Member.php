<?php

namespace app\v2\controller;
use app\common\model\QuantCash;
use app\common\model\TradeProfit;
use app\common\service\AppupService;
use app\common\service\MemberService;
use app\common\service\PlatformMemberService;
use app\common\service\QuantCashService;
use app\common\service\QuantConfigService;
use app\common\service\QuantMoveService;
use app\common\service\QuantTransferService;
use app\common\service\QuantWalletService;
use app\common\service\TradeProfitService;
use think\facade\Request;
use think\facade\Db;
use app\extend\Error;
use app\extend\TronTools;
use ccxt\okex;
use ccxt\binance;

class Member extends Backend
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new MemberService();
    }


    public function bandapi()
    {
        return return_json($this->service->bandApi($this->user_id));
    }
    public function google_code()
    {
        return return_json($this->service->google_code());
    }

    public function get_kefu_config()
    {
        $service = new QuantConfigService();
        return return_json($service->get_kefu_config());
    }

    public function login()
    {
        return return_json($this->service->login());
    }
    public function btc()
    {
        return return_json(get_config('usdt_btc',1));
    }

    public function register()
    {
        return return_json($this->service->register());
    }

    public function forget()
    {
        return return_json($this->service->forget());
    }

    //谷歌验证

    public function googleCode()
    {
        return return_json($this->service->googleCode($this->user_id));
    }
    public function agreement()
    {
        return return_json($this->service->agreement($this->user_id));
    }

    //判断谷歌是否验证
    public function is_google()
    {
        return return_json($this->service->isGoogle($this->user_id));
    }

    //判断谷歌是否验证
    public function bind_google()
    {
        return return_json($this->service->bindGoogle($this->user_id));
    }

//修改密码
    public function set_password()
    {
        return return_json($this->service->set_password($this->user_id));
    }

//修改交易密码
    public function set_tradepassword()
    {
        return return_json($this->service->set_tradepassword($this->user_id));
    }
    //修改昵称

    public function nickname()
    {
        $result = $this->service->nickname($this->user_id);
        return return_json($result);
    }
//关于我们
    public function about_us(){
        return return_json(get_config('about_us',1));
    }
    //关于我们配置
    public function about(){
        return return_json(get_config('about_logo,about_title,about_notice'));
    }
    //修改头像
    public function headimg()
    {
        $result = $this->service->headimg($this->user_id);
        return return_json($result);
    }

    //邀请码
    public function qrcode(){

        $info = \app\common\model\Member::where('id',$this->user_id)->find();
        $result['recode'] = $info['recode'];

        $result["recode_qrcode"] =QRcarcodepng(RECORD_URL.$info["recode"]);

        return return_json($result);
    }


    //提现
    public function cash()
    {
        return return_json($this->service->cash($this->user_id));
    }

    //转账
    public function move()
    {
        return return_json($this->service->move($this->user_id));
    }

    // 协议
    public function secret()
    {
        return return_json(get_config('secret,user_argee'));
    }
    //教学视频
    public function video()
    {
        return return_json(get_config('video',1));
    }

//提币记录
    public function cashList()
    {
        $cash = new QuantCashService();
        $return = $cash->getUser($this->user_id);
        return $return;
    }

//提币记录
    public function moveList()
    {
        $cash = new QuantMoveService();
        $return = $cash->getUser($this->user_id);
        return return_json($return);
    }

//提币记录
    public function transferList()
    {
        $cash = new QuantTransferService();
        $return = $cash->getUser($this->user_id);
        return return_json($return);
    }


    //获取用户是否设置过api

    public function getApi()
    {
        $cash = new PlatformMemberService();
        $return = $cash->getAPI($this->user_id);
        return return_json($return);
    }

//app升级

    //获取用户是否设置过api

    public function appup()
    {
        $cash = new AppupService();
        $return = $cash->appup();
        return return_json($return);
    }




    /**
     * 修改昵称
     *
     */
    public function set_mobile()
    {
        $result = $this->service->set_mobile($this->user_id);
        return return_json($result);
    }

    /**
     * 修改昵称
     *
     */
    public function platform()
    {
        $result = $this->service->platform($this->user_id);
        return return_json($result);
    }

    /**
     * 获取数据列表
     * @return mixed
     * @since 2020/11/11
     * @author 牧羊人
     */
    public function index()
    {
        $result = $this->service->getInfo($this->user_id);
        return return_json($result);
    }



    //修改昵称
    public function username()
    {
        $result = $this->service->username($this->user_id);

        return return_json($result);
    }

    public function list()
    {
        $result = $this->service->list($this->user_id);
        return return_json($result);
    }


    public function code_login()
    {
        return return_json($this->service->code_login());
    }


    /**
     * 提款 发送验证码
     */
    public function cash_sms()
    {
        $result = $this->service->cash_sms($this->user_id);
        return return_json($result);
    }


    public function get_cash_config()
    {
        $service = new QuantConfigService();
        return return_json($service->get_cash_config());
    }

    public function get_point_config()
    {
//        echo sprintf("%.2f",123.123465);die;
        $service = new QuantConfigService();
        return return_json($service->get_point_config());
    }


    //获取账号金额
    public function balance()
    {
        return return_json($this->service->balance($this->user_id));
    }

    //修改私钥
    public function auth()
    {
        return return_json($this->service->auth($this->user_id));
    }


    // 排行榜
    public function ranking()
    {
        return return_json($this->service->ranking());
    }




    public function asset_list()
    {

        $wallet= new QuantWalletService();
//
        return return_json($wallet->getUser());

    }


    public function order()
    {
        $member = new \app\common\model\Member();
        $info = $member->getInfo($this->user_id);

        $result = [
            "today_profit" => $info['qb_profit_today']??0,
            "all_profit" => $info['qb_profit_all']??0,

        ];
        return return_json($result);

    }

    public function order_all()
    {
//        TradeProfitService
        $tain = new TradeProfit();



        $list  =  Db::query("select SUM(profit_usdt) as profit , date_format(from_unixtime(create_time), '%Y-%m-%d') today from yrnet_trade_profit where user_id={$this->user_id} AND profit_usdt>0 group by today");

//        $results = $tain->getList([['user_id','=',$this->user_id]], [], 0, 0);
        if($list){
            foreach ($list as  $key => &$row){
///
                $row['type_name']=($row['profit']>=0)?'盈利':'亏损';
                $row['profit']=get_round($row['profit'],6);
                $row['create_time']=$row['today'];
                $volume[$key]  = $row['today'];
            }
            array_multisort($volume, SORT_DESC, $list);
        }


        return return_json($list);

    }

    public function order_today()
    {
        $tain = new TradeProfit();
        $map=[];
        $map[] = ['create_time','between',[mktime(0,0,0,date('m'),date('d'),date('Y')),mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1]];
        $map[] = ['user_id','=',$this->user_id];
        $results = $tain->getList($map, [], 0, 0);

        if($results){
            foreach ($results as  $key => &$k){
                $open = $k["open_type"]==2?'多':'空';
                $k["order_sn"] =$k['order_id'];
                $k[ "symbol_title"] = $k['symbol']."永续合约 ".$open;
                $k["platform_id"] = isset($k['platform_id'])?$k['platform_id']:2;
                $k['create_time']=date('Y-m-d H:i:s',strtotime($k['create_time']));
                $k["profit"] = $k['profit_usdt'];
                $volume[$key]  = $k['create_time'];
            }
            array_multisort($volume, SORT_DESC, $results);
        }


        return return_json($results);
//        $results = array(
//            "msg" => '操作成功',
//            "code" => 0,
//            "count" => 0,
//            "data" => [
////                [
////                    "order_sn" => "123456",
////                    "symbol_title" => "BTC/USDT永续合约 空",
////                    "platform_id" => 2,
////                    "create_time" => date("Y-m-d H:i:s"),
////                    "profit" => "0.8"
////                ],
//            ]
//        );
//        return return_json($results);
    }
}