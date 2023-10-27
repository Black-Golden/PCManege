<?php namespace app\adminapi\controller;


use app\common\service\TeamService;
use app\adminapi\controller\Backend;

/**
 * Work管理-控制器
 * @author 测试
 * @since: 2022/03/18
 * Class Work
 * @package app\adminapi\controller
 */
class Work extends Backend
{
    /**
     * 初始化方法
     * @author 测试
     * @since: 2022/03/18
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new TeamService();
    }


}