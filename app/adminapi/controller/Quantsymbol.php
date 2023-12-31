<?php namespace app\adminapi\controller;


use app\common\service\SymbolService;
use app\adminapi\controller\Backend;

/**
 * Quantsymbol管理-控制器
 * @author 测试
 * @since: 2022/01/23
 * Class Quantsymbol
 * @package app\adminapi\controller
 */
class Quantsymbol extends Backend
{
    /**
     * 初始化方法
     * @author 测试
     * @since: 2022/01/23
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new SymbolService();
    }


	/**
	 * 设置是否在线
	 * @return mixed
	 * @since 2022/01/23
	 * @author 测试
	 */
	public function setIsOnline()
	{
		if (IS_POST) {
			$result = $this->service->setIsOnline();
			return $result;
		}
	}

}
