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

use app\common\model\MemberLevel;

/**
 * 会员等级-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class MemberLevelService
 * @package app\common\service
 */
class MemberLevelService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * MemberLevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new MemberLevel();
    }

    /**
     * 获取会员等级列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/15
     */
    public function getMemberLevelList()
    {
        $list = $this->model->where("mark", "=", 1)->select()->toArray();
        return message("操作成功", true, $list);
    }

}
