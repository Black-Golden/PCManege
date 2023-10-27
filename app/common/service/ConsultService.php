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


use app\common\model\Consult;

/**
 * 咨询管理-服务类
 * @author 测试
 * @since: 2022/02/11
 * Class ConsultService
 * @package app\adminapi\service
 */
class ConsultService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Consult();
    }


    /**
     * 获取数据列表
     * @return array
     * @since 2022/02/11
     * @author 测试
     */
    public function list()
    {
        $page=1;
        if(isset($this->input['page'])){
            $page = $this->input['page'];
        }

        $arr=[];
       $list= $this->model->where('mark',1)->page($page)->limit(20)->order('update_time','DESC')->select();
       $count= $this->model->where('mark',1)->count();

        foreach ($list as &$key){
            $time=strtotime($key['update_time']);
            $key['time']=date('H:i',$time);
            $arr[date('m/d',$time)]['time']=date('m/d',$time);
            $arr[date('m/d',$time)]['child'][]=$key;
        }
        $arr = array_values($arr);
        return return_json([
            'code'=>0,
            'data'=>$arr,
            'count'=>$count,
        ]);
    }

    /**
     * 获取数据列表
     * @return array
     * @since 2022/02/11
     * @author 测试
     */
    public function info()
    {
        $param = $this->input;
        $list= $this->model->where('mark',1)->where('id',$param['id'])->find();
        return $list;
    }


    /**
     * 爬取数据
     */
    public function by($page = 1)
    {
        if($page == 5){
            die;
        }
        $list = curl_get('https://www.finacerun.com/api/v1/basic/week/Lists?limit=10&page='.$page);
        $list=json_decode($list,true);
        if(isset($list['data']['data'])){
            $list = $list['data']['data'];
            foreach ($list as $key){
                $id  = Consult::where('by_id',$key['id'])->find();
                if($id){
                    continue;
                }
                $data = [
                    'by_id'=>$key['id'],
                    'title'=>$key['title'],
                    'content'=>$key['content'],
                    'create_time'=>$key['w_time'],
                    'update_time'=>$key['w_time'],
                ];
                $this->model->create($data);
            }
        }
        return $this->by($page+1);

    }








}
