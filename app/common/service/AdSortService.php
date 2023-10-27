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

use app\common\model\AdSort;

/**
 * 广告位-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class AdSortService
 * @package app\common\service
 */
class AdSortService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * AdSortService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new AdSort();
    }

    /**
     * 获取广告位列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/20
     */
    public function getAdSortList()
    {
        $list = $this->model->where("mark", '=', 1)->select()->toArray();
        return message("操作成功", true, $list);
    }

}
