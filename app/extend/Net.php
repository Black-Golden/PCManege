<?php
namespace app\extend;
use think\facade\Db;

class Net
{
	var $dbs_class;
	var $user_id = null;
	var $cat_id = null;
	
	var $self,$parent,$root;
	
	var $arr_path = array();
	var $arr_route = array();
	
	// 互转
	public function init(){

		if( $this->user_id==null && $this->cat_id==null )return;
		if( $this->user_id==null ){
			$this->user_id = Db::name($this->dbs_class)
				->where('cat_id',$this->cat_id)
				->value('user_id');
		}
		if( $this->cat_id==null ){
			$this->cat_id = Db::name($this->dbs_class)
				->where('user_id',$this->user_id)
				->value('cat_id');
		}

	}
	
	// 路由递归
	// 获取路径
	public function get_route(){
		$parent_id = Db::name($this->dbs_class)
			->where('cat_id',$this->cat_id)
			->value('parent_id');
		array_push($this->arr_path,$this->cat_id);
		if( $parent_id == 0 ){
			return;
		}
		$this->cat_id = $parent_id;
		$this->get_route();
	}
	
	public function rule_route(){
		for($i=0;$i<count($this->arr_path);$i++){
			$row_private = Db::name('quant_cat_private')
				->field('cat_id,user_id,cat_name,depth,full_path')
				->where('cat_id',$this->arr_path[$i])
				->find();
			$row_member = Db::name('member')
				->field('rank')
				->where('id',$row_private['user_id'])
				->find();
			$row_private['rank'] = $row_member['rank'];
			array_push($this->arr_route,$row_private);
		}
	}
	
}
