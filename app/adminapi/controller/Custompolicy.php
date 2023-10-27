<?php namespace app\adminapi\controller;


use app\common\service\CustomPolicyService;
use app\adminapi\controller\Backend;

/**
 * Custompolicy管理-控制器
 * @author 测试
 * @since: 2022/11/16
 * Class Custompolicy
 * @package app\adminapi\controller
 */
class Custompolicy extends Backend
{
    /**
     * 初始化方法
     * @author 测试
     * @since: 2022/11/16
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new CustomPolicyService();
    }

                                
}
