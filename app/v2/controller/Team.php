<?php

namespace app\v2\controller;

use app\common\model\Invate;
use app\common\service\MemberService;
use app\common\service\TeamService;
use think\facade\Db;
use think\facade\Request;
use app\v1\model\ProfitModel;

class Team extends Backend
{
    public function index()
    {
        $member = new \app\common\model\Member();
        $info = $member->getInfo($this->user_id);

        //查询距离下次还剩多少
        $rank = $info["rank"];
        $next_rank = $info["rank"] + 1;
        $all_profit = $info['qb_team_all'] + $info['qb_profit_all'];
        $last_per = 0;
        $last_profit = 0;
        $next_profit = 0;
        if ($next_rank >= 8) {
            $next_rank = 0;
            $next_profit = 0;
        }
        if ($next_rank == 1) {
            $next_profit = 1000;
        }
        if ($next_rank == 2) {
            $next_profit = 5000;
        }
        if ($next_rank == 3) {
            $next_profit = 10000;
        }
        if ($next_rank == 4) {
            $next_profit = 20000;
        }
        if ($next_rank == 5) {
            $next_profit = 50000;
        }
        if ($next_rank == 6) {
            $next_profit = 100000;
        }
        if ($next_rank == 7) {
            $next_profit = 200000;
        }

        if ($rank == 7) {
            $next_profit = 0;
            $last_profit = 0;
        }
        if ($all_profit != 0 && $next_profit != 0) {
            $last_per = $all_profit / $next_profit * 100;
            $last_profit = $next_profit - $all_profit;
        }

        //$all_profit = $info['qb_team_all'] + $info['qb_profit_all'];
        $result = [
            "qb_team_all" => $info['qb_team_all'] ?? 0,
            "qb_profit_all" => $info['qb_profit_all'] ?? 0,
            "team_profit" => $info['qb_team_all'] ?? 0,
            "last_per" => $last_per ??0,
            "last_profit" => $last_profit,
            "next_profit" => $next_profit,
            "rank" => $rank,
            "next_rank" => $next_rank,
            "team_person" => $info['team_all'] ?? 0,
            "team_today_profit" => $info['qb_team_today'] ?? 0,
            "one_today_profit" => $info['qb_child_today'] ?? 0,
            "two_today_profit" => $info['qb_indirect_today'] ?? 0,
        ];

        return return_json($result);
    }

    //获取用户的团队等级
    public function team_list()
    {
        //type  = 1
        $team = new TeamService();
        $results = $team->getTeam($this->user_id);
        return return_json($results);

        //return return_json($team->team_list($this->user_id));
    }


}

















