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

use app\common\model\AdminUserRole;

/**
 * 用户角色关系-服务类
 * @author 牧羊人
 * @since 2020/11/14
 * Class UserRoleService
 * @package app\common\service
 */
class AdminUserRoleService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/14
     * UserRoleService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new AdminUserRole();
    }

    /**
     * 获取用户角色列表
     * @param $user_id 用户ID
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/11/14
     * @author 牧羊人
     */
    public function getUserRoleList($user_id)
    {
        $roleList = $this->model->alias("ur")
            ->field('r.*')
            ->join(DB_PREFIX . 'role r', 'ur.role_id=r.id')
            ->distinct(true)
            ->where('ur.user_id', '=', $user_id)
            ->where('r.status', '=', 1)
            ->where('r.mark', '=', 1)
            ->order('r.sort asc')
            ->select()->toArray();
        return $roleList;
    }

    /**
     * 删除用户角色关系数据
     * @param $user_id 用户ID
     * @since 2020/11/11
     * @author 牧羊人
     */
    public function deleteUserRole($user_id)
    {
        $this->model->where("user_id", '=', $user_id)->delete();
    }

    /**
     * 批量插入用户角色关系数据
     * @param $user_id 用户ID
     * @param $roleIds 角色ID集合
     * @author 牧羊人
     * @since 2020/11/11
     */
    public function insertUserRole($user_id, $roleIds)
    {
        if (!empty($roleIds)) {
            $list = [];
            foreach ($roleIds as $val) {
                $data = [
                    'user_id' => $user_id,
                    'role_id' => $val,
                ];
                $list[] = $data;
            }
            $this->model->insertAll($list);
        }
    }

}
