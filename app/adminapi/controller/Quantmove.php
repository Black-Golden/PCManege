<?php namespace app\adminapi\controller;


use app\common\service\QuantMoveService;
use app\adminapi\controller\Backend;

/**
 * Quantmove管理-控制器
 * @author 测试
 * @since: 2022/11/17
 * Class Quantmove
 * @package app\adminapi\controller
 */
class Quantmove extends Backend
{
    /**
     * 初始化方法
     * @author 测试
     * @since: 2022/11/17
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new QuantMoveService();
    }

                                                
}
