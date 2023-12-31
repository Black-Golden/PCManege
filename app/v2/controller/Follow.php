<?php namespace app\v2\controller;



use app\common\service\FollfwService;
use app\common\service\TradeProfitService;
use app\v2\controller\Backend;

/**
 * Quantmarketbuy前端管理-控制器
 * @author 测试
 * @since: 2022/01/23
 * Class Quantmarketbuy
 * @package app\v1\controller
 */
class Follow extends Backend
{
    /**
     * 初始化方法
     * @author 测试
     * @since: 2022/01/23
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new FollfwService();
    }


    //点击跟单
    public function follow(){
        $list = $this->service->follow($this->user_id);
        return return_json($list);
    }

    //点击跟单
    public function follow1(){
        $list = $this->service->follow($this->user_id);
        return return_json($list);
    }

}
