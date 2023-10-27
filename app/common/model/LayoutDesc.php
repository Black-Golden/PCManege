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
 * 布局描述-模型
 * @author 牧羊人
 * @since 2020/11/15
 * Class LayoutDesc
 * @package app\common\model
 */
class LayoutDesc extends BaseModel
{
    // 设置数据表名
    protected $name = "layout_desc";

    /**
     * 获取记录信息
     * @param int $id 布局描述ID
     * @return 数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/15
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id); // TODO: Change the autogenerated stub
        if ($info) {
            // 站点名称
            if ($info['item_id']) {
                $itemModel = new Item();
                $itemInfo = $itemModel->getInfo($info['item_id']);
                if ($itemInfo) {
                    $info['item_name'] = $itemInfo['name'];
                }
            }
        }
        return $info;
    }

}
