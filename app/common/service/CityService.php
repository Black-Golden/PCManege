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

use app\common\model\City;

/**
 * 城市管理-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class CityService
 * @package app\common\service
 */
class CityService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * CityService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new City();
    }

    /**
     * 获取城市列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/11/15
     * @author 牧羊人
     */
    public function getList()
    {
        // 请求参数
        $param = request()->param();

        // 查询条件
        $map = [];
        // 上级ID
        $pid = intval(getter($param, 'pid', 0));
        if (!$pid) {
            $map[] = ['pid', '=', 0];
        } else {
            $map[] = ['pid', '=', $pid];
        }
        // 城市名称
        $name = getter($param, "name");
        if ($name) {
            $map[] = ['name', 'like', "%{$name}%"];
        }
        $list = $this->model->getList($map, "sort asc");
        if (!empty($list)) {
            foreach ($list as &$val) {
                if ($val['level'] <= 2) {
                    $val['hasChildren'] = true;
                }
            }
        }
        return message("操作成功", true, $list);
    }

}
