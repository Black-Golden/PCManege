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

use app\common\model\Agreement;
use app\common\model\Email;
use app\common\model\Invate;
use app\common\model\Member;
use app\common\model\PlatformMember;
use app\common\model\QuantCash;

use app\common\model\QuantMove;
use app\common\model\QuantWallet;
use app\common\model\TrxLog;
use app\common\model\UsdtLog;
use think\facade\Db;
use app\extend\Trade;
use app\extend\TronTools;
use ccxt\Exchange;
use ccxt\okex5;
use ccxt\binance;
use ccxt\huobipro;
use think\facade\Request;
use think\Filesystem;


/**
 * 会员管理-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class MemberService
 * @package app\common\service
 */


class MemberService extends BaseService
{
    /**
     * 构造函数
     * @author 牧羊人
     * @since 2020/11/15
     * MemberService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Member();
        $this->ga = new \PHPGangsta_GoogleAuthenticator();
    }

    public function bandApi($userId)
    {


        $liset= PlatformMember::where('user_id',$userId)->where('mark',1)->select()->toArray();

        if($liset){
            foreach ($liset as &$v){
                if($v['platform_id'] == 2){
                    $v['platform']='欧易';
                }  if($v['platform_id'] == 3){
                    $v['platform']='币安';
                }
            }
            return $liset;
        }else{
            return ['code'=>1];

        }
    }
    public function addTrx()
    {
        $param = $this->input;
        $validate = getValidate([
            'id' => 'require',
            'private_key' => 'require',
            'trx' => 'require',
        ], [
            'id.require' => '请选择用户',
            'private_key.require' => '请填写私钥地址',
            'trx.require' => '请充值数量',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        //查询TRX够不够
        $tron = new  TronTools();
        $balance = $tron->get_balance($param['private_key']);
        if ($balance["trx"] < $param['trx']) {
            return [
                "code" => 1,
                "msg" => "余额不足"
            ];
        }
        $member = Member::where(["id" => $param["id"]])->find();
        $result = $tron->transfer_trx($param["private_key"], $member["addr_recharge"], $param["trx"]);

        if ($result["code"] == 1) {
            return [
                "code" => 1,
                "msg" => "充值trx异常"
            ];
        }
        $data = [
            "txHash" => $result["txid"],
            'user_id' => $param['id'],
            'address' => $member["addr_recharge"],
            'num' => $param['trx'],
            'status' => 1,
            'create_time' => time()
        ];
        TrxLog::insert($data);
        return true;
    }
 public function resetpass()
    {
        $param = $this->input;
        $validate = getValidate([
            'id' => 'require',
        ], [
            'id.require' => '请选择用户',

        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        Member::where(["id" => $param["id"]])->update(['password'=> md5(sha1(123456))]);

        return true;
    }

    public function pooling()
    {
        $param = $this->input;
        $validate = getValidate([
            'id' => 'require',
            'address' => 'require',

        ], [
            'id.require' => '请选择用户',
            'address.require' => '请填写地址',

        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $member = Member::where(["id" => $param["id"]])->find();
        $tron = new  TronTools();
        $balance = $tron->get_balance($member['private_key']);
        if ($balance["trx"] < 8) {
            return [
                "code" => 1,
                "msg" => "手续费不足，无法归集"
            ];
        }
        $usdt = (int)$balance["usdt"] / pow(10, 6);
        if ($usdt < 1) {
            return [
                "code" => 1,
                "msg" => "usdt不足，无法归集"
            ];
        }
//        return [
//            "code" => 1,
//            "msg" => $balance["usdt"].$param["address"]
//        ];

        $result = $tron->transfer_usdt($member['private_key'], $param["address"], $usdt);
        if ($result["code"] == 1) {
            return [
                "code" => 1,
                "msg" => "归集usdt异常"
            ];
        }
        $data = [
            "txHash" => $result["txid"],
            'user_id' => $param['id'],
            'address' => $param["address"],
            'num' => $usdt,
            'status' => 1,
            'create_time' => time()
        ];
        UsdtLog::insert($data);
        return true;
    }

    //交易所余额
    public function surplus()
    {
        $param = $this->input;
        $validate = getValidate([
            'id' => 'require',
            'platform_id' => 'require',

        ], [
            'id.require' => '请选择用户',
            'platform_id.require' => '请交易所类型',

        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $trade = new Trade();
        $balance = $trade->get_balance([
            "user_id" => $param["id"],
            "platform_id" => $param["platform_id"],
            "platform_type"=>1
        ]);

        $exchange = $this->sign($param["id"], $this->input["platform_id"]);

        if($this->input["platform_id"] ==3){
            $a = $exchange->fetch_balance(["type" => "future"])["USDT"];
            $balance["total"] ="总余额".$balance["total"].",当前余额".$a["total"];
        }



        return [
            "balance" => $balance["total"]
        ];

    }

    public function trx_balance()
    {
        $tron = new  TronTools();
        //$member = $this->model->where(["id" => input("id")])->find();
        $member = Db::table('yrnet_member')->where(["id" => input("id")])->find();
        $balance = $tron->get_balance($member["private_key"]);
        $usdt = (int)$balance["usdt"] / pow(10, 6);
        return [
            "address" => $balance["address"],
            "trx" => $balance["trx"],
            "usdt" => $usdt,
            "balance" => $usdt
        ];

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

        if (isset($param['start_time']) && isset($param['end_time'])) {
            $start = strtotime($param['start_time']);
            $end = strtotime($param['end_time']);
            $map[] = ['reg_time', 'BETWEEN', [$start, $end]];
        }
//        return parent::getList($map); // TODO: Change the autogenerated stub
        $data = parent::getList($map); // TODO: Change the autogenerated stub
        $data = json_decode($data->getContent(), true);
        foreach ($data['data'] as &$key) {
            $key['addr'] = $this->qrcode_usdt($key["id"]);
            $key['addr'] = get_image_url($key['addr']['filePath']);
        }
        return return_json($data);
    }

    /**
     * 添加或编辑
     * @return array
     * @since 2020/11/21
     * @author 牧羊人
     */
    public function edit()
    {
        // 请求参数
        $data = request()->param();
        // 头像处理
        $avatar = getter($data, 'avatar');

        if (strpos($avatar, "temp")) {
            $data['headimg'] = save_image($avatar, 'member');
        } else {
            $data['headimg'] = str_replace(IMG_URL, "", $data['headimg']);
        }

        $userInfo = $this->getInfo($data["id"]);


        if ($userInfo["rank"] != $data["rank"]) {
            $data["is_auto_rank"] = 1;
        }
        if ($data["rank"] == 0) {
            $data["is_auto_rank"] = 0;
        }


        // 城市数据处理
        $city = $data['city'];
        if (!empty($city)) {
            $data['province_code'] = $city[0];
            $data['city_code'] = $city[1];
            $data['district_code'] = $city[2];
        } else {
            $data['province_code'] = 0;
            $data['city_code'] = 0;
            $data['district_code'] = 0;
        }
        unset($data['city']);
        return parent::edit($data); // TODO: Change the autogenerated stub
    }

    public function getInfo($userId)
    {
        $info = $this->model->where(["id" => $userId])->field("id,username,tradepassword,rank,headimg,recode,addr_cash,addr_recharge,qb_usdt,qb_profit_all,qb_contract,nickname,agreement,bond,email")->find();
        $info["headimg"] = return_url($info["headimg"]);
        if ($info["addr_recharge"]) {
            $info["addr_recharge_qrcode"] = QRcarcodepng($info['addr_recharge']);
        }
        if (!$info['nickname']) {
            $info['nickname'] = '暂无昵称';
        }
        $info['set_trade'] = 0;
        if ($info['tradepassword']) {
            $info['set_trade'] = 1;
        }
        unset($info['tradepassword']);
        //用户是否设置过API
        $info['is_set_api'] = 0;
        $info['platform_id'] = 0;
        $platform = PlatformMember::where('user_id', $userId)->where('mark', 1)->find();
        if ($platform) {
            $info['platform_id'] = $platform['platform_id'];
            $info['is_set_api'] = 1;
        }

        $info['point'] = get_config('point', 1);
        $info['qb_usdt'] = sprintf("%.2f", $info['qb_usdt']);
        $info['bond'] = sprintf("%.2f", $info['bond']);
        $info['today'] = date("Y-m-d");
        $service = new TeamService();
        $team = $service->team($userId);
        $info['team'] = isset($team[0]) ? count($team[0]) : 0;
        return $info;
    }


    public function qrcode($commend, $userId)
    {
        $res = $this->$commend($userId);

        $outfile = str_replace("\\", "/", $res['outfile']);
//        var_dump($res);die;
        $data = $res['data'];
        if (!file_exists($outfile)) {
            //vendor("phpqrcode.phpqrcode
            $QRcode = new QRcode();

//            $level = 'L';
//            $size = 20;
//            ob_start();

            $QRcode->png($data, $outfile);
        }
        return Request::instance()->domain() . $res['filePath'];
//        return return_json([
//            "url" => Request::instance()->domain() . $res['filePath']
//        ]);
    }

    private function qrcode_usdt($userId)
    {
        $addr_recharge = Db::name('member')
            ->where('id', $userId)
            ->value('addr_recharge');
        $fileName = "public\\uploads\\usdt\\{$addr_recharge}.png";
        $filePath = "/uploads/usdt/{$addr_recharge}.png";
        $outfile = root_path() . $fileName;
        $outfile = str_replace("/", "\\", $outfile);

        $result = [
            'data' => $addr_recharge,
            'outfile' => $outfile,
            'filePath' => $filePath
        ];
        return $result;
    }

    private function qrcode_recommend($recode)
    {
        // $data = Request::instance()->domain() . '/h5/#/pages/user/register?recode=' . $recode;

        $data = MAIN_URL . '/h5/page/index.html?recode=' . $recode;
        $fileName = "public\\uploads\\qrcode\\" . $recode . '.png';
        $filePath = "/uploads/qrcode/" . $recode . '.png';
        $outfile = root_path() . $fileName;
        $outfile = str_replace("/", "\\", $outfile);
        $result = [
            'data' => $data,
            'outfile' => $outfile,
            'filePath' => $filePath
        ];
        return $result;
    }

    //修改头像
    public function headimg($userId)
    {
        // 错误提示语
        $error = "";
        // 上传图片
        $result = upload_image('headimg', '', $error);
        if (!$result) {
            return message($error, false);
        }
        // 多图片上传处理
        $list = [];
        if (is_array($result)) {
            foreach ($result as $val) {
                $list['show_img'][] = IMG_URL . $val;
                $list['upload_img'][] = $val;
            }
        } else {

            $data = ['headimg' =>  '/uploads/' . $result];

            $data =  Member::where('id', $userId)->update($data);
            return [
                "code" => 0,
                "msg" => "修改成功"
            ];
        }
//        //上传图片
//        $files = request()->file('headimg');
//        if ($files) {
//            if ($_FILES['headimg']['size'] > 2 * 1024 * 1024) {
//                return [
//                    "code" => 1,
//                    "msg" => "头像文件太大，请上传2M以内文头像"
//                ];
//            }
//            $savename = Filesystem::disk('public')->putFile('headimg', $files);
//            $savename = str_replace("\\", "/", $savename);
//            //入库
//            $data = ['headimg' => '/uploads/' . $savename];
//            $a = $this->model->where('id', $userId)->update($data);
//
//            return [
//                "code" => 0,
//                "msg" => "修改成功"
//            ];
//        }
        return [
            "code" => 1,
            "msg" => "请上传头像"
        ];

    }

    //修改昵称
    public function nickname($userId)
    {
        // 验证器 验证
        $validate = getValidate([
            'nickname' => 'require',
        ], [
            'nickname.require' => '请输入昵称',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }

        $data = ['nickname' => $this->input['nickname']];
        $this->model->where('id', $userId)->update($data);
        return [
            "code" => 0,
            "msg" => "修改成功"
        ];

    }

    public function set_password($userId)
    {

        $validate = getValidate([
            'verif' => 'require',
            'new_password' => 'require',
            're_password' => 'require',
        ], [

            'verif.require' => '请输入验证码',
            're_password.require' => '请确认新密码',
            'new_password.require' => '请输入新密码',

        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        if ($this->input['new_password'] != $this->input['re_password']) {
            $result = [
                'code' => 1,
                'msg' => '两次密码不同'
            ];
            return $result;
        }
        $info = Member::where('id', $userId)->find();
        if (md5(sha1($this->input['new_password'])) == $info['password']) {
            $result = [
                'code' => 1,
                'msg' => '新密码与旧密码相同'
            ];
            return $result;
        }
        $code = Email::where('email',$info['email'])->where('endtime',"<",time())->where('used',0)->order('id DESC')->find();
        if(!$code){
            $result = [
                'code' => 1,
                'msg' => '验证码失效'
            ];
            return $result;
        }
//        $checkResult = $this->ga->verifyCode($info['google_key'], $this->input['verif'], 1);



        if ($this->input['verif'] == $code['code'] ) {
            Member::where('id', $userId)->update(['password' => md5(sha1($this->input['new_password']))]);
            Email::where('id',$code['id'])->update(['used'=>1]);
            return [
                'code' => 0,
                'msg' => '修改成功',
            ];
        } else {
            return [
                'code' => 1,
                'msg' => '修改失败,谷歌密码错误',
            ];
        }
    }

    public function set_tradepassword($userId)
    {

        $validate = getValidate([
            'verif' => 'require',
            'new_password' => 'require',
            're_password' => 'require',
        ], [
            'verif.require' => '请输入验证码',
            'new_password.require' => '请输入新密码',
            're_password.require' => '请确认新密码',

        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        if ($this->input['new_password'] != $this->input['re_password']) {
            $result = [
                'code' => 1,
                'msg' => '两次密码不同'
            ];
            return $result;
        }
        // old password
        $data_member = Member::where('id', $userId)->find();
        if (md5(sha1($this->input['new_password'])) == $data_member['tradepassword']) {
            $result = [
                'code' => 1,
                'msg' => '新密码与旧密码相同'
            ];
            return $result;
        }
        $code = Email::where('email',$data_member['email'])->where('endtime',"<",time())->where('used',0)->order('id DESC')->find();
        if(!$code){
            $result = [
                'code' => 1,
                'msg' => '验证码失效'
            ];
            return $result;
        }
//        $checkResult = $this->ga->verifyCode($data_member['google_key'], $this->input['verif'], 1);

        if ($this->input['verif'] == $code['code']) {
            Member::where('id', $userId)->update(['tradepassword' => md5(sha1($this->input['new_password']))]);
            Email::where('id',$code['id'])->update(['used'=>1]);
            return $result = [
                'code' => 0,
                'msg' => '修改成功',
            ];
        } else {
            return $result = [
                'code' => 1,
                'msg' => '修改失败',
            ];
        }

    }

    //获取谷歌二维码
    public function googleCode($id)
    {

        $info = $this->model->where('id', $id)->field('username,id,google_key')->find();
        $qrCodeUrl = $this->ga->getQRCodeGoogleUrl($info['username'], $info['google_key']);
        $result = [
            'code' => 0,
            'msg' => '获取成功',
            'data' => ['google_key' => $info['google_key'], 'qrCodeUrl' => $qrCodeUrl,]
        ];
        return $result;
    }

    //获取谷歌二维码
    public function isGoogle($id)
    {

        $info = $this->model->where('id', $id)->field('is_google,id')->find();
//        $qrCodeUrl = $this ->ga->getQRCodeGoogleUrl($info['username'], $info['google_key']);
        $result = [
            'code' => 0,
            'msg' => '获取成功',
            'data' => $info
        ];
        return $result;
    }

    //绑定谷歌验证
    public function bindGoogle($id)
    {
        $param = $this->input;
        $validate = getValidate([
            'verif' => 'require',
//            'tradepassword' => 'require',

        ], [
            'verif.require' => '请输入谷歌验证码',
//            'tradepassword.require' => '请输入交易密码',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
//
        $info = $this->model->where('id', $id)->field('tradepassword,username,google_key')->find();
//        if (md5(sha1($this->input['tradepassword'])) != $info['tradepassword']) {
//            return $result = [
//                'code' => 1,
//                'msg' => '交易密码错误',
//            ];
//        }
        $checkResult = $this->ga->verifyCode($info['google_key'], $param['verif'], 1);

        if ($checkResult) {
            $this->model->where('id', $id)->update(['is_google' => 1]);
            return $result = [
                'code' => 0,
                'msg' => '验证成功',
            ];
        } else {
            return $result = [
                'code' => 1,
                'msg' => '验证失败,谷歌密码错误',
            ];
        }


    }


//服务协议书
    public function agreement($id)
    {
        $data=[];
        $param = $this->input;
        $validate = getValidate([
            'email' => 'require|email',
            'platform_id' => 'require',
            'setup_id' => 'require',
            'uid' => 'require',
            'key' => 'require',
            'secret' => 'require',
            'qb_usdt' => 'require',
            'in_usdt' => 'require',
            'bondaddr' => 'require',
            'bond' => 'require|float',

//
//            'minbond' => 'require|float',
//
////            'sassets' => 'require',
//            'stop_loss' => 'require|float',
//            'hassets' => 'require|float',
//            'bassets' => 'require|float',
//            'assets' => 'require|float',

        ], [
            'email.require' => '请输入甲方邮箱',
            'platform_id.require' => '请输入交易所',
            'setup_id.require' => '请输入策略',
            'uid.require' => '请输入uid',
            'email.email' => '请输入正确的邮箱',
            'key.require' => '请输入ApiKEY',
            'secret.require' => '请输入Api秘钥',
            'qb_usdt.require' => '请输入余额',
            'qb_usdt.float' => '请输入余额',
            'in_usdt.require' => '请输入参与金额',
            'in_usdt.float' => '请输入参与金额',
            'bondaddr.require' => '请输入保证金地址',
            'bond.require' => '请输入保证金',
            'bond.float' => '请输入正确的保证金',



//            'minbond.require' => '请输入最低保证金',
//            'minbond.float' => '请输入正确的最低保证金',
//
//
//            'stop_loss.require' => '请输入初始止损线',
//            'stop_loss.float' => '请输入正确的初始止损线',
//            'hassets.require' => '请输入初始合约资产',
//            'hassets.float' => '请输入正确的初始合约资产',
//            'bassets.require' => '请输入初始币币资产',
//            'bassets.float' => '请输入正确的初始币币资产',
//            'assets.require' => '请输入初始初期资产',
//            'assets.float' => '请输入初正确的始初期资产',


        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }

        $param['user_id'] =  $id;
        if($param['passphrase']){
            $data['platform_id'] = 2;
            $data['key'] =     $param['key'];
            $data['secret'] =  $param['secret'];
            $data['passphrase'] =  $param['passphrase'];
            $data['user_id'] =  $id;
            unset($param['key']);
            unset($param['secret']);
            unset($param['passphrase']);
        }else{
            $data['platform_id'] = 3;
            $data['key'] =     $param['key'];
            $data['secret'] =  $param['secret'];
            $data['user_id'] =  $id;
            unset($param['key']);
            unset($param['secret']);
            unset($param['passphrase']);
        }

unset($param['platform_id']);
        $plat = PlatformMember::where('user_id',$id)->where('platform_id',$data['platform_id'])->find();
        if($plat){
            unset( $data['user_id']);
            PlatformMember::where('user_id',$id)->update($data);
        }else{
            PlatformMember::insert($data);
        }
        $i = Agreement::insert($param);
        if($i){
            Member::where('id',$id)->update(['agreement'=>1]);
            return true;
        }else{
            return ['code'=>1,'msg'=>'操作失败'];
        }
    }


    public function register()
    {

        $validate = getValidate([
//            'username' => 'require',
            'email' => 'require',
            'password' => 'require',
//            'tradepassword' => 'require',
//            'recode' => 'require',
            'code' => 'require',

        ], [
//            'username.require' => '请输入用户名',
            'email.require' => '请输入邮箱',
            'password.require' => '请输入密码',
//            'tradepassword.require' => '请输入交易密码',
//            'recode.require' => '请输入邀请码',
            'code.require' => '请输入邮箱验证码',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
//        if (!ctype_alnum($this->input["username"])) {
//            $result = [
//                'code' => 1,
//                'msg' => '用户名只能有数字和字母组成',
//            ];
//            return $result;
//        }
//        $data_member = Member::where('username', $this->input["username"])->find();
//        if ($data_member) {
//            $result = [
//                'code' => 1,
//                'msg' => '用户名已存在',
//            ];
//            return $result;
//        }
        $data_member = Member::where('email', $this->input["email"])->find();
        if ($data_member) {
            $result = [
                'code' => 1,
                'msg' => '邮箱已被注册',
            ];
            return $result;
        }
        $data_member["id"] = 0;
        if (isset($this->input["recode"]) && $this->input["recode"] != "" && $this->input["recode"] != "supermember1") {
            $data_member = Member::where('recode', $this->input["recode"])->find();
            if (!$data_member) {
                $result = [
                    'code' => 1,
                    'msg' => '邀请人不存在',
                ];
                return $result;
            }
        }

        $code=Email::where('email', $this->input["email"])->where('used',0)->where('endtime','>',time())->find();
        if(!$code){
            $result = [
                'code' => 1,
                'msg' => '验证码已过期',
            ];
            return $result;
        }
        if($code['code']!= $this->input["code"]){
            $result = [
                'code' => 1,
                'msg' => '邮箱验证码错误',
            ];
            return $result;
        }
        Email::where('id',$code['id'])->update(['used'=>1]);
        // 验证验证码是否正确 先添加上万能验证码
//
        // 创建新的"安全密匙secret"
        $secret = $this->ga->createSecret();
        $tts = new TronTools();
        $res = $tts->create();
        $data = [
            'username' =>$this->input['email'],
            'password' => md5(sha1($this->input['password'])),
//            'tradepassword' => md5(sha1($this->input['tradepassword'])),
            'google_key' => $secret,
            'rank' => 0,
            'recode' => $this->recode(),
            'headimg' => '/images/person.png',
            'reg_time' => time(),
            'addr_cash' => null,
            'addr_recharge' => $res['address'],
            'private_key' => $res['private_key'],
            'qb_usdt' => 0,
            'email' =>  $this->input['email'],
            "referee_id" => $data_member["id"]
        ];

        $user_id = Member::insertGetId($data);
//        $invate = new Invate();
//        $invate->relation([
//            "user_id" => $user_id,
//            "parent_id" => $data_member["id"]
//        ]);
        // JWT生成token
        $jwt = new \Jwt();
        $jwt::$key = "show_index";
        $token = $jwt->getToken($user_id);
        $result = [
            'code' => 0,
            'msg' => '注册成功',
            'data' => [
                'token' => $token
            ]
        ];
        return $result;
    }

    public function recode()
    {
        $recode = get_reccode();
        $list = Member::where('recode', $recode)->find();
        if ($list) {
            return $this->recode();
        }
        return $recode;
    }

    public function login()
    {
        $param = $this->input;
        // 验证器 验证
        $validate = getValidate([
            'password' => 'require',
            'email' => 'require',
        ], [
            'password.require' => '请输入密码',
            'email.require' => '请输入邮箱',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $member = $this->model
            ->field('id,password')
            ->where('email', $param['email'])
            ->where('mark', 1)
            ->find();

        if( md5(sha1($this->input['password'])) != $member['password']){
            return  $result = [
                'code' => 1,
                'msg' => '密码错误',
            ];
        }
        $mobile = $this->model
            ->where('email', $param['email'])
            ->where('mark', 1)
            ->find();
        if (!$mobile) {
            return  $result = [
                'code' => 1,
                'msg' => '该账号尚未注册 请先注册',
            ];
        }

        if ($mobile['status'] != 1) {
            return [
                'code' => 1,
                'msg' => '该登录账号已冻结',
            ];
        }

        if ($member) {
            // JWT生成token
            $jwt = new \Jwt();
            $jwt::$key = "show_index";
            $token = $jwt->getToken($member['id']);
            $this->model->where('id', $member['id'])->inc('login_count')->update(['login_time' => time(), 'login_ip' => Request::ip(), "token" => $token]);
            $result = [
                'code' => 0,
                'msg' => '登录成功',
                'data' => [
                    'token' => $token
                ]
            ];
        } else {
            $result = [
                'code' => 1,
                'msg' => '登录失败',
            ];
        }
        return $result;
    }

    public function google_code()
    {
        $param = $this->input;
        // 验证器 验证
        $validate = getValidate([
            'email' => 'require',
            'verif' => 'require',
            'code' => 'require',
        ], [
            'email.require' => '请输入用户名',
            'verif.require' => '请输入谷歌验证码',
            'code.require' => '请输入谷歌验证码',

        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $data_user = $this->model
            ->field('id,email,google_key')
            ->where('email', $param['email'])
            ->where('mark', 1)
            ->find();
        if (!$data_user) {
            return $result = [
                'code' => 1,
                'msg' => '该账号尚未注册 请先注册',
            ];
        }


        $code=Email::where('email', $this->input["email"])->where('used',0)->where('endtime','>',time())->find();
        if(!$code){
            $result = [
                'code' => 1,
                'msg' => '验证码已过期',
            ];
            return $result;
        }
        if($code['code']!= $this->input["code"]){
            $result = [
                'code' => 1,
                'msg' => '邮箱验证码错误',
            ];
            return $result;
        }
        Email::where('id',$code['id'])->update(['used'=>1]);
        $checkResult = $this->ga->verifyCode($data_user['google_key'], $param['verif'], 1);



        if ($checkResult) {
            return $result = [
                'code' => 0,
                'msg' => '验证成功',
            ];
        } else {
            return $result = [
                'code' => 1,
                'msg' => '验证失败',
            ];
        }


    }

    public function forget()
    {
        $param = $this->input;
        // 验证器 验证
        $validate = getValidate([
            'email' => 'require',
            'password' => 'require',
            're_password' => 'require',

        ], [
            'email.require' => '请输入邮箱',
            're_password.require' => '请输入确认密码',
            'password.require' => '请输入密码',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        if ($this->input['password'] != $this->input['re_password']) {
            $result = [
                'code' => 1,
                'msg' => '两次密码不同'
            ];
            return $result;
        }

        $data_member = Member::where('email', $this->input["email"])->find();
        if (!$data_member) {
            $result = [
                'code' => 1,
                'msg' => '邮箱不存在',
            ];
            return $result;
        }

        $data = [
            'password' => md5(sha1($this->input['password']))
        ];
        Member::where('id', $data_member["id"])->update($data);
        $result = [
            'code' => 0,
            'msg' => '密码重置成功',
        ];
        return $result;
    }

    //充值
    public function recharge()
    {
        $validate = getValidate([
            'id' => 'require',
            'amount' => 'require|number|min:1',
        ], [
            'amount.require' => '充值金额不能为空',
            'id.require' => '请选择用户',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }

        $member = $this->model->where('id', $this->input['id'])
            ->find();
//        var_dump($member);die;
        if (strlen($member['addr_recharge']) != 34) {
            return [
                "code" => 1,
                "msg" => "该用户充值地址无效"
            ];
        }
        $data = [
            'user_id' => $this->input['id'],
            'qb_usdt' => $this->input['amount'],
            'usdt_memo' => '通过后台充值' . $this->input['amount'] . 'USDT',
            'usdt_type_id' => 3,
        ];
        $data = Member::setUsdt($data);

//        $data = operate($data, $type = 0);
//        QuantWallet::create($data);
        if ($data) {
            return [
                "code" => 0,
                "msg" => "充值成功"
            ];
        }
        return [
            "code" => 1,
            "msg" => "充值失败"
        ];

    }

    //提现
    //
    public function cash($userId)
    {
        // 验证器 验证
        // check error
        // erc20：42
        // trc20：34
        $validate = getValidate([
            'addr_cash' => 'require|length:34',
            'amount' => 'require|min:1',
            'tradepassword' => 'require',
            'code' => 'require',
        ], [
            'addr_cash.require' => '请输入提现地址',
            'addr_cash.length' => '提现地址不符合长度',
            'amount.require' => '提现金额不能为空',
            'amount.min' => '提现金额最小为1',
            'tradepassword.require' => '交易密码不能为空',
            'code.require' => '验证码不能为空',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }


        $member = $this->model->where('id', $userId)
            ->find();

        if (!$member['tradepassword']) {
            return [
                "code" => 2,
                "msg" => "未设置交易密码，请先设置交易密码再进行提款"
            ];
        }

        if (strlen($this->input['addr_cash']) != 34) {
            return [
                "code" => 1,
                "msg" => "提币地址无效"
            ];
        }


        if ($member['qb_usdt'] + get_config('cash_fee', 1) < $this->input['amount']) {
            return [
                "code" => 1,
                "msg" => "提币余额不足"
            ];
        }
        $code = Email::where('email',$member['email'])->where('endtime',"<",time())->where('used',0)->order('id DESC')->find();
        if(!$code){
            $result = [
                'code' => 1,
                'msg' => '验证码失效'
            ];
            return $result;
        }

        if ($this->input['code'] != $code['code']) {
            $result = [
                'code' => 1,
                'msg' => '验证码错误'
            ];
            return $result;
        }

//        $checkResult = $this->ga->verifyCode($member['google_key'], $this->input['code'], 1);

//        if (!$checkResult) {
//
//            return [
//                'code' => 1,
//                'msg' => '谷歌验证失败',
//            ];
//        }
        if (md5(sha1($this->input['tradepassword'])) != $member['tradepassword']) {
            $result = [
                'code' => 1,
                'msg' => '交易密码错误'
            ];
            return $result;
        }

        $this->model->startTrans();
        try {
            $usdt = $this->input['amount'] + get_config('cash_fee', 1);
            $param = [
                'user_id' => $userId,
                "qb_usdt" => -$usdt,
                "usdt_memo" => "提现手续费和余额共计" . $usdt . 'USDT',
                "usdt_type_id" => 2,
                "fee" => get_config('cash_fee', 1),
                "addr_cash" => $this->input['addr_cash'],

            ];
            Member::setUsdt($param);
            if ($member['addr_cash'] != $this->input['addr_cash']) {
                $this->model->where(["id" => $userId])->update(['addr_cash' => $this->input['addr_cash']]);
            }

            $this->model->commit();
        } catch (\Exception $e) {
            // 事务回滚
            $this->model->rollback();
            return [
                'code' => 1,
                'msg' => $e->getMessage()
            ];
        }
        // success
        $result = [
            'msg' => '提现成功',
        ];
        return $result;
    }

    public function move($userId)
    {

        $validate = getValidate([
            'username' => 'require',
            'amount' => 'require|min:1',
            'tradepassword' => 'require',
            'code' => 'require',
        ], [
            'username.require' => '请输入转账用户',
            'addr_cash.length' => '转账地址不符合长度',
            'amount.require' => '转账金额不能为空',
            'amount.min' => '转账金额最小为1',
            'tradepassword.require' => '交易密码不能为空',
            'code.require' => '验证码不能为空',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $member = $this->model->where('id', $userId)
            ->find();

        $to_member = $this->model->where('username', $this->input["username"])
            ->find();
        if (!$to_member) {
            return [
                "code" => 1,
                "msg" => "用户不存在"
            ];
        }

        $move_amount = $this->input['amount'] + get_config('move_fee', 1);

        if ($member['qb_usdt'] < $move_amount) {
            return [
                "code" => 1,
                "msg" => "转账余额不足"
            ];
        }
        if (md5(sha1($this->input['tradepassword'])) != $member['tradepassword']) {
            $result = [
                'code' => 1,
                'msg' => '交易密码错误'
            ];
            return $result;
        }

//        $checkResult = $this->ga->verifyCode($member['google_key'], $this->input['code'], 1);
//
//        if (!$checkResult) {
//
//            return [
//                'code' => 1,
//                'msg' => '谷歌验证失败',
//            ];
//        }

        $code = Email::where('email',$member['email'])->where('endtime',"<",time())->where('used',0)->order('id DESC')->find();
        if(!$code){
            $result = [
                'code' => 1,
                'msg' => '验证码失效'
            ];
            return $result;
        }
        if ($this->input['code'] != $code['code']) {
            $result = [
                'code' => 1,
                'msg' => '验证码错误'
            ];
            return $result;
        }
        $this->model->startTrans();
        try {
            $data = [
                "user_id" => $userId, //用户id
                "qb_usdt" => -$move_amount, //交易金额
                "usdt_memo" => $member['username'] . '给' . $to_member['username'] . '转' . $this->input['amount'] . 'USDT',
                "usdt_type_id" => 7 //充值U的时候  如果是提现的话必须选上(手续费和提现地址)
            ];

            Member::setUsdt($data);
            $this->model->where(["id" => $to_member["id"]])->inc("qb_usdt", $this->input['amount'])->update();

            $data = [
                'user_id_from' => $userId,
                'user_id_to' => $to_member['id'],
//                'wallet' => "qb_usdt",
                'amount' => $this->input['amount'],
                'fee' => get_config('move_fee', 1),
                'score_begin' => $member["qb_usdt"],
                'score_end' => $member["qb_usdt"] - $move_amount,

            ];

            QuantMove::create($data);
//            $data = [
//                'user_id' => $userId,
//                'wallet' => 'qb_usdt',
//                'num' => $this->input['amount'],
//                'last_num' => $to_member['qb_usdt'],
//                'type_id' => 7,
//                'memo' =>  $member['username'] .'给' . $to_member['username'] . '转' . $this->input['amount'] . 'USDT',
//            ];
////            $data = operate($data, $type = 1);
//            QuantWallet::create($data);
            $data = [
                'user_id' => $to_member['id'],
                'wallet' => 'qb_usdt',
                'num' => $this->input['amount'],
                'payment_id' => 2,
                'last_num' => $to_member['qb_usdt'],
                'type_id' => 7,
                'memo' => $member['username'] . '给' . $to_member['username'] . '转' . $this->input['amount'] . 'USDT',
            ];

            QuantWallet::create($data);

            $this->model->commit();
        } catch (\Exception $e) {
            // 事务回滚
            $this->model->rollback();
            return [
                'code' => 1,
                'msg' => $e->getMessage()
            ];
        }
        // success
        $result = [
            'msg' => '转账成功',
        ];
        return $result;
    }

    public function balance($userId)
    {
        $validate = getValidate([
            'platform_id' => 'require',
            //'platform_type' => 'require',
        ], [
            'platform_id.require' => '请选择平台',
            //'platform_type.require' => '请选择平台',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $arr = [
            "free" => "0",
            "used" => "0",
            "total" => "100",
        ];
        $exchange = $this->sign($userId, $this->input["platform_id"]);

        // if

        //$a= $exchange->fetch_balance([ "type" => "future"]);


        if ($exchange) {
            try {
                if ($this->input["platform_id"] == 2) {

                    $usdt = $exchange->fetch_balance()["USDT"];
                } else if ($this->input["platform_id"] == 1) {

                    $usdt = $exchange->fetch_balance()["USDT"];
                } else {
                    $usdt = $exchange->fetch_balance(["type" => "future"])["USDT"];
                }
                cache($userId . "_" . $this->input["platform_id"] . "_balance", $usdt, 60);
                return [
                    "free" => "0",
                    "used" => "0",
                    "total" => $usdt,
                ];
            } catch (\Exception $e) {
                return [
                    "free" => "0",
                    "used" => "0",
                    "total" => "100",
                ];
            }
        }
        return $arr;

//        var_dump($exchange);die;

    }

    //返回授权信息
    public function sign($userId, $platform_id)
    {
        $platform_member_info = PlatformMember::where(["platform_id" => $platform_id, "user_id" => $userId])->find();
        if ($platform_member_info) {
            if ($platform_member_info["platform_id"] == 2) {
                $exchange = new okex5();
                $exchange->password = $platform_member_info["passphrase"];

                if (env("x-simulated-trading") == 1) {
                    $exchange->headers = [
                        "x-simulated-trading" => "1"
                    ];
                }
            } else if ($platform_member_info["platform_id"] == 3) {
                $exchange = new binance();
            }
            $exchange->apiKey = $platform_member_info["key"];
            $exchange->secret = $platform_member_info["secret"];
            return $exchange;
        } else {
            return false;
        }
    }


    //修改api key 和secret
    public function auth($userId)
    {

        $validate = getValidate([
            'platform_id' => 'require',
            'key' => 'require',
            'secret' => 'require',
        ], [
            'platform_id.require' => '请选择平台',
            'key.require' => '请选择key',
//            'key.min' => 'key不能少于32个字符',
//            'key.max' => 'key不能超过64个字符',
//            'secret.min' => 'secret不能少于32个字符',
//            'secret.max' => 'secret不能超过64个字符',

            'secret.require' => '请选择私钥',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $passphrase = "";
        if ($this->input["platform_id"] == 2) {
            $validate = getValidate([
                'passphrase' => 'require',
            ], [
                'passphrase.require' => '请选择私钥',
            ]);
            if ($validate['code'] == 1) {
                return $validate;
            }
            $passphrase = $this->input["passphrase"];
        }
        $platform_member_info = PlatformMember::where(["platform_id" => $this->input["platform_id"], "user_id" => $userId])->find();
        if (!$platform_member_info) {
            $data = [
                "platform_id" => $this->input["platform_id"],
                "user_id" => $userId,
                "key" => $this->input["key"],
                "secret" => $this->input["secret"],
                "passphrase" => $passphrase
            ];
            PlatformMember::create($data);
        } else {
            $data = [
                "key" => $this->input["key"],
                "secret" => $this->input["secret"],
                "passphrase" => $passphrase
            ];
            PlatformMember::where(["platform_id" => $this->input["platform_id"], "user_id" => $userId])->update($data);
        }
        $trade = new Trade();
        $trade->set_position_mode([
            "user_id" => $userId,
            "platform_id" => $this->input["platform_id"]
        ]);
//        if (!$results) {
//            PlatformMember::where(["platform_id" => $this->input["platform_id"], "user_id" => $userId])->delete();
//            return [
//                "code" => 1,
//                "msg" => "api信息错误"
//            ];
//        }
//        $this->input['user_id'] = $userId;
//        $trade = new Trade();
//        $trade->set_position_mode($this->input);
        return true;
    }


        public function ranking()
    {
        !
        $type = $this->input['type'];
        //按照合约排序
        if ($type == 1) {
            $order = 'qb_profit_all DESC';
        }//按照现货排序
        elseif ($type == 2) {
            $order = 'qb_child_all DESC';
        } //按照现货排序
        elseif ($type == 3) {
            $order = 'qb_team_all DESC';
        } else {
            $order = 'qb_profit_all DESC';
        }

        $list = $this->model
            ->field('username,headimg,qb_profit_all,qb_child_all,qb_profit_all')->order($order)->where(["mark" => 1])->limit(100)
            ->select();
        foreach ($list as $key => &$val) {
//            var_dump($val["qb_all"]);            $val["username"] = substr_cut($val["username"]);
            $val["headimg"] = return_url($val["headimg"]);

        }
        return $list;
    }


    public function vip($userId)
    {
        $info = Member::where('id', $userId)->where('mark', 1)->find();
        if ($info['rank'] > 0) {
            return [
                'code' => 1,
                'msg' => '您已经是会员了'
            ];
        }
        if ($info['qb_usdt_point'] >= get_config('point', 1)) {
            Member::where('id', $userId)->update(['rank' => 1]);
            return [
                'code' => 0,
                'msg' => '开通成功'
            ];
        }
        return [
            'code' => 2,
            'msg' => '点卡余额不足，请充值'
        ];
    }

    public function platform($userId)
    {
        $validate = getValidate([
            'platform_id' => 'require',
        ], [
            'platform_id.require' => '请选择平台',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $list = PlatformMember::where('user_id', $userId)->where('platform_id', $this->input['platform_id'])->find();
        if (!$list) {
            return [
                'code' => 1,
                'msg' => '请先配置API'
            ];
        }
        if ($this->input['platform_id'] == 2) {
            if (!$list['passphrase']) {
                return [
                    'code' => 1,
                    'msg' => '请先配置API'
                ];
            }
        }
        return true;
    }
}
