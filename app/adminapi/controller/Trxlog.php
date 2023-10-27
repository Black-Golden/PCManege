<?php namespace app\adminapi\controller;


use app\common\service\TrxLogService;
use app\adminapi\controller\Backend;

/**
 * Tradelog管理-控制器
 * @author 测试
 * @since: 2022/11/16
 * Class Tradelog
 * @package app\adminapi\controller
 */
class Trxlog extends Backend
{
    /**
     * 初始化方法
     * @author 测试
     * @since: 2022/11/16
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new TrxLogService();
    }


}
