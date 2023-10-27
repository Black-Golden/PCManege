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
 * 交易卖出-模型
 * @author 测试
 * @since: 2022/11/16
 * Class SingleLoopSell
 * @package app\adminapi\model
 */
class SingleLoopSell extends BaseModel
{
    // 设置数据表名
    public $name = "single_loop_sell";

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return \app\common\model\数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 测试
     * @since: 2022/11/16
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id); // TODO: Change the autogenerated stub
        if ($info) {
            $user = new Member();
            $member = $user->where('id',$info['user_id'])->find();
            if($member){
                $info['user_name']=$member['username'];
            }else{
                $info['user_name']='';
            }
        }
        return $info;
    }


    public function getBuyQbUsdtAttr($value)
    {
        return (float)$value;
    }

    public function getQbUsdtAttr($value)
    {
        return (float)$value;
    }
    public function getBuyQbTokenAttr($value)
    {
        return (float)$value;
    }

    public function getQbTokenAttr($value)
    {
        return (float)$value;
    }

    public function getBuyPriceAttr($value)
    {
        return (float)$value;
    }

    public function getPriceAttr($value)
    {
        return (float)$value;
    }


}