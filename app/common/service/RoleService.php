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

use app\common\model\Menu;
use app\common\model\Role;
use app\common\model\RoleMenu;

/**
 * 角色管理-服务类
 * @author 牧羊人
 * @since 2020/11/14
 * Class RoleService
 * @package app\common\service
 */
class RoleService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/14
     * RoleService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Role();
    }

    /**
     * 获取数据列表
     * @return array
     * @since 2020/11/20
     * @author 牧羊人
     */
    public function getList()
    {
        return parent::getList([], "id asc"); // TODO: Change the autogenerated stub
    }

    /**
     * 获取角色列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/14
     */
    public function getRoleList()
    {
        $list = $this->model->where([
            ['status', '=', 1],
            ['mark', '=', 1],
        ])->order("sort", "asc")
            ->select()
            ->toArray();
        return message("操作成功", true, $list);
    }

    /**
     * 获取权限列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/20
     */
    public function getPermissionList()
    {
        // 请求参数
        $param = request()->param();
        // 角色ID
        $roleId = intval(getter($param, "role_id", 0));
        // 获取全部菜单
        $menuModel = new Menu();
        $menuList = $menuModel->where([
            ['status', '=', 1],
            ['mark', '=', 1],
        ])->order("sort", "asc")->select()->toArray();
        if (!empty($menuList)) {
            $roleMenuModel = new RoleMenu();
            $roleMenuList = $roleMenuModel
                ->field("menu_id")
                ->where("role_id", '=', $roleId)
                ->select()
                ->toArray();
            $menuIdList = array_key_value($roleMenuList, "menu_id");
            foreach ($menuList as &$val) {
                if (in_array($val['id'], $menuIdList)) {
                    $val['checked'] = true;
                    $val['open'] = true;
                }
            }
        }
        return message("操作成功", true, $menuList);
    }

    /**
     * 保存权限
     * @return array
     * @since 2020/11/14
     * @author 牧羊人
     */
    public function savePermission()
    {
        // 请求参数
        $param = request()->param();
        // 角色ID
        $roleId = intval(getter($param, "role_id", 0));
        if (!$roleId) {
            return message("角色ID不能为空", false);
        }
        unset($param['role_id']);
        // 删除角色菜单关系数据
        $roleMenuModel = new RoleMenu();
        $roleMenuModel->where("role_id", $roleId)->delete();
        // 插入角色菜单关系数据
        if (is_array($param) && !empty($param)) {
            $list = [];
            foreach ($param as $val) {
                $data = [
                    'role_id' => $roleId,
                    'menu_id' => $val,
                ];
                $list[] = $data;
            }
        }
        $roleMenuModel->insertAll($list);
        return message();
    }

}
