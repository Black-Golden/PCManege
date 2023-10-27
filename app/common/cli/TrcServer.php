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

class TrcServer extends Command
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
        $this->setName('trcserver')
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
        //$this->startRegister();
        Worker::runAll();
    }

    private function startBusinessWorker()
    {
        //$list = QuantSymbol::where(["is_online"=>1,"title"=>"BTC/USDT"])->limit("1")->select();
        //foreach ($list as $key=>$val){
            $worker = new BusinessWorker();
          //  $worker->name = $val["title"];                        #设置BusinessWorker进程的名称
            $worker->count =4;                                       #设置BusinessWorker进程的数量
            $worker->eventHandler = \app\socket\service\Events::class;
        //}
    }
}
