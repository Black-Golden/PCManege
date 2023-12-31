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


use app\common\model\Ad;

/**
 * 广告管理-服务类
 * @author 测试
 * @since: 2022/11/17
 * Class AdService
 * @package app\adminapi\service
 */
class AdService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Ad();
    }

	/**
     * 获取数据列表
     * @return array
     * @since 2022/11/17
     * @author 测试
     */
    public function getList()
    {
        $param = $this->input;

        // 查询条件
        $map = [];


	    // 广告标题
        $title = isset($param['title']) ? trim($param['title']) : '';
        if ($title) {
            $map[] = ['title', 'like', "%{$title}%"];
        }

	    // 广告格式
        $type = isset($param['type']) ? (int)$param['type'] : 0;
        if ($type) {
            $map[] = ['type', '=', $type];
        }

	    // 状态
        $status = isset($param['status']) ? (int)$param['status'] : 0;
        if ($status) {
            $map[] = ['status', '=', $status];
        }

        return parent::getList([], [], 1, 1); // TODO: Change the autogenerated stub
    }

	/**
     * 添加或编辑
     * @return array
     * @since 2022/11/17
     * @author 测试
     */
    public function edit()
    {
        // 参数
        $data = $this->input;

		// 广告图片处理
        $cover = trim($data['cover']);
//        if (strpos($cover, "temp")) {
//            $data['cover'] = save_image($cover, 'ad');
//        } else {
            $data['cover'] = str_replace(IMG_URL, "", $data['cover']);
//        }

        return parent::edit($data); // TODO: Change the autogenerated stub
    }


}
