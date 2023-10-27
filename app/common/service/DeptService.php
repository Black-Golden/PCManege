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

use app\common\model\Dept;

/**
 * 部门管理-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class DeptService
 * @package app\common\service
 */
class DeptService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * DeptService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Dept();
    }

    /**
     * 获取部门列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/11/20
     * @author 牧羊人
     */
    public function getList()
    {
        // 请求参数
        $param = request()->param();

        // 查询条件
        $map = [];

        // 部门名称
        $name = getter($param, "name");
        if ($name) {
            $map[] = ['name', 'like', "%{$name}%"];
        }
        $list = $this->model->getList($map, "sort asc");
        return message("操作成功", true, $list);
    }

    /**
     * 获取部门列表
     * @return array
     * @since 2021/5/26
     * @author 牧羊人
     */
    public function getDeptList()
    {
        $deptList = $this->model
            ->where("mark", "=", 1)
            ->select()
            ->toArray();
        return message("操作成功", true, $deptList);
    }

}
