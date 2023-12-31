<?php namespace app\v1\controller;


use app\common\service\PolicyService;
use app\v1\controller\Backend;

/**
* Policy前端管理-控制器
* @author 测试
* @since: 2022/03/21
* Class Policy
* @package app\v1\controller
*/
class Policy extends Backend
{
/**
* 初始化方法
* @author 测试
* @since: 2022/03/21
*/
public function initialize()
{
parent::initialize(); // TODO: Change the autogenerated stub
$this->service = new PolicyService();
}

    public function list(){

       return return_json($this->service->list());
    }

    public function chain(){

        return return_json(get_config('bian_url,ouyi_url'));
    }
}
