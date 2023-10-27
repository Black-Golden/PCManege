<?php
namespace app\extend;

use think\facade\Db;

class Error
{
	//登录判断
	static public function chk_login(){
		if( Session::has('user') ){
			return true;
		}else{
			return false;
		}
	}
	
	//空值判断
	static public function chk_empty($param){
		if( empty($param) ){
			return true;
		}
		if($param == null || $param == ''){
			return true;
		}else{
			return false;
		}
	}
	
	//数字判断
	static public function chk_number($param){
		if( is_numeric($param) ){
			if( (int)$param == $param )
			if( $param > 0 )
			return true;
		}else{
			return false;
		}
	}
	
	//支付密码
	static public function chk_password2($user_id,$password2){
		$user = Db::name('quant_member')
			->field('tradepassword')
			->where('user_id',$user_id)
			->find();
		if( empty($user) ){
			return false;
		}
		if( $user['tradepassword'] == md5(sha1($password2)) ){
			return true;
		}
		return false;
	}
	
	//生成错误信息
	static public function be_error($msg){
		$result = [
			'state'	=> 'error',
			'msg'	=> $msg
		];
		return json_encode($result);
	}
	
	//生成成功信息
	static public function be_success($msg){
		$result = [
			'state'	=> 'success',
			'msg'	=> $msg
		];
		return json_encode($result);
	}
}













