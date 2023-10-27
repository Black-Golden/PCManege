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


use app\common\model\Platform;
use app\common\model\PlatformMember;

/**
 * 平台渠道管理-服务类
 * @author 测试
 * @since: 2022/01/23
 * Class PlatformService
 * @package app\adminapi\service
 */
class PlatformService extends BaseService
{
    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Platform();
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


        // 平台名称
        $name = isset($param['name']) ? trim($param['name']) : '';
        if ($name) {
            $map[] = ['name', 'like', "%{$name}%"];
        }

        return parent::getList($map); // TODO: Change the autogenerated stub
    }


    public function getApiList($userId)
    {

        $data = Platform::where(["mark" => 1])->field("id,name")->select();
        foreach ($data as &$val) {
            $val["key"] = "";
            $val["secret"] = "";
            $val["passphrase"] = "";
            $val['type'] = 0;

            $info = PlatformMember::where(["platform_id" => $val["id"], "user_id" => $userId])->find();
            //156

            if ($info) {
                $val["key"] = $info["key"];
                $val["secret"] = $info["secret"];
                $val["passphrase"] = $info["passphrase"];
                if ($info["platform_id"] == 2) {
                    if ($info["key"] && $info["secret"] && $info["passphrase"]) {
                        $val['type'] = 1;
                    }
                } else {
                    if ($info["key"] && $info["secret"]) {
                        $val['type'] = 1;
                    }
                }
            }
        }
        return return_json($data);
    }


public function plantform($userId){
    $param = $this->input;
    // 验证器 验证
    $validate = getValidate([
        'platform_id' => 'require',
    ], [
        'platform_id.require' => '请选择代币',
    ]);
    if ($validate['code'] == 1) {
        return $validate;
    }
    $info = PlatformMember::where(["platform_id" => $param["platform_id"], "user_id" => $userId])->find();
    if(!$info){
        $info=[
            'platform_id'=>$param['platform_id'],
            'key'=>'',
            'secret'=>'',
            'passphrase'=>'',
        ];
    }
    return $info;
}

}
