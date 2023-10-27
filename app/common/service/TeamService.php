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
use app\common\model\TeamConfig;

/**
 * 城市管理-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class CityService
 * @package app\common\service
 */
class TeamService extends BaseService
{

    public $list;

    public function __construct()
    {
        parent::__construct();

    }

    public function adminTeam()
    {

        $id = $this->input['id'];
        $distributor = new Member();
        $idslevel = $distributor->where("referee_id", $id)->select();//Query(['referee_id' => ['in', implode(',', $uids)]], 'uid');
        foreach ($idslevel as &$key) {
            $key['headimg'] = get_image_url($key['headimg']);

            $key['hasChildren'] = false;
            if ($key['team_all']) {
                $key['hasChildren'] = true;
            }
        }

        return $idslevel;

    }
//获取我的所有上级
    public function adminTeamTop()
    {
        $id = $this->input['id'];
        $data=[];

        $this->adminTop($id,$data);

        $referee_id = Member::where('id','IN',$data)->select();

        return $referee_id;

    }

    public function adminTop($uids,&$data=[])
    {
        $distributor = new Member();
        $ids = $distributor->where("id", $uids)->value('referee_id');//Query(['referee_id' => ['in', implode(',', $uids)]], 'uid');

        if ($ids) {
            $data[] = $ids;
            return $this->adminTop($ids,$data);

        }
        return $data;
    }
    public function getMyTeam($uids = [])
    {
        $distributor = new Member();
        $idslevel = $distributor->whereIn("referee_id", $uids)->column("id");//Query(['referee_id' => ['in', implode(',', $uids)]], 'uid');
        if ($idslevel) {
            $this->list[] = $idslevel;
            $this->getMyTeam($idslevel);
            //$list = array_merge($list, $this->getMyTeam($idslevel));
        }
    }

    public function getMyTop($uids = [])
    {
        $distributor = new Member();
        $idslevel = $distributor->whereIn("id", $uids)->column("id,referee_id,rank");//Query(['referee_id' => ['in', implode(',', $uids)]], 'uid');

        if ($idslevel) {
            $this->list[] = $idslevel[0];

            $idslevel = [
                $idslevel[0]["referee_id"]
            ];
            $this->getMyTop($idslevel);
            //$list = array_merge($list, $this->getMyTeam($idslevel));
        }
    }

    public function team($user_id)
    {
        $this->list = [];
        $this->getMyTeam([$user_id]);
        return $this->list;
    }

    public function team_onw_two($user_id)
    {
        $arr = $this->team($user_id);
        $user_ids = [];
//        if (isset($arr[0])) {
//            $count += count($arr[0]);
//        }
//        if (isset($arr[1])) {
//            $count += count($arr[1]);
//        }
        if ($arr) {
            foreach ($arr as $key => $value) {
                if ($key <= 1) {
                    foreach ($value as $k => $v) {
                        $user_ids[] = $v;
                    }
                }
            }
        }
        return [
            "user_ids" => $user_ids,
        ];
    }

    public function top($user_id)
    {
        $this->list = [];
        $this->getMyTop([$user_id]);
        return $this->list;
    }

    public $type = [5 => 'LV5', 1 => 'LV1', 2 => 'LV2', 3 => 'LV3', 4 => 'LV4', 6 => 'LV6', 7 => 'LV7', 0 => 'LV0',];

    //获取team列表
    public function getTeam($user_id)
    {
        $memberModel = new Member();
        $return = ['code' => 0, 'count' => 0, 'data' => ''];
        $list = $this->team($user_id);
        $type = $this->input['type'];
        if ($list) {
            $arr = [];
            $ids = [];
            //直推
            if ($type == 1) {
                $ids = $list[0];
                $count = $memberModel->where('id', 'IN', $ids)->count();
                $list = $memberModel->where('id', 'IN', $ids)->order('id desc')->page(PAGE, PERPAGE)->select();
                foreach ($list as $value) {
                    $sum = $memberModel->where('referee_id', $value['id'])->count();
                    $arr[] = [
                        'team_person' => $value['team_all'],
                        'today_profit' => $value['qb_profit_today'],
                        'one_person' => $sum,
                        'create_time' => date('Y-m-d', $value['reg_time']),
                        'username' => $value['username'],
                        'level' => $this->type[$value['rank']]
                    ];
                }
                $list = $arr;

            } else {
                //循环出所有的团队id
                foreach ($list as $k => $value) {
                    if ($k == 1) {
                        foreach ($value as $v) {
                            array_push($ids, $v);
                        }
                    }
                }
                $count = $memberModel->where('id', 'IN', $ids)->count();
                $list = $memberModel->where('id', 'IN', $ids)->order('id desc')->page(PAGE, PERPAGE)->select();
                foreach ($list as $value) {
                    $sum = $memberModel->where('referee_id', $value['id'])->count();
                    $arr[] = [
                        'team_person' => $value['team_all'],
                        'today_profit' => $value['qb_profit_today'],
                        'one_person' => $sum,
                        'create_time' => date('Y-m-d', $value['reg_time']),
                        'username' => $value['username'],
                        'level' => $this->type[$value['rank']]
                    ];
                }
                $list = $arr;
            }
            $return['count'] = $count;
            $return['data'] = $list;
        }

        return $return;
    }


    public function per_top($user_id)
    {
        $list = $this->top($user_id);
        $one_person = $new_arr = $arr = [];
        if ($list) {
            foreach ($list as $key => $value) {
                $value["per"] = 1;
                if ($key == 0) {
                    $one_person = [
                        "id" => $value["referee_id"],
                        "per" => $value["per"],
                    ];
                } else {
                    if ($list[$key]["rank"] != 0) {
                        $arr[] = [
                            "id" => $value["id"],
                            "per" => $value["per"],
                            "rank" => $value["rank"]
                        ];
                    }


                }
            }
            if ($arr) {
                $arr = array_reverse($arr);
                foreach ($arr as $key => $value) {
                    if ($key < (count($arr) - 1)) {
                        if ($value["rank"] > $arr[$key + 1]["rank"]) {
                            $new_arr[] = $value;
                        }
                    }
                    if ($key == (count($arr) - 1)) {
                        $new_arr[] = $value;
                    }
                }
                $new_arr = array_reverse($new_arr);

                if (count($new_arr) == 1) {
                    $new_arr[0]["per"] = $new_arr[0]["rank"];
                }
                if (count($new_arr) == 2) {
                    $new_arr[0]["per"] = $new_arr[0]["rank"];
                    $new_arr[1]["per"] = $new_arr[1]["rank"] - $new_arr[0]["rank"];
                }
                if (count($new_arr) == 3) {
                    $new_arr[0]["per"] = $new_arr[0]["rank"];
                    $new_arr[1]["per"] = $new_arr[1]["rank"] - $new_arr[0]["rank"];
                    $new_arr[2]["per"] = $new_arr[2]["rank"] - $new_arr[1]["rank"];
                }
                if (count($new_arr) == 4) {
                    $new_arr[0]["per"] = $new_arr[0]["rank"];
                    $new_arr[1]["per"] = $new_arr[1]["rank"] - $new_arr[0]["rank"];
                    $new_arr[2]["per"] = $new_arr[2]["rank"] - $new_arr[1]["rank"];
                    $new_arr[3]["per"] = $new_arr[3]["rank"] - $new_arr[2]["rank"];
                }
                if (count($new_arr) == 5) {
                    $new_arr[0]["per"] = $new_arr[0]["rank"];
                    $new_arr[1]["per"] = $new_arr[1]["rank"] - $new_arr[0]["rank"];
                    $new_arr[2]["per"] = $new_arr[2]["rank"] - $new_arr[1]["rank"];
                    $new_arr[3]["per"] = $new_arr[3]["rank"] - $new_arr[2]["rank"];
                    $new_arr[4]["per"] = $new_arr[4]["rank"] - $new_arr[3]["rank"];
                }
                if (count($new_arr) == 6) {
                    $new_arr[0]["per"] = $new_arr[0]["rank"];
                    $new_arr[1]["per"] = $new_arr[1]["rank"] - $new_arr[0]["rank"];
                    $new_arr[2]["per"] = $new_arr[2]["rank"] - $new_arr[1]["rank"];
                    $new_arr[3]["per"] = $new_arr[3]["rank"] - $new_arr[2]["rank"];
                    $new_arr[4]["per"] = $new_arr[4]["rank"] - $new_arr[3]["rank"];
                    $new_arr[5]["per"] = $new_arr[5]["rank"] - $new_arr[4]["rank"];
                }
                if (count($new_arr) == 7) {
                    $new_arr[0]["per"] = $new_arr[0]["rank"];
                    $new_arr[1]["per"] = $new_arr[1]["rank"] - $new_arr[0]["rank"];
                    $new_arr[2]["per"] = $new_arr[2]["rank"] - $new_arr[1]["rank"];
                    $new_arr[3]["per"] = $new_arr[3]["rank"] - $new_arr[2]["rank"];
                    $new_arr[4]["per"] = $new_arr[4]["rank"] - $new_arr[3]["rank"];
                    $new_arr[5]["per"] = $new_arr[5]["rank"] - $new_arr[4]["rank"];
                    $new_arr[6]["per"] = $new_arr[6]["rank"] - $new_arr[5]["rank"];
                }
            }
        }
        return [
            "one_list" => $one_person,
            "two_list" => $new_arr
        ];
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
            $val["reg_time"] = date("Y-m-d H:i:s", $member["reg_time"]);
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
            $val["reg_time"] = date("Y-m-d H:i:s", $member["reg_time"]);
        }
        return $two_list;
    }

    public function team_level($userId)
    {
        //查询我的下级用户信息
        $team_id = Invate::where(["user_id" => $userId])->value('team_id');
        $info = TeamConfig::where('id', $team_id)->find();
        if ($info) {
            $info['one_per'] = $info['one_per'] * 100 . '%';
            $info['two_per'] = $info['two_per'] * 100 . '%';
        }
        return $info;
    }

    public function team_config()
    {
        $list = TeamConfig::select();
        if ($list) {
            foreach ($list as $item) {
                $item['one_per'] = $item['one_per'] * 100 . '%';
                $item['two_per'] = $item['two_per'] * 100 . '%';
            }
        }
        return $list;
    }

    //获取收入明细
    public function team_list($userId)
    {
        //查询我的下级用户信息
        $two_list = Commission::where(["user_id" => $userId])->order('id', "DESC")->paginate();

        foreach ($two_list as $key => &$val) {
            $member = Member::where(["id" => $val["form_user_id"]])->find();
            $val["username"] = '';
            $val["create_time"] = '';
            if ($member) {
                $val["username"] = substr_cut($member["username"]);
//                $val["create_time"] = date("Y-m-d H:i:s",$val["create_time"]);
            }
            $val['now_per'] = $val['now_per'] * 100 . '%';
            $val['num'] = sprintf("%.2f", $val['num']);
            $val['user_num'] = sprintf("%.2f", $val['user_num']);
            $val['type'] = '间推用户';
            if ($val['type_id'] == 1) {
                $val['type'] = '直推用户';
            }
        }
        return $two_list;

    }
}
