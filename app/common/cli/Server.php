<?php
namespace app\common\cli;
use app\common\middleware\InitApp;
use app\common\model\QuantSymbol;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use GatewayWorker\BusinessWorker;
use Workerman\Worker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;

class Server extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker:server {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a GatewayWorker Server.';

    protected function configure()
    {
        $this->setName('server')
            ->addArgument('action', Argument::OPTIONAL, "action  start|stop|restart|status")
            ->addArgument('type', Argument::OPTIONAL, "d -d")
            ->setDescription('workerman chat');
    }

    protected function execute(Input $input, Output $output)
    {
        global $argv;
        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();
        $action = trim($input->getArgument('action'));
        $type = trim($input->getArgument('type')) ? '-d' : '';
        $argv[0] = '';
        $argv[1] = $action;
        $argv[2] = $type ? '-d' : '';
//        $init = new InitApp();
//        $init->initSystemConstant();
        $this->start();
    }

    private function start()
    {
        $this->startBusinessWorker();
        Worker::runAll();
    }

    private function startBusinessWorker()
    {
        //$list = QuantSymbol::where(["is_online"=>1,"title"=>"BTC/USDT"])->limit("1")->select();
        //foreach ($list as $key=>$val){
            $worker = new BusinessWorker();
          //  $worker->name = $val["title"];                        #设置BusinessWorker进程的名称
            $worker->count =5;                                       #设置BusinessWorker进程的数量
           // $worker->registerAddress = '127.0.0.1:'.env("CENTER_PORT");                        #注册服务地址
            $worker->eventHandler = \app\socket\service\Events::class;
        //}


                  #设置使用哪个类来处理业务,业务类至少要实现onMessage静态方法，onConnect和onClose静态方法可以不用实现
    }

    private function startWebGateWay()
    {
        $context = array(
            /*'ssl' => array(
               // 'local_cert' => base_path() . "/rsa/1.pem", // 也可以是crt文件
               // 'local_pk' => base_path() . "/rsa/1.key",
                'verify_peer' => false,
            )*/
        );
        $gateway = new Gateway("websocket://0.0.0.0:".env("WEB_PORT"), $context);
        $gateway->name = 'websocket';                         #设置Gateway进程的名称，方便status命令中查看统计
        $gateway->count = 4;                                 #进程的数量
        $gateway->lanIp = '127.0.0.1';                       #内网ip,多服务器分布式部署的时候需要填写真实的内网ip
        $gateway->startPort = 2300;                              #监听本机端口的起始端口
        $gateway->pingInterval = 0;
        $gateway->pingNotResponseLimit = 5;                                 #服务端主动发送心跳
        $gateway->pingData = '{"mode":"webheart"}';
        $gateway->registerAddress = '127.0.0.1:'.env("CENTER_PORT");                  #注册服务地址
        //$gateway->transport = 'ssl';
    }


    private function startRegister()
    {
        $gateway = new Register('text://0.0.0.0:'.env("CENTER_PORT"));
    }

    private function startWSSGateWay()
    {
        $context = array(
            'ssl' => array(
                'local_cert' => base_path() . "/rsa/1.pem", // 也可以是crt文件
                'local_pk' => base_path() . "/rsa/1.key",
                'verify_peer' => false,
            )
        );
        $gateway = new Gateway("websocket://0.0.0.0:23460", $context);
        $gateway->name = 'location';                         #设置Gateway进程的名称，方便status命令中查看统计
        $gateway->count = 5;                                 #进程的数量
        $gateway->lanIp = '127.0.0.1';                       #内网ip,多服务器分布式部署的时候需要填写真实的内网ip
        $gateway->startPort = 2300;                              #监听本机端口的起始端口
        $gateway->pingInterval = 30;
        $gateway->pingNotResponseLimit = 0;                                 #服务端主动发送心跳
        $gateway->pingData = '{"mode":"heart"}';
        $gateway->registerAddress = '127.0.0.1:'.env("CENTER_PORT");                  #注册服务地址
        $gateway->transport = 'ssl';
    }
}

//ws = new WebSocket("ws://127.0.0.1:23460");
//ws.onopen = function() {
//    ws . send('{"mode":"say","order_id":"21",type:1,"content":"文字内容","user_id":21}');
//    ws . send('{"mode":"chats","order_id":"97"}');
//};
//ws.onmessage = function(e) {
//    console.log("收到服务端的消息：" + e.data);
//};
