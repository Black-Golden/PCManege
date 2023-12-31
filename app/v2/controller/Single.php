<?php namespace app\v2\controller;


use app\common\service\CustomPolicyService;
use app\common\service\SingleConfigService;
use app\common\service\SingleSetService;
use app\common\service\SymbolService;
use app\v2\controller\Backend;

/**
 * Quantsymbol前端管理-控制器
 * @author 测试
 * @since: 2022/01/23
 * Class Quantsymbol
 * @package app\v1\controller
 */
class Single extends Backend
{
    /**
     * 初始化方法
     * @author 测试
     * @since: 2022/01/23
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        //$this->service = new SymbolService();
    }

    public function config()
    {
        $service = new SingleConfigService();
        $data = $service->get_config($this->user_id);
        return return_json($data);
    }

    //平仓全部卖出
    public function sell_all()
    {
        $service = new SingleSetService();
        $data = $service->sell_all($this->user_id);
        return return_json($data);
    }



    //一键补仓
    public function cover()
    {
        $service = new SingleSetService();
        $data = $service->cover($this->user_id);
        return return_json($data);
    }

    //策略修改
    public function setup_edit(){
        $service = new SingleConfigService();
        $data = $service->update_set($this->user_id);
        return return_json($data);
    }

    //策略修改
    public function set_setup(){
        $service = new SingleConfigService();
        $data = $service->set_setup($this->user_id);
        return return_json($data);
    }

    public function set()
    {
        $service = new SingleConfigService();
        $data = $service->config($this->user_id);
        return return_json($data);
    }

    public function init()
    {
        $service = new SingleConfigService();
        $data = $service->init($this->user_id);
        return return_json($data);
    }
    //自定义策略
    public function SetPolicy()
    {
        $service = new CustomPolicyService();
        $data = $service->edit($this->user_id);
        return return_json($data);
    }

}
