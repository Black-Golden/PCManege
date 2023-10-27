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

use app\common\model\Item;

/**
 * 站点管理-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class ItemService
 * @package app\common\service
 */
class ItemService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * ItemService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Item();
    }

    /**
     * 获取站点列表
     * @return array
     * @since 2020/11/15
     * @author 牧羊人
     */
    public function getList()
    {
        $param = request()->param();

        // 查询条件
        $map = [];
        // 站点类型
        $type = getter($param, "type", 0);
        if ($type) {
            $map[] = ["type", '=', $type];
        }
        return parent::getList($map); // TODO: Change the autogenerated stub
    }

    /**
     * 添加或编辑
     * @return array
     * @since 2020/11/15
     * @author 牧羊人
     */
    public function edit()
    {
        // 请求参数
        $data = request()->param();

        // 图片处理
        $image = trim($data['image']);
        if (!$data['id'] && !$image) {
            return message('请上传站点图片', false);
        }
        if (strpos($image, "temp")) {
            $data['image'] = save_image($image, 'item');
        } else {
            $data['image'] = str_replace(IMG_URL, "", $data['image']);
        }
        return parent::edit($data); // TODO: Change the autogenerated stub
    }

    /**
     * 获取站点列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/15
     */
    public function getItemList()
    {
        $list = $this->model->where("status", "=", 1)
            ->where("mark", "=", 1)
            ->order("sort", "asc")
            ->select()
            ->toArray();
        return message("操作成功", true, $list);
    }

}