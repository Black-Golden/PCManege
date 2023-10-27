<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站=> http=>//www.rxthink.cn
// +----------------------------------------------------------------------
// | Author=> 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service;

use app\common\model\AdminUser;
use app\common\model\CacheModel;
use app\common\model\Email;
use app\common\model\Member;
use app\common\model\Menu;
use PHPMailer\PHPMailer\PHPMailer;
use think\Cache;
use think\facade\Db;

/**
 * 代码生成器-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class GenerateService
 * @package app\common\service
 */
class FollfwService extends BaseService
{

    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new AdminUser();
    }

 //点击跟但
    public function follow($id){

        $data = $this->input;
//        $admin_id = $data['admin_id'];//跟单员id
        $info=[
            "id"=> 21,
			"username"=> "771213",
			"headimg"=> "http=>//api.wending.goodsgood.top//images/person.png",
			"qb_profit_all"=> "0.00",
			"winning"=> 100,
			"profit"=> 100,

			"profit_all"=> 100,
			"num"=> 2
        ];
        $list =[
            ["id"=> 24,
                "open_type"=> 3,
                "user_id"=> 5,
                "platform_id"=> 3,
                "symbol"=> "MATIC/USDT",
                "total"=> 0,
                "sum"=> 0,
                "level"=> 1,
                "price"=> 0,
                "new_price"=> "0.86480",
                "all"=> 0,
                "is_run"=> 1,
                "profit"=> 0],
            ["id"=> 24,
                "open_type"=> 3,
                "user_id"=> 5,
                "platform_id"=> 3,
                "symbol"=> "BTC/USDT",
                "total"=> 0,
                "sum"=> 0,
                "level"=> 1,
                "price"=> 0,
                "new_price"=> "0.86480",
                "all"=> 0,
                "is_run"=> 1,
                "profit"=> 0
            ]
        ];

        return ['data'=>['info'=>$info,'data'=>$data]];

    }

}
