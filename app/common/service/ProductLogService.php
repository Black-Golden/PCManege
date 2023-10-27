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


use app\common\model\ProductLog;

/**
 * 累计收益管理-服务类
 * @author 测试
 * @since: 2023/09/27
 * Class ProductLogService
 * @package app\adminapi\service
 */
class ProductLogService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new ProductLog();
    }

	/**
     * 获取数据列表
     * @return array
     * @since 2023/09/27
     * @author 测试
     */
    public function getList()
    {
        $param = $this->input;

        // 查询条件
        $map = [];
        $order = '';
        $order_id = isset($param['order_id']) ? (int)$param['order_id'] : 0;
        if ($order_id) {
            $map[] = ['order_id', '=', $order_id];
        }
        $orders = isset($param['order']) ? (int)$param['order'] : 0;
        if ($orders) {
            $order = $orders;
        }

//var_dump($map);
        return parent::getList($map,$order); // TODO: Change the autogenerated stub
    }


                                            
}