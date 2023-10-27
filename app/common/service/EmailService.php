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
class EmailService extends BaseService
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

    public function setemail(){

        $input  = $this->input;

        header("content-type:text/html;charset=utf-8"); //设置编码
        $key="sjvquwdkgynhdhia";
        $mail = new PHPMailer();

        (string)$code  = $this->getcode();
// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
//        $mail->SMTPDebug = 1;
// 使用smtp鉴权方式发送邮件
        $mail->isSMTP();
// smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = true;
// 链接qq域名邮箱的服务器地址
        $mail->Host = 'smtp.qq.com';
// 设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
// 设置ssl连接smtp服务器的远程服务器端口号
        $mail->Port = 465;
// 设置发送的邮件的编码
        $mail->CharSet = 'UTF-8';
// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = '你好';
// smtp登录的账号 QQ邮箱即可
        $mail->Username = '2468845779@qq.com';
// smtp登录的密码 使用生成的授权码
        $mail->Password = $key;
// 设置发件人邮箱地址 同登录账号
        $mail->From = '2468845779@qq.com';
// 邮件正文是否为html编码 注意此处是一个方法
        $mail->isHTML(true);
// 设置收件人邮箱地址
        $mail->addAddress($input['email']);

// 添加该邮件的主题
        $mail->Subject = '测试邮件';
// 添加邮件正文
        $mail->Body = '您好，您的验证码为:'.$code;
// 为该邮件添加附件
//$mail->addAttachment('./example.pdf');
// 发送邮件 返回状态
        $status = $mail->send();

        Email::insert(['email'=>$input['email'],'code'=>$code,'endtime'=>time()+5*60]);

        if(!$status){
            return ['code'=>1];
        }
        return true;
    }

    //生成随机数验证码
    function getcode(){
        $code=rand(100000,999999);
        return (string)$code;
    }
    public function email($userId){

        $email = Member::where('id',$userId)->value('email');
        if(!$email){
            return ['code'=>1,'msg'=>'获取验证码失败'];
        }

        header("content-type:text/html;charset=utf-8"); //设置编码
        $key="sjvquwdkgynhdhia";
        $mail = new PHPMailer();

        (string)$code  = $this->getcode();
// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
//        $mail->SMTPDebug = 1;
// 使用smtp鉴权方式发送邮件
        $mail->isSMTP();
// smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = true;
// 链接qq域名邮箱的服务器地址
        $mail->Host = 'smtp.qq.com';
// 设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
// 设置ssl连接smtp服务器的远程服务器端口号
        $mail->Port = 465;
// 设置发送的邮件的编码
        $mail->CharSet = 'UTF-8';
// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = '你好';
// smtp登录的账号 QQ邮箱即可
        $mail->Username = '2468845779@qq.com';
// smtp登录的密码 使用生成的授权码
        $mail->Password = $key;
// 设置发件人邮箱地址 同登录账号
        $mail->From = '2468845779@qq.com';
// 邮件正文是否为html编码 注意此处是一个方法
        $mail->isHTML(true);
// 设置收件人邮箱地址
        $mail->addAddress($email);

// 添加该邮件的主题
        $mail->Subject = '验证邮件';
// 添加邮件正文
        $mail->Body = '您好，您的验证码为:'.$code;
// 为该邮件添加附件
//$mail->addAttachment('./example.pdf');
// 发送邮件 返回状态
        $status = $mail->send();

        Email::insert(['email'=>$email,'code'=>$code,'endtime'=>time()+5*60]);

        if(!$status){
            return ['code'=>1];
        }

        return true;
    }

}
