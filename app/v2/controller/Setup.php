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
use app\common\service\NoticeService;
use app\common\service\SingleLoopSetupService;
use app\common\service\SingleSetService;

class Setup extends Backend
{

    //删除策略
    public function delete()
    {
        $setup = new SingleLoopSetupService();
        $results = $setup->del($this->user_id);
        return return_json($results);
    }

    //浮亏汇总
    public function loss_poll()
    {

//        $results = [
//            "data" => [
////                [
////                    "title" => "BNB/USDT永续多 全仓 50倍",
////                    "rounds" => "3",
////                    "loss" => "0.8"
////                ],
////                [
////                    "title" => "BNB/USDT永续多 全仓 50倍",
////                    "rounds" => "3",
////                    "loss" => "0.7"
////                ],
////                [
////                    "title" => "BNB/USDT永续多 全仓 50倍",
////                    "rounds" => "3",
////                    "loss" => "0.2"
////                ]
//            ],
//            "param" => [
//                "count" => 4,
//                "loss" => 0.9,
//            ]
//        ];
        $setup = new SingleLoopSetupService();
        $results = $setup->lossPoll($this->user_id);

        return json($results);
    }

    public function setupInfo()
    {
        $SingleLoopSetup = new SingleLoopSetupService();
        $data = $SingleLoopSetup->setupInfo($this->user_id);
        return return_json($data);
    }

    /**
     * 设置是否循环
     * @return mixed
     * @since 2022/02/16
     * @author 测试
     */
    public function floating()
    {
        $SingleLoopSetup = new SingleLoopSetupService();
        $data = $SingleLoopSetup->floating($this->user_id);
        return return_json($data);
    }

//策略详情
    public function getInfo()
    {
        $SingleLoopSetup = new SingleLoopSetupService();
        $data = $SingleLoopSetup->getInfo();
        return return_json($data);
    }

//策略详情
    public function getSymbol()
    {
        $SingleLoopSetup = new SingleLoopSetupService();
        $data = $SingleLoopSetup->getSymbol($this->user_id);
        return return_json($data);
    }

    /**
     * 设置暂停补仓
     * @return mixed
     * @since 2022/02/16
     * @author 测试
     */
    public function coverInfo()
    {
        $SingleLoopSetup = new SingleSetService();
        $data = $SingleLoopSetup->coverInfo($this->user_id);
        return return_json($data);
    }

    /**
     * @return \think\response\Json
     * 一建平仓
     */
    public function sell_all()
    {
        $SingleLoopSetup = new SingleSetService();
        $data = $SingleLoopSetup->sell_all($this->user_id);
        return return_json($data);
    }

    /**
     * @return \think\response\Json
     * 暂停  isrun=0
     */
    public function stop()
    {
        $SingleLoopSetup = new SingleSetService();
        $data = $SingleLoopSetup->stop($this->user_id);
        return return_json($data);
    }
}
















