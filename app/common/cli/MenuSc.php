<?php
declare (strict_types=1);

namespace app\common\cli;
use app\common\middleware\InitApp;
use app\common\model\Menu;
use app\common\service\GoodsService;
use mydogger\pinyin\Pinyin;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\service\GenerateService;
use think\facade\Db;

class MenuSc extends Command
{


    protected function configure()
    {
        // 指令配置
        $this->setName('menu_sc')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {
//        $server = new GenerateService();
//        $server->input =[
//            "id"=>1,
//            "1"=>1,
//        ];
//       $list =  $server->generate();
        // $columnList = $server->getColumnList( "yrnet_member");
        // 指令输出
//        $output->writeln('hello');

//        $list = Db::table("yrnet_zphnet_com")->where(["zid"=>4,"status"=>1])->field("uid")->select();
//        $str ='';
//        foreach ($list as $key=>$value){
//            $data = Db::table("yrnet_company")->where(["uid"=>$value["uid"],"r_status"=>1])->field("uid,name")->find();
//            if($data){
//                $str.=$data["uid"]."|".$data["name"].",";
//            }
//
//
//        }

//        echo $str;
//        //echo json_encode($data,JSON_UNESCAPED_UNICODE);
//        exit;
        define('DB_PREFIX', env('database.prefix', ''));
        $init = new InitApp();
        $init->initSystemConstant();
//
//        $goods =  new GoodsService();
//
//        $goods->input["files"] ="/temp/20211219/ed49a454639791dc234619268535ff21.xlsx";
//        print_r($goods->export(1));
//        exit;
//        $server->input =[
//            "id"=>1,
//            "1"=>1,
//        ];

//        $o = new OrderInfoService();
//
//        $a = $o->importExcel("1.xls");
//        print_r($a);
//        exit;
//        $table = new UserRole();

        $return_data=[
            "公海审核",
            "发票"
        ];
        $menu = Menu::where(["title"=>$return_data[0]])->find();
        $py = Pinyin::first($return_data[0]).":".Pinyin::first($return_data[1]);

        $data = [
            'pid' => $menu['id'],
            'title' => $return_data[0]."-".$return_data[1],
            'permission' => $py,
            'type' => 1,
        ];
        $result = Menu::create($data);

//        var_dump($result);
//        exit;
        // $columnList = $server->getColumnList( "yrnet_member");

        // 指令输出
        $output->writeln('hello');
    }
}
