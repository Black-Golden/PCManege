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

namespace app\adminapi\controller;

// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

/**
 * 上传文件
 * @author 牧羊人
 * @since 2020/11/14
 * Class Upload
 * @package app\adminapi\controller
 */
class Upload extends Backend
{
    /**
     * 上传图片（支持多图片上传）
     * 备注：1、单文件：file
     *      2、多文件：file[],file[]
     *
     * @author 牧羊人
     * @since 2020-04-21
     */
    public function uploadImage()
    {
        // 错误提示语
        $error = "";
        // 上传图片
        $result = upload_image('file', '', $error);
        if (!$result) {
            return message($error, false);
        }
        // 多图片上传处理
        $list = [];
        if (is_array($result)) {
            foreach ($result as $val) {
                $list[] = IMG_URL . $val;
            }
        } else {
            $list = IMG_URL . $result;
        }
        return message("上传成功", true, $list);
    }

    /**
     * 上传文件(支持多文件上传)
     * 备注：1、单文件：file
     *      2、多文件：file[],file[]
     *
     * @author 牧羊人
     * @since 2020-04-21
     */
    public function uploadFile()
    {
        $error = "";
        // 上传文件(非图片)
        $result = upload_file('file', '', $error);
        if (!$result) {
            return message($error);
        }
        return message("上传成功", true, $result);
    }

    /**
     * 上传凭据
     * @return array
     */
    public function uploadNewFile()
    {
        $rule = input('post.rule');
        if(!$rule){
            return return_json([
                'code' => 1,
                'msg' => '规则错误'
            ]);
        }
        $error = "";
        // 上传文件(非图片)
        $result = upload_new_file('file', '', $error,$rule);
        // 插入数据
        $res = \app\common\model\Attachment::create([

        ]);
        if (!$result) {
            return message($error);
        }
        return message("上传成功", true, $result);
    }


    /**
     * 上传到七牛云
     * @return array
     */
    public function qiniuImage()
    {
//        $result = upload_new_file('file', '', $error,$rule=1);
//        var_dump($result);die;
        // 获取文件对象
        $files = \request()->file('file');
        // 判断是否有上传的文件
        if (!$files) {
            return return_json(
                ["err"=>1,"msg"=>'error',"data"=>"请选择图片"]
            );
        }

        // 要上传文件的本地路径
        $filePath = $files->getRealPath();
        //获取文件后缀
        $var = explode('.',$_FILES["file"]['name']);//把字符串以，分开合成一个数组
        $owext =array_pop($var);
//        var_dump(ACCESS_KEY);die;
        // 允许上传的后缀
        $allowext = 'gif,GIF,jpg,JPG,jpeg,JPEG,png,PNG,bmp,BMP';
//        var_dump(strpos($allowext, $owext));
        if(!strpos($allowext, $owext)){
            return return_json(
                ["err"=>1,"msg"=>'error',"data"=>"请选择正确的格式"]
            );

        }
//        die;
        // 上传到存储后保存的文件名
        $key = date('YmdHis',time()).mt_rand(10001,99999).'.'.$owext;

        // 需要填写你的 Access Key 和 Secret Key
        $accessKey ="n49qNvbV4gPK_4qzDnnBSqGymm8Dzp3NcibwD9am";
        $secretKey = "AoidR_bIbHxQ58KYTPx0CyY1qhewtBwue8UkpVUF";
        $bucket = "yrnet-yunding";
        $domain ="pic.yunding.yrnet.top";
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//        echo "\n====> putFile result: \n";

        if ($err !== null) {
            return return_json(
                ["err"=>1,"msg"=>$err,"data"=>""]
            );

        } else {
            $ret['url']='http://'.$domain.'/'.$ret['key'];
//            var_dump($ret);
            return return_json(
                [
                    "err"=>0,
                    "msg"=>$err,
                    "data"=>$ret
                ]
            );
        }
    }

}
