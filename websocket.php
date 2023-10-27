
<?php
/**
 * 使用workerman异步实现扫码检票刷新小程序页面状态
 * 大体逻辑:
 *  1.[小程序] 页面通过端口19987与 [此服务程序] 建立socket长连接,并传递order_no(或者openid)过来映射连接
 *  2.[扫码设备] 或人工扫码检票后,通过建立socket长连接请求本服务程序的19989端口,并传递order_no(或者openid)过来,回调指定页面
 */
require "vendor/workerman/workerman/Autoloader.php";
require "vendor/autoload.php";

use Workerman\Worker;
//初始化一个worker容器,监听19988端口
$worker = new Worker('websocket://0.0.0.0:19988');

/**
 * 这里进程数必须设置为1,否则会报端口占用错误
 * (php7 可以设置进程数大于1,前提是$inner_worker->reusePort=true)
 */
$worker->count = 1;

//$worker进程启动后创建一个text Worker,以便打开一个内部通讯端口
$worker->onWorkerStart = function($worker)
{
    //开启一个内部端口,方便内部系统推送数据,Text协议格式 文本+换行符
    $inner_text_worker = new Worker('text://0.0.0.0:19989');
    $inner_text_worker->onMessage=function($connection,$buffer)
    {
        //$data数组格式,里面有uid,表示向那个uid的页面推送数据
        $data = json_decode($buffer,true);
        $uid = $data['uid'];

        //通过workerman,向uid的页面推送数据
        $ret = sendMessageByUid($uid,$buffer);

        //返回推送结果
        $connection->send($ret?'ok':'fail');
    };
    $inner_text_worker->listen();
};


//当有客户端发来消息时执行的回调函数(微信小程序的话,可以接受order_no(或者openid)作为uid传过来)
$worker->onMessage = function($connection,$data)
{
    global $worker;
    $date = date('Y-m-d H:i:s',time());
    //$data = json_decode($data,true);

    $connection->send($data);

};

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
$worker->onConnect = function($connection)
{
    $connection->send('{"phone":"18561591960"}');

};

//当有客户端连接断开时,删除映射
$worker->onClose = function($connection)
{
    global $worker;
    if(isset($connection->uid))
    {
        //连接断开时,删除映射
        unset($worker->uidConnections[$connection->uid]);
    }
};

//向所有验证的用户推送数据
function broadcast($message)
{
    global $worker;
    foreach($worker->uidConnections as $connection)
    {
        $connection->send($message);
    }
}

//针对uid推送数据
function sendMessageByUid($uid,$message)
{
    global $worker;
    if(isset($worker->uidConnections[$uid]))
    {
        $connection = $worker->uidConnections[$uid];
        $connection->send($message);
        return true;
    }
    return false;
}

//运行所有worker
Worker::runAll();
