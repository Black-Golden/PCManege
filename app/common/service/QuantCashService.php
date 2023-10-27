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


use app\common\model\Member;
use app\common\model\QuantCash;

/**
 * 提现记录管理-服务类
 * @author 测试
 * @since: 2022/01/23
 * Class QuantCashService
 * @package app\adminapi\service
 */
class QuantCashService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new QuantCash();
    }

	/**
     * 获取数据列表
     * @return array
     * @since 2022/01/23
     * @author 测试
     */
    public function getList()
    {
        $param = $this->input;

        // 查询条件
        $map = [];
        if(isset($param['start_time']) && isset($param['end_time'])){
            $start = strtotime($param['start_time']);
            $end = strtotime($param['end_time']);
            $map[] = ['create_time', 'BETWEEN', [$start,$end]];
        }
        if(isset($param['user_name'])){
//            $ids = Member::where('username','like','%user_name%') ->column('id');
            $ids = Member::where('username','like',"%{$param['user_name']}%") ->column('id');
//            var_dump($ids);die;
            $map[]=['user_id','IN',$ids];
        }
        return parent::getList($map); // TODO: Change the autogenerated stub
    }


    public function getUser($userId){
        // 查询条件
        $map = [];
        $map[]=['user_id','=',$userId];
        return parent::getList($map); // TODO: Change the autogenerated stub

    }
    public function edit()
    {
        // 请求参数
        $data = request()->param();

        //查看类型
        if(isset($data['is_check'])){
            if($data['is_check'] == 2){
                $return_usdt= $data['amount']+$data['fee'];
                Member::where('id',$data['user_id'])->inc('qb_usdt',$return_usdt)
                    ->update();
            }
        }

//        var_dump($data);die;
        return parent::edit($data); // TODO: Change the autogenerated stub
    }


}