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

use app\common\model\ActionLog;

/**
 * 登录日志-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class LoginLogService
 * @package app\common\service
 */
class LoginLogService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * LoginLogService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new ActionLog();
    }

    /**
     * 获取登录日志列表
     * @return array
     * @since 2020/11/15
     * @author 牧羊人
     */
    public function getList()
    {
        $param = request()->param();
        // 查询条件
        $map = [];

        // 只取登录日志
        $map[] = ['type', "<=", 2];

        // 用户账号
        $username = getter($param, "username");
        if ($username) {
            $map[] = ["username", "=", $username];
        }
        return parent::getList($map); // TODO: Change the autogenerated stub
    }
}
