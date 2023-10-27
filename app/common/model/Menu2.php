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

namespace app\common\model;

/**
 * 菜单-模型
 * @author 牧羊人
 * @since 2020/11/14
 * Class Menu
 * @package app\common\model
 */
class Menu2 extends BaseModel
{
    // 设置数据表名
    protected $name = "menu2";

    /**
     * 获取子级菜单列表
     * @param $pid 上级ID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/11/14
     * @author 牧羊人
     */
    public function getChilds($pid)
    {
        $map = [
            ['type', '=', 0],
            ['pid', '=', $pid],
            ['status', '=', 1],
            ['mark', '=', 1],
        ];
        $list = $this->where($map)->order("sort asc")->select()->toArray();
        if (!empty($list)) {
            foreach ($list as &$val) {
                $val['children'] = $this->getChilds($val['id']);
            }
        }
        return $list;
    }

}
