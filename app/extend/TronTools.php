<?php
namespace app\extend;

use app\tron\TronKit;
use app\tron\TronApi;
use app\tron\Credential;
use app\extend\Error;

class TronTools
{
	var $contract_address = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

	/*
	 * http://www.coincou.sh/index/trc20/create.html
	 * 创建账号 trc20
	 */
	public function create(){
		$credential = Credential::create();
		$result = [
			'private_key'	=> $credential->privateKey(),
			'address'		=> $credential->address()->base58()
		];
		return $result;
	}

	/*
	 * http://www.coincou.sh/index/trc20/query.html
	 * 查询 trc20 usdt 余额
	 * param: [contract_address]
	 * param: private_key
	 */
	public function get_balance( $private_key ){
		$credential = Credential::fromPrivateKey($private_key);
		$address = $credential->address()->base58();
		$kit = new TronKit(TronApi::mainNet(),$credential);
		$inst = $kit->trc20($this->contract_address);
		$balance_trx = $kit->getTrxBalance($address);
		$balance_usdt = $inst->balanceOf($address);
		$result = [
			'address'	=> $address,
			'trx'		=> (string)$balance_trx /pow(10,6),
			'usdt'		=> (string)$balance_usdt
		];
		return $result;
	}

	/*
	 * http://www.coincou.sh/index/trc20/transfer.html
	 * 转账 trc20 usdt
	 * param: [contract_address]
	 * param: private_key_from
	 * param: address_to
	 * param: amount
	 * return txid,result = 1
	 */
	public function transfer_usdt($private_key_from,$address_to,$amount){
		$credential = Credential::fromPrivateKey($private_key_from);
		$address_from = $credential->address()->base58();
		$kit = new TronKit(TronApi::mainNet(),$credential);
		$inst = $kit->trc20($this->contract_address);
        $balance = $kit->getTrxBalance($address_from);
        if($balance>10){
            //$balance_usdt = $inst->balanceOf($address_from);
            $ret = $inst->transfer($address_to,$amount*pow(10,6));
            $result = [
                'txid'			=> $ret->tx->txID,
                'result'		=> $ret->result
            ];
        }else{
            $result = [
                'txid'	=> "",
                'result'=> ""
            ];
        }
        if($result['result'] == true){
            return [
                "code"=>0,
                "txid"=>$result["txid"],
                'state'	=> 'success',
                'msg'	=> '操作成功'
            ];
        }else{
            return [
                "code"=>1,
                'state'	=> 'error',
                'msg'	=> '操作失败'
            ];
        }
	}

	/*
	 * http://www.coincou.sh/index/trc20/trx.html
	 * 转账 trc20 trx
	 * param: private_key_from
	 * param: address_to
	 * param: amount
	 * return txid,result
	 */
	public function transfer_trx($private_key_from,$address_to,$amount){
		$api = TronApi::mainNet();
		$credential = Credential::fromPrivateKey($private_key_from);
		$kit = new TronKit($api,$credential);
		$address_from = $credential->address()->base58();
		$balance = $kit->getTrxBalance($address_from);
		if($balance){
            $ret = $kit->sendTrx($address_to,$amount*pow(10,6),$address_from);
            $result = [
                'txid'	=> $ret->txid,
                'result'=> $ret->result
            ];
        }else{
            $result = [
                'txid'	=> "",
                'result'=> ""
            ];
        }
		if($result['result'] == true){
			return [
			    "code"=>0,
                "txid"=>$result["txid"],
				'state'	=> 'success',
				'msg'	=> '操作成功'
			];
		}else{
			return [
                "code"=>1,
                "txid"=>"",
				'state'	=> 'error',
				'msg'	=> '操作失败'
			];
		}
	}
}













