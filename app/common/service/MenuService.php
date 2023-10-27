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

/**
 * 菜单管理-服务类
 * @author 牧羊人
 * @since 2020/11/14
 * Class MenuService
 * @package app\common\service
 */
class MenuService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/14
     * MenuService constructor.
     */
    public function __construct()
    {
        $this->model = new Menu();
    }

    /**
     * 获取数据列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/11/15
     * @author 牧羊人
     */
    public function getList()
    {
        // 请求参数
        $param = request()->param();

        // 查询条件
        $map = [];
        // 菜单标题
        $title = getter($param, "title");
        if ($title) {
            $map[] = ['title', 'like', "%{$title}%"];
        }
        $list = $this->model->getList($map, "sort asc");
        return message("操作成功", true, $list);
    }

    /**
     * 获取菜单详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2021/3/24
     */
    public function info()
    {
        // 记录ID
        $id = request()->param("id", 0);
        $info = [];
        if ($id) {
            $info = $this->model->getInfo($id);
        }
        // 获取权限节点
        $checkedList = array();
        if ($info['type'] == 0) {
            $permissionList = $this->model
                ->field("sort")
                ->where("pid", "=", $info['id'])
                ->where("type", "=", 1)
                ->where("mark", "=", 1)
                ->select()
                ->toArray();
            if (is_array($permissionList)) {
                $checkedList = array_key_value($permissionList, "sort");
            }
            $info['checkedList'] = $checkedList;
        }
        return message("操作成功", true, $info);
    }

    /**
     * 添加或编辑
     * @return array
     * @since 2021/3/24
     * @author 牧羊人
     */
    public function edit()
    {
        // 参数
        $param = request()->param();
        // 权限节点
        $checkedList = isset($param['checkedList']) ? $param['checkedList'] : array();
        unset($param['checkedList']);
        // 保存数据
        $result = $this->model->edit($param);
        if (!$result) {
            return message("操作失败", false);
        }

        // 设置权限节点
        $isFunc = false;
        if ($param['type'] == 0 && !empty($param['path']) && !empty($checkedList)) {
            $item = explode("/", $param['path']);
            // 模块名称
            $moduleName = $item[count($item) - 1];
            // 模块标题
            $moduleTitle = str_replace("管理", "", $param['title']);
            // 删除以存在的节点
            $funcIds = $this->model
                ->where("pid", $result)
                ->where("type", 1)
                ->column("id");
            $this->model->deleteDAll($funcIds, true);
            $isFunc = true;
            // 遍历权限节点
            foreach ($checkedList as $val) {
                $data = [];
                $data['pid'] = $result;
                $data['type'] = 1;
                $data['status'] = 1;
                $data['sort'] = intval($val);
                $data['target'] = $param['target'];

                // 判断当前节点是否已存在
                $permissionInfo = $this->model
                    ->where("pid", "=", $result)
                    ->where("type", "=", 1)
                    ->where("sort", "=", $val)
                    ->where("mark", "=", 1)
                    ->find();
                if ($permissionInfo) {
                    $data['id'] = $permissionInfo ? $permissionInfo['id'] : 0;
                }
                if ($val == 1) {
                    // 查询
                    $data['title'] = "查询" . $moduleTitle;
                    $data['permission'] = "sys:{$moduleName}:index";
                } else if ($val == 5) {
                    // 添加
                    $data['title'] = "添加" . $moduleTitle;
                    $data['permission'] = "sys:{$moduleName}:add";
                } else if ($val == 10) {
                    // 修改
                    $data['title'] = "修改" . $moduleTitle;
                    $data['permission'] = "sys:{$moduleName}:edit";
                } else if ($val == 15) {
                    // 删除
                    $data['title'] = "删除" . $moduleTitle;
                    $data['permission'] = "sys:{$moduleName}:delete";
                } else if ($val == 20) {
                    // 状态
                    $data['title'] = "设置状态";
                    $data['permission'] = "sys:{$moduleName}:status";
                } else if ($val == 25) {
                    // 批量删除
                    $data['title'] = "批量删除";
                    $data['permission'] = "sys:{$moduleName}:dall";
                } else if ($val == 30) {
                    // 全部展开
                    $data['title'] = "全部展开";
                    $data['permission'] = "sys:{$moduleName}:expand";
                } else if ($val == 35) {
                    // 全部折叠
                    $data['title'] = "全部折叠";
                    $data['permission'] = "sys:{$moduleName}:collapse";
                } else if ($val == 40) {
                    // 添加子级
                    $data['title'] = "添加子级";
                    $data['permission'] = "sys:{$moduleName}:addz";
                } else if ($val == 45) {
                    // 导出数据
                    $data['title'] = "导出数据";
                    $data['permission'] = "sys:{$moduleName}:export";
                } else if ($val == 50) {
                    // 导入数据
                    $data['title'] = "导入数据";
                    $data['permission'] = "sys:{$moduleName}:import";
                } else if ($val == 55) {
                    // 分配权限
                    $data['title'] = "分配权限";
                    $data['permission'] = "sys:{$moduleName}:permission";
                }
                if (empty($data['title'])) {
                    continue;
                }
                $menuModel = new Menu();
                $menuModel->edit($data);
            }
        }
        return message("操作成功", true, $isFunc);
    }

    /**
     * 获取菜单权限列表
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/11/20
     * @author 牧羊人
     */
    public function getPermissionList($user_id)
    {
        $list = [];
        if ($user_id == 1) {
            // 管理员拥有全部权限
            $list = $this->model->getChilds(0);
        } else {
            // 其他角色
            $list = $this->getPermissionMenu($user_id, 0);
        }
        return message("操作成功", true, $list);
    }

    /**
     * 获取权限菜单
     * @param $user_id 用户ID
     * @param $pid 上级ID
     * @return mixed
     * @since 2020/11/20
     * @author 牧羊人
     */
    public function getPermissionMenu($user_id, $pid)
    {
        $menuModel = new Menu();
        $menuList = $menuModel->alias('m')
            ->join(DB_PREFIX . 'role_menu rm', 'rm.menu_id=m.id')
            ->join(DB_PREFIX . 'admin_user_role ur', 'ur.role_id=rm.role_id')
            ->distinct(true)
            ->where('ur.user_id', '=', $user_id)
            ->where('m.type', '=', 0)
            ->where('m.pid', '=', $pid)
            ->where('m.status', '=', 1)
            ->where('m.mark', '=', 1)
            ->order('m.pid ASC,m.sort ASC')
            ->field('m.*')
            ->select()->toArray();
        if (!empty($menuList)) {
            foreach ($menuList as &$val) {
                $childList = $this->getPermissionMenu($user_id, $val['id']);
                if (is_array($childList) && !empty($childList)) {
                    $val['children'] = $childList;
                }
            }
        }
        return $menuList;
    }

    /**
     *
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2021/3/23
     * @author 牧羊人
     */
    public function getPermissionsList($user_id)
    {
        $list = [];
        if ($user_id == 1) {
            // 管理员拥有全部权限
            $permissionList = $this->model
                ->where("type", "=", 1)
                ->where("mark", "=", 1)
                ->distinct(true)
                ->field("permission")
                ->select()
                ->toArray();
            $list = empty($permissionList) ? array() : array_key_value($permissionList, 'permission');
        } else {
            // 其他角色
            $menuModel = new Menu();
            $permissionList = $menuModel->alias('m')
                ->join(DB_PREFIX . 'role_menu rm', 'rm.menu_id=m.id')
                ->join(DB_PREFIX . 'admin_user_role ur', 'ur.role_id=rm.role_id')
                ->distinct(true)
                ->where('ur.user_id', '=', $user_id)
                ->where('m.type', '=', 1)
                ->where('m.status', '=', 1)
                ->where('m.mark', '=', 1)
                ->field('m.permission')
                ->select()
                ->toArray();
            $list = empty($permissionList) ? array() : array_key_value($permissionList, 'permission');
        }
        return $list;
    }

}
