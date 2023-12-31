<?php 
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service;


use app\common\model\Config;

/**
 * 配置分组管理-服务类
 * @author 测试
 * @since: 2021/09/09
 * Class ConfigService
 * @package app\adminapi\service
 */
class ConfigService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Config();
    }

	/**
     * 获取数据列表
     * @return array
     * @since 2021/09/09
     * @author 测试
     */
    public function getList()
    {
        $param = $this->input;

        // 查询条件
        $map = [];

	
	    // 分组名称
        $name = isset($param['name']) ? trim($param['name']) : '';
        if ($name) {
            $map[] = ['name', 'like', "%{$name}%"];
        }
	
        return parent::getList($map); // TODO: Change the autogenerated stub
    }


                                
}
