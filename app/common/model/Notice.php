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
 * 通知公告-模型
 * @author 牧羊人
 * @since 2020/11/15
 * Class Notice
 * @package app\common\model
 */
class Notice extends BaseModel
{
    // 设置数据表名
    protected $name = "notice";

    /**
     * 获取通知信息
     * @param int $id 通知ID
     * @return 数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2021/6/5
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id); // TODO: Change the autogenerated stub
        if ($info) {
            if ($info['content']) {
                while (strstr($info['content'], "[IMG_URL]")) {
                    $info['content'] = str_replace("[IMG_URL]", IMG_URL, $info['content']);
                }
            }
        }
        return $info;
    }

}
