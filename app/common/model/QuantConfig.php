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
 * 后台配置-模型
 * @author 测试
 * @since: 2022/01/23
 * Class QuantConfig
 * @package app\adminapi\model
 */
class QuantConfig extends BaseModel
{
    // 设置数据表名
    public $name = "quant_config";

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return \app\common\model\数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 测试
     * @since: 2022/01/23
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id); // TODO: Change the autogenerated stub

        if ($info) {
            if ($info['type'] == 5){
                $info['value'] = get_image_url($info['value']);
            }elseif($info['type'] == 4){
                $info['value'] = get_image_url($info['value']);
            }
        }
        return $info;
    }


}
