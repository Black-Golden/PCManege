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

namespace app\adminapi\controller;

use app\BaseController;

/**
 * 后台-控制器
 * @author 牧羊人
 * @since 2020/11/14
 * Class Backend
 * @package app\adminapi\controller
 */
class Backend extends BaseController
{
    // 模型
    protected $model;
    // 服务
    protected $service;
    // 用户ID
    protected $user_id;
    // 登录信息
    protected $userInfo;
    // 中间件
    protected $middleware = [
        'checkLogin'
    ];

    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/11/14
     */
    protected function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub

        // 获取Token
        $token = request()->header("Authorization");
        if ($token && strpos($token, 'Bearer ') !== false) {
            $token = str_replace("Bearer ", null, $token);
            // JWT解密token
            $jwt = new \Jwt();
            $this->user_id = $jwt->verifyToken($token);
        }

        // 初始化配置
        $this->initConfig();
    }

    /**
     * 初始化配置
     * @author 牧羊人
     * @since 2020/11/14
     */
    public function initConfig()
    {
        // 定义是否GET请求
        defined('IS_GET') or define('IS_GET', $this->request->isGet());

        // 定义是否POST请求
        defined('IS_POST') or define('IS_POST', $this->request->isPost());

        // 定义是否AJAX请求
        defined('IS_AJAX') or define('IS_AJAX', $this->request->isAjax());

        // 定义是否PAJAX请求
        defined('IS_PJAX') or define('IS_PJAX', $this->request->isPjax());

        // 定义是否PUT请求
        defined('IS_PUT') or define('IS_PUT', $this->request->isPut());

        // 定义是否DELETE请求
        defined('IS_DELETE') or define('IS_DELETE', $this->request->isDelete());

        // 定义是否HEAD请求
        defined('IS_HEAD') or define('IS_HEAD', $this->request->isHead());

        // 定义是否PATCH请求
        defined('IS_PATCH') or define('IS_PATCH', $this->request->isPatch());

        // 定义是否为手机访问
        defined('IS_MOBILE') or define('IS_MOBILE', $this->request->isMobile());

        // 定义是否为cli
        defined('IS_CLI') or define('IS_CLI', $this->request->isCli());

        // 定义是否为cgi
        defined('IS_CGI') or define('IS_CGI', $this->request->isCgi());

        // 分页参数
        defined('PAGE') or define('PAGE', getter(request()->param(), "page", 1));
        defined('PERPAGE') or define('PERPAGE', getter(request()->param(), "limit", 20));

    }

    /**
     * 获取数据列表
     * @return mixed
     * @since 2020/11/11
     * @author 牧羊人
     */
    public function index()
    {
        $result = $this->service->getList();
        return $result;
    }

    /**
     * 获取数据详情
     * @return mixed
     * @since 2020/11/11
     * @author 牧羊人
     */
    public function info()
    {
        $result = $this->service->info();
        return $result;
    }

    /**
     * 添加或编辑
     * @return mixed
     * @since 2020/11/11
     * @author 牧羊人
     */
    public function edit()
    {
        $result = $this->service->edit();
        return $result;
    }

    /**
     * 删除数据
     * @return mixed
     * @since 2020/11/11
     * @author 牧羊人
     */
    public function delete()
    {
        $result = $this->service->delete();
        return $result;
    }

    /**
     * 设置状态
     * @return mixed
     * @since 2020/11/21
     * @author 牧羊人
     */
    public function status()
    {
        $result = $this->service->status();
        return $result;
    }

    /**
     * 更新信息
     * @return mixed
     * @since 2020/11/21
     * @author 牧羊人
     */
    public function setattr()
    {
        $result = $this->service->setattr();
        return $result;
    }

    /**
     * 更新全部信息
     * @return mixed
     * @since 2020/11/21
     * @author 牧羊人
     */
    public function setallattr()
    {
        $result = $this->service->setallattr();
        return $result;
    }

    public function field()
    {
        $result = $this->service->get_field($this->user_id);
        return $result;
    }

    public function searchform()
    {
        $result = $this->service->searchform($this->user_id);
        return return_json($result);
    }

    public function createform()
    {
        $result = $this->service->createform($this->user_id);
        return return_json($result);
    }

    public function editform()
    {
        $result = $this->service->editform($this->user_id);
        return return_json($result);
    }

    public function set_examine()
    {
        $result = $this->service->setExamine($this->user_id);
        return return_json($result);
    }
    //停用 启用
    public function set_enable()
    {
        $result = $this->service->setEnable();
        return $result;
    }

}
