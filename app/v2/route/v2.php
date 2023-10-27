<?php

use think\facade\Route;

Route::group(function () {

    //获取平台余额
    Route::get("platform/balance", "member/balance");
    //交易日志
    Route::get('tradelog', 'market/trade_log');//可以搜索
    //日志  获取代币列表
    Route::get('symbol/list', 'market/symbolList');
    //获取我的持有策略
    Route::get('bonds', 'market/bonds');//可以搜索
    //获取我的策略详情
    Route::get('bonds/info', 'market/bonds_info');
    //浮亏汇总
    Route::get('loss_pool', "setup/loss_poll");
    //删除策略
    Route::post('delete', "setup/delete");
    //交易记录
    Route::get('transaction', 'market/transaction');

    //平仓前处理
    Route::post('record', 'market/record');

    //我的
    Route::get('me', 'member/index');
    //服务协议书
    Route::post('agreement', 'member/agreement');


    //团队相关
    Route::get('team', 'team/index');


    Route::get('asset', 'member/asset_list');

    //我的电子账单
    Route::get('order', 'member/order');
    //今日盈利列表
    Route::get('order/today', 'member/order_today');
    //总盈利列表
    Route::get('order/all', 'member/order_all');


    //我的团队列表
    Route::get('team/list', 'team/team_list');
//    Route::get('team/level', 'team/team_level');
//    Route::get('team/two', 'team/get_two_child');
//    Route::get('team/one', 'team/get_one_child');
//    Route::get('team/config', 'team/team_config');


    //分享二维码
    //交易记录
    Route::get('qrcode', 'member/qrcode');

    //提币
    Route::post('cash', 'member/cash');

    //提币记录
    Route::get('cash/list', 'member/cashList');
    //转账
    Route::post('move', 'member/move');
    //转账记录
    Route::get('move/list', 'member/moveList');
    //转账记录
    Route::get('transfer/list', 'member/transferList');

    //谷歌验证
    //获取谷歌二维码
    Route::post('google', 'member/googleCode');
    //获取谷歌验证状态
    Route::post('is_google', 'member/is_google');
    //绑定谷歌验证
    Route::post('bind_google', 'member/bind_google');
    //修改密码
    Route::post('password', 'member/set_password');
    //修改交易密码
    Route::post('tradepassword', 'member/set_tradepassword');
    //修改昵称
    Route::post('nickname', 'member/nickname');
    //修改头像
    Route::post('headimg', 'member/headimg');
    //关于我们
    Route::get('about/us', 'member/about_us');




    //api设置
    //获取api配置
    Route::post('platform', 'platform/platform');
    //添加/修改api
    Route::post('auth', 'member/auth');
    //获取用户是否设置过api
    Route::get('get/api', 'member/getApi');



    //获取购买配置
    Route::get('single/config', 'single/config');
    //初始化交易机器人
    Route::post('single/init', 'single/init');

    //策略配置
    Route::post('single/set', 'single/set');
    //策略详情
    Route::post('setup/info', 'setup/setupInfo');
    //层级浮亏纪录
    Route::post('floating', 'setup/floating');
    //交易设置/修改-》获取详情
    Route::post('setup/getinfo', 'setup/getInfo');
    //交易设置/修改-》获取详情 代币列表
    Route::post('setup/symbol', 'setup/getSymbol');
    //一建平仓
    Route::post('sell/all', 'setup/sell_all');//接口需要调整


    //自定义策略
    //策略配置
    Route::post('set/policy', 'single/SetPolicy');
    //一键补仓
    Route::post('single/cover', 'single/cover');
    //一键补仓 数据展示
    Route::post('cover/info', 'setup/coverInfo');
    //暂停
    Route::post('setup/stop', 'setup/stop');


    //一键平仓
//    Route::post('single/sell_all', 'single/sell_all');

    //策略修改
    Route::post('single/edit', 'single/setup_edit');
    Route::post('single/setup', 'single/set_setup');
    //提币配置
    Route::get('cash/config', 'market/get_cash_config');

    Route::get('getemail', 'index/getemail');


    Route::post('bandapi', 'member/bandapi');

    Route::post("product", "index/product");
    Route::post("product/info", "index/product_info");
    Route::post("product/margin", "index/product_margin");
    Route::post("product/save", "index/save");
    Route::post("product/order", "index/order");
    Route::post("order/info", "index/order_info");
    Route::post("order/log", "index/order_log");



})->middleware("checkApiLogin");


//不需要auth
Route::group(function () {

    //下载链接
    Route::get('download', 'symbol/download');
    Route::post('email', 'index/email');
    Route::get('about', 'member/about');

    Route::get('set', 'index/test');
    Route::get('index', 'index/index');
    #首页
    Route::get('swiper', 'index/get_swiper');//轮播图
    //公告一条
    Route::get('notice_top', 'market/notice_top');
    //公告
    Route::get('notice', 'market/notice');
    Route::get('notice_info', 'market/notice_info');

    Route::get('symbol', 'symbol/round');//行情列表(可以传platform_id)

    //登录
    Route::post('login', 'member/login');
    //注册
    Route::post('register', 'member/register');
    //忘记密码
    Route::post('forget', 'member/forget');
    //谷歌验证
    Route::post('google/code', 'member/google_code');
    //牛人排行
    Route::get('rank', 'member/ranking');


    //行情
    //获取代币行情列表


//官方文档/使用教程
    Route::get('document', 'market/document');
    Route::get('documentinfo', 'market/documentinfo');
//咨询
    Route::get('consult', 'index/consult');
    Route::get('consultinfo', 'index/consultinfo');
    //联系客服
    //客服列表
    Route::get('kefu', 'member/get_kefu_config');
    //usdt/btc
    Route::get('btc', 'member/btc');
    //教学视频
    Route::get('video', 'member/video');


    //手机号验证码登录
    Route::post('code/login', 'member/code_login');


    //app升级
    Route::post('is/version', 'member/appup');


    //更新用户数据 排行
//    Route::get('ranking', 'buy/ranking');
    Route::get('get/profit', 'buy/get_user_profit');

    //协议
    Route::get('secret', 'member/secret');
    //获取下载链接
    Route::get('get/appurl', 'market/get_appurl');
    Route::get('by', 'consult/by');


    //判断是否需要升级
//    Route::get('is/version', '');
    //排行榜
//    Route::get('rank', '');
//    //获取首页广告
//    Route::get('ad', '');
//    //获取可用的平台信息
//    Route::get('platform', '');
//
//    //获取公告新闻帮助手册
//    Route::get('notice', '');
//    Route::get('notice/info', '');
//
//    //返回联系客服信息(返回全部数据)
//    Route::get('contact', '');
//    //返回app下载链接
//    Route::get('uploads/app', '');
});


Route::miss(function () {
//    if (app()->request->isOptions()) {
//        $header = Config::get('cookie.header');
//        unset($header['Access-Control-Allow-Credentials']);
//        return Response::create('ok')->code(200)->header($header);
//    } else {
    return return_json([
        "code" => 1,
        "msg" => "error",
        "data" => "返回错误"
    ]);
//    }
});
