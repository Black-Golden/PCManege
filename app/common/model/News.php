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

use app\common\model\AdminUser;
use app\common\model\DictData;

/**
 * 消息记录-模型
 * @author 测试
 * @since: 2021/12/24
 * Class News
 * @package app\adminapi\model
 */
class News extends BaseModel
{
    // 设置数据表名
    public $name = "news";

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return \app\common\model\数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 测试
     * @since: 2021/12/24
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id); // TODO: Change the autogenerated stub
        if ($info) {
            $admin = new AdminUser();
            $info['user_info'] = $admin->getInfo($info['user_id']);
            $info['user_info'] =$info['user_info']['realname'];
            $info['create_info'] = $admin->getInfo($info['create_user']);
            $info['create_info'] =$info['create_info']['realname'];
//            //获取字典值
            $dict = new DictData();
            $info['classificationa_name'] = $dict->getInfo($info['classification']);
            $info['classificationa_name'] =$info['classificationa_name']['name'];//
            if($info['enclosure']){
                $info['enclosure'] =explode(',',$info['enclosure']);//把字符串以，分开合成一个数组;
            }
        }
        return $info;
    }


}
