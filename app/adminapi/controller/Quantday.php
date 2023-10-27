<?php namespace app\adminapi\controller;


use app\common\service\QuantDayService;
use app\adminapi\controller\Backend;

/**
 * Quantday管理-控制器
 * @author 测试
 * @since: 2022/01/23
 * Class Quantday
 * @package app\adminapi\controller
 */
class Quantday extends Backend
{
    /**
     * 初始化方法
     * @author 测试
     * @since: 2022/01/23
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new QuantDayService();
    }

                
	/**
	 * 设置是否汇总
	 * @return mixed
	 * @since 2022/01/23
	 * @author 测试
	 */
	public function setIsDeal()
	{
		if (IS_POST) {
			$result = $this->service->setIsDeal();
			return $result;
		}
	}
    	            
}
