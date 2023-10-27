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

use app\common\model\Invate;
use app\common\model\Member;
use app\common\model\Commission;
use app\common\model\QuantPoint;

/**
 * 城市管理-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class CityService
 * @package app\common\service
 */
class TeamService extends BaseService
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_now_month($userId)
    {

    }

    public function member($userId){

        $info = Member::where('id',$userId)->find();
        $result['recode'] = $info['recode'];
        $member= new MemberService;
        $result["recode_qrcode"] = $member->qrcode("qrcode_recommend", $info["recode"]);
        $result['url']= MAIN_URL.'/h5/page/index.html?recode=' ;
        return $result;
    }
    public function work_list($userId){

        $result=[];
        //获取当前用户的直推人
        $one_list = Invate::where(["parent_one_user_id" => $userId])->column('user_id');
        //获取当前用户的间推人
        $two_list = Invate::where(["parent_two_user_id" => $userId])->column('user_id');
        //直推收益
        $result['direct']= QuantPoint::where('user_id','IN',$one_list)->whereMonth('create_time')->where('type_id',1)->sum('num');
        $result['indirect']= QuantPoint::where('user_id','IN',$two_list)->whereMonth('create_time')->where('type_id',1)->sum('num');
        return $result;
    }

    public function get_one_child($userId)
    {

        //查询我的下级用户信息
        $one_list = Invate::where(["parent_one_user_id" => $userId])->paginate();
        foreach ($one_list as $key => &$val) {
            $member = Member::where(["id" => $val["user_id"]])->find();
            $val["username"] = substr_cut($member["username"]);
            $val["headimg"] = return_url($member['headimg']);
            $val["rank"] = $member["rank"];
            $val["reg_time"] = date("Y-m-d H:i:s",$member["reg_time"]);
        }
        return $one_list;
    }

    public function get_two_child($userId)
    {
        //查询我的下级用户信息
        $two_list = Invate::where(["parent_two_user_id" => $userId])->paginate();
        foreach ($two_list as $key => &$val) {
            $member = Member::where(["id" => $val["user_id"]])->find();
            $val["username"] = substr_cut($member["username"]);
            $val["headimg"] = return_url($member['headimg']);
            $val["rank"] = $member["rank"];
            $val["reg_time"] = date("Y-m-d H:i:s",$member["reg_time"]);
        }
        return $two_list;
    }

    /**
     * @param $userId
     * @return \think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * 获取流水
     */
    public function get_user_list($userId)
    {

        $param = $this->input;
        // 查询条件
        $map = [];
        // 验证器 验证
        $validate = getValidate([
            'type_id' => 'require',
        ], [
            'type_id.require' => '请选择类型',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }


        $map[]=['type_id','=',1];
//        $map[]=['user_id','=',$userId];
        $page= $param['page'];
        //查询我的直推下级用户ID
        $parent_one_user_id=Invate::where('parent_one_user_id',$userId)->column('user_id');
        //查询我的间推下级用户ID
        $parent_two_user_id=Invate::where('parent_two_user_id',$userId)->column('user_id');
        if($param['type_id'] == 1){
            $map[]=['user_id','IN',$parent_one_user_id];
            //模糊查询用户信息
            if(isset($param['username'])  &&  $param['username'] != ''){
                $user_id = Member::where('username','like', "%{$param['username']}%")->column('id');
                // 找到两个数组的交集
                $result = array_intersect($user_id, $parent_one_user_id);
                // 重新索引
                $result = array_values($result);
                $map[]=['user_id','IN',$result];
            }
            //模糊查询用户手机号
            if(isset($param['mobile']) && $param['mobile'] != ''){
                $user_id = Member::where('mobile','like', "%{$param['mobile']}%")->column('id');
                // 找到两个数组的交集
                $result = array_intersect($user_id, $parent_one_user_id);
                // 重新索引
                $result = array_values($result);
                $map[]=['user_id','IN',$result];
            }
            //模糊查询用户信息
            if(isset($param['start_time']) && isset($param['end_time']) &&  $param['end_time']!= '' && $param['start_time']!= ''){
                $param['start_time'] = strtotime($param['start_time']."00:00:00");
                $param['end_time'] = strtotime($param['end_time']."23:59:59");
                $map[]=['create_time','between',[$param['start_time'],$param['end_time']]];
            }
        }
        if($param['type_id'] == 2){
            $map[]=['user_id','IN',$parent_two_user_id];
            //模糊查询用户信息
            if(isset($param['username']) &&  $param['username'] != ''){
                $user_id = Member::where('username','like', "%{$param['username']}%")->column('id');
                // 找到两个数组的交集
                $result = array_intersect($user_id, $parent_two_user_id);
                // 重新索引
                $result = array_values($result);
                $map[]=['user_id','IN',$result];
            }
            //模糊查询用户手机号
            if(isset($param['mobile']) && $param['mobile'] != ''){
                $user_id = Member::where('mobile','like', "%{$param['mobile']}%")->column('id');
                // 找到两个数组的交集
                $result = array_intersect($user_id, $parent_two_user_id);
                // 重新索引
                $result = array_values($result);
                $map[]=['user_id','IN',$result];
            }
            //模糊查询用户信息
            if(isset($param['start_time']) && isset($param['end_time']) &&  $param['end_time']!= '' && $param['start_time']!= ''){
                echo 123;
                $param['start_time'] = strtotime($param['start_time']."00:00:00");
                $param['end_time'] = strtotime($param['end_time']."23:59:59");
                $map[]=['create_time','between',[$param['start_time'],$param['end_time']]];
            }
        }

        $count = QuantPoint::where($map)->count();
        $list  = QuantPoint::where($map)->page($page)->limit(20)->select();
        if($list){
            $list=$list->toArray();
            foreach ($list as &$key){
                $user = Member::where('id',$key['user_id'])->field('username,mobile')->find();
                if($user){
                    $key['mobile']=substr_cut($user['mobile']);
                    $key['username']=$user['mobile'];
                }
                $key['user_num']=sprintf("%.2f",$key['num']);
            }
        }
        $sum  = QuantPoint::where($map)->sum('num');

        return [
            'code'=>0,
            'msg'=>'success',
            'data'=>$list,
            'count'=>$count,
            'sum'=>sprintf("%.2f",$sum)
        ];

    }


}
