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

namespace app\adminapi\controller;

use app\common\service\AdService;

/**
 * 广告管理-控制器
 * @author 牧羊人
 * @since 2020/11/15
 * Class Ad
 * @package app\adminapi\controller
 */
class Ad extends Backend
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     */
    protected function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->service = new AdService();
    }
}
