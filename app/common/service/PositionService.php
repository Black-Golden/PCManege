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

use app\common\model\Position;

/**
 * 岗位管理-服务类
 * @author 牧羊人
 * @since 2020/11/14
 * Class PositionService
 * @package app\common\service
 */
class PositionService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/14
     * PositionService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Position();
    }

    /**
     * 获取岗位列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/14
     */
    public function getPositionList()
    {
        $list = $this->model->where([
            ['status', '=', 1],
            ['mark', '=', 1],
        ])->order("sort", "asc")
            ->select()
            ->toArray();
        return message("操作成功", true, $list);
    }

}
