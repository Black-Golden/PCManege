<?php

namespace app\v2\controller;

use app\common\model\Appup;
use app\common\model\Document;
use app\common\model\SingleLoopBuy;
use app\common\model\SingleLoopSell;
use app\common\model\SingleLoopSetup;
use app\common\model\TradeLog;
use app\common\service\AppupService;
use app\common\service\DocumentService;
use app\common\service\MemberService;
use app\common\service\QuantHedgeItemService;
use app\common\service\QuantMarketItemService;
use app\common\service\QuantSetupService;
use app\common\service\SingleLoopBuyService;
use app\common\service\SingleLoopSetupService;
use app\common\service\SymbolService;
use app\common\service\TradeLogService;
use app\extend\Huobi;
use ccxt\okex;
use app\common\service\NoticeService;
use think\facade\Db;

class Market extends Backend
{

    //平仓数据
    public function record()
    {
      $serve = new SingleLoopSetupService();
        return return_json($serve->getLast());
    }


    //公告 只显示最新一条
    public function notice_top()
    {
        $NoticeService = new NoticeService();
        return return_json($NoticeService->top());
    }

    //公告
    public function notice()
    {
        $NoticeService = new NoticeService();
        $results = $NoticeService->getList([], [], 1, 1);
        return return_json($results);
    }

    //公告
    public function notice_info()
    {
        $NoticeService = new NoticeService();
        return return_json($NoticeService->info());
    }
    //提币配置
    public function get_cash_config()
    {
        $result=get_config('cash_fee,min_fee,move_fee');
        return return_json($result);
    }



    //策略
    public function bonds()
    {
        $single = new SingleLoopSetupService();
        $list = $single->round();
        return return_json($list);
    }

//交易日志
    public function trade_log()
    {
        $trade_log = new TradeLogService();

        $list = $trade_log->getList();
        return return_json($list);
    }
    //日志 获取代币列表
    public function symbolList()
    {
        $SingleLoopSetup = new SingleLoopSetup();

        $list =   $SingleLoopSetup->where('user_id',$this->user_id)->group('symbol')->column('symbol');
        return return_json($list);
    }

    //交易记录
    public function transaction()
    {

        $SingleLoopSell = new SingleLoopBuyService();
        $list = $SingleLoopSell->transaction($this->user_id);
        return return_json($list);

    }



    //官方文档/使用教程
    public function document()
    {
        $DocumentService = new DocumentService();
        $list = $DocumentService->getList();
        return return_json($list);
    }

    //官方文档/使用教程
    public function documentinfo()
    {
        $DocumentService = new DocumentService();
        $list = $DocumentService->getInfo();
        return return_json($list);
    }


}
















