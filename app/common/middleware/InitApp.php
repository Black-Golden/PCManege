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

declare (strict_types=1);

namespace app\common\middleware;

use app\common\model\Config;
use app\common\model\QuantConfig;

class InitApp
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 初始化系统常量
        $this->initSystemConstant();

        // 初始化消息中间件RabbitMQ常量
        $this->initRabbitMQ();

        // 初始化数据库常量
        $this->initDbInfo();
        return $next($request);
    }

    /**
     * 初始化系统常量
     *
     * @author 牧羊人
     * @since 2020-04-21
     */
    public function initSystemConstant()
    {
        // 基础常量
        define('ROOT_PATH', app()->getRootPath());
        define('APP_PATH', ROOT_PATH . "app");
        define('ROUTE_PATH', ROOT_PATH . "route");
        define('RUNTIME_PATH', ROOT_PATH . "runtime");
        define('EXTEND_PATH', ROOT_PATH . "extend");
        define('VENDOR_PATH', ROOT_PATH . "vendor");
        define('PUBLIC_PATH', ROOT_PATH . 'public');

        // 附件常量
        // 文件上传路径
        //$upload_parh = \think\facade\Filesystem::getDiskConfig(config('filesystem.default'), 'root');
        define('ATTACHMENT_PATH', PUBLIC_PATH."/uploads");
//        define('IMG_PATH', ATTACHMENT_PATH . "/images");
        define('UPLOAD_TEMP_PATH', ATTACHMENT_PATH . '/temp');
        define('UPLOAD_LOG_PATH', ATTACHMENT_PATH . '/log');
//        define('IMG_PATH', PUBLIC_PATH . "/images");
        // 系统配置
        define('SITE_NAME', env('system.sitename'));
        define('NICK_NAME', env('system.nickname'));
        define('SYSTEM_VERSION', env('system.version'));

        // 系统域名
        define('SITE_URL', env('domain.siteurl'));
        define('IMG_URL', env('domain.img_url'));
        define('IMG_PATH', env('domain.imgs_url'));
        define('RECORD_URL', env('domain.record_url'));
        define('H5_URL', env('domain.h5_url'));
        define('IOS_URL', env('domain.ios_url'));
        //系统域名
        define('MAIN_URL', env('main.main_url'));

        // 七牛云
        define('ACCESS_KEY', env('qiniu.access_key'));
        define('SECRET_KEY', env('qiniu.secret_key'));
        define('BUCKET', env('qiniu.bucket'));
        define('DOMAIN', env('qiniu.domain'));

//        //查询配置项
//        $quant = QuantConfig::where(["mark" => 1])->column("value", "name");
//        print_r($quant);
//        config($quant, 'app');
////         读取系统配置
        $system_config = cache('system_config');
        if (!$system_config) {
            $system_config = QuantConfig::where(["mark" => 1])->column("value", "name");
            //if ($system_config['develop_mode'] == 0) {
            cache('system_config', $system_config);
            //}
        }
        // 设置配置信息
        config($system_config, 'app');

    }

    /**
     * 初始化RabbitMQ
     *
     * @author 牧羊人
     * @since 2020-04-21
     */
    public function initRabbitMQ()
    {

    }

    /**
     * 初始化数据库常量
     *
     * @author 牧羊人
     * @since 2020-04-21
     */
    public function initDbInfo()
    {
        // 数据表前缀
        define('DB_PREFIX', env('database.prefix', ''));
    }

}
