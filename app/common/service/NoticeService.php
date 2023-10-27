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

use app\common\model\Notice;

/**
 * 通知公告-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class NoticeService
 * @package app\common\service
 */
class NoticeService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * NoticeService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Notice();
    }



    public function edit()
    {
        // 获取参数
        $data = request()->param();
        //内容处理
        save_image_content($data['content'], $data['title'], "notice");
        return parent::edit($data); // TODO: Change the autogenerated stub
    }

    /**
     * 获取最新公告
     */
    public function top()
    {
        $message = $this->model->order('id', 'desc')->where('mark', 1)->find();
        return $message;
    }

    /**
     * 获取最新公告
     */
    public function info()
    {
        $param = $this->input;

        $message = $this->model->getInfo($param['id']);

        return $message;
    }

}
