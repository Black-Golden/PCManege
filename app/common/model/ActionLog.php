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

namespace app\common\model;

use think\facade\Db;

/**
 * 行为日志-模型
 * @author 牧羊人
 * @since 2020/11/14
 * Class ActionLog
 * @package app\common\model
 */
class ActionLog extends BaseModel
{
    // 设置数据表
    protected $table = null;
    // 自定义日志标题
    protected static $title = '';
    // 自定义日志内容
    protected static $content = '';

    /**
     * 构造函数
     * @param array $data
     * @since 2020/11/14
     * ActionLog constructor.
     * @author 牧羊人
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        // 设置表名
        $this->table = DB_PREFIX . 'action_log_' . date('Y') . '_' . date('m');
        // 初始化行为日志表
        $this->initTable();
    }

    /**
     * 初始化日志表
     * @return string|null
     * @throws \think\db\exception\BindParamException
     * @author 牧羊人
     * @since 2020/11/14
     */
    private function initTable()
    {
        $tbl = $this->table;
        if (!$this->tableExists($tbl)) {
            $sql = "CREATE TABLE `{$tbl}` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '唯一性标识',
                  `username` varchar(60) CHARACTER SET utf8mb4 NOT NULL COMMENT '操作人用户名',
                  `method` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '请求类型',
                  `module` varchar(30) NOT NULL COMMENT '模型',
                  `action` varchar(255) NOT NULL COMMENT '操作方法',
                  `url` varchar(200) CHARACTER SET utf8mb4 NOT NULL COMMENT '操作页面',
                  `param` text CHARACTER SET utf8mb4 NOT NULL COMMENT '请求参数(JSON格式)',
                  `title` varchar(100) NOT NULL COMMENT '日志标题',
                  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '操作类型：1登录系统 2注销系统 3操作日志',
                  `content` varchar(1000) NOT NULL DEFAULT '' COMMENT '内容',
                  `ip` varchar(18) CHARACTER SET utf8mb4 NOT NULL COMMENT 'IP地址',
                  `user_agent` varchar(360) CHARACTER SET utf8mb4 NOT NULL COMMENT 'User-Agent',
                  `create_user` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加人',
                  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
                  `mark` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '有效标识：1正常 0删除',
                  PRIMARY KEY (`id`) USING BTREE
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='系统行为日志表';";
            Db::query($sql);

        }
        return $tbl;
    }

    /**
     * 设置标题
     * @param $title 标题
     * @since 2020/11/14
     * @author 牧羊人
     */
    public static function setTitle($title)
    {
        self::$title = $title;
    }

    /**
     * 设置内容
     * @param $content 内容
     * @since 2020/11/14
     * @author 牧羊人
     */
    public static function setContent($content)
    {
        self::$content = $content;
    }

    /**
     * 创建日志
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/20
     */
    public static function record()
    {
        if (!self::$title) {
            // 操作控制器名
            $menuModel = new Menu();
            $info = $menuModel->getOne([
                ['path', '=', request()->url()],
            ]);
            if ($info) {
                if ($info['type'] == 1) {
                    $menuInfo = $menuModel->getInfo($info['pid']);
                    self::$title = $menuInfo['title'];
                } else {
                    self::$title = $info['title'];
                }
            }
        }
        // 日志数据
        $data = [
            'username' => '管理员',
            'module' => app('http')->getName(),
            'action' => request()->url(),
            'method' => request()->method(),
            'url' => request()->url(true), // 获取完成URL
            'param' => request()->param() ? json_encode(request()->param()) : '',
            'title' => self::$title ? self::$title : '操作日志',
            'type' => self::$title == '登录系统' ? 1 : (self::$title == '注销系统' ? 2 : 3),
            'content' => self::$content,
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
            'create_user' => empty(session('user_id')) ? 0 : session('user_id'),
            'create_time' => time(),
        ];
        // 日志入库
        self::insert($data);
    }

}
