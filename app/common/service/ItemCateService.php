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

use app\common\model\ItemCate;

/**
 * 栏目管理-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class ItemCateService
 * @package app\common\service
 */
class ItemCateService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * ItemCateService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new ItemCate();
    }

    /**
     * 获取栏目列表
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
        // 上级ID
        $pid = intval(getter($param, 'pid', 0));
        if (!$pid) {
            $map[] = ['pid', '=', 0];
        } else {
            $map[] = ['pid', '=', $pid];
        }
        $list = $this->model->getList($map, "sort asc");
        if (!empty($list)) {
            foreach ($list as &$val) {
                if ($val['pid'] == 0) {
                    $val['hasChildren'] = true;
                }
            }
        }
        return message("操作成功", true, $list);
    }

    /**
     * 添加或编辑
     * @return mixed
     * @since 2020/11/20
     * @author 牧羊人
     */
    public function edit()
    {
        // 请求参数
        $data = request()->param();
        // 封面处理
        $cover = getter($data, 'cover');
        if (strpos($cover, "temp")) {
            $data['cover'] = save_image($cover, 'itemcate');
        } else {
            $data['cover'] = str_replace(IMG_URL, "", $data['cover']);
        }
        return parent::edit($data); // TODO: Change the autogenerated stub
    }

}
