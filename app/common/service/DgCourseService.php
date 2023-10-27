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


use app\common\model\DgCourse;

/**
 * 操作指南管理-服务类
 * @author 测试
 * @since: 2022/11/16
 * Class DgCourseService
 * @package app\adminapi\service
 */
class DgCourseService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new DgCourse();
    }

	/**
     * 获取数据列表
     * @return array
     * @since 2022/11/16
     * @author 测试
     */
    public function getList()
    {
        $param = $this->input;

        // 查询条件
        $map = [];

	
	    // 标题
        $title = isset($param['title']) ? trim($param['title']) : '';
        if ($title) {
            $map[] = ['title', 'like', "%{$title}%"];
        }
	
        return parent::getList($map); // TODO: Change the autogenerated stub
    }


                        
}