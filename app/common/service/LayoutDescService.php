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

use app\common\model\LayoutDesc;

/**
 * 布局描述-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class LayoutDescService
 * @package app\common\service
 */
class LayoutDescService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * LayoutDescService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new LayoutDesc();
    }
}
