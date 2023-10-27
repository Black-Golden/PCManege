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

use app\common\model\Dict;

/**
 * 字典-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class DictTypeService
 * @package app\common\service
 */
class DictService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * DictTypeService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Dict();
    }
}
