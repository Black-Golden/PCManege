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

use app\common\model\Category;
use FormBuilder\Factory\Elm as Form;
use think\Config;
use think\facade\Db;

/**
 * 服务基类
 * @author 牧羊人
 * @since 2020/11/14
 * Class BaseService
 * @package app\common\service
 */
class BaseService
{
    // 模型
    protected $model;
    public $input;

    public function __construct()
    {
        $this->input = request()->param();
        //$this->user_id =
    }
    /**
     * 获取数据列表
     * @return array
     * @since 2020/11/14
     * @author 牧羊人
     */
    public function getList()
    {
        // 初始化变量
        $map = $child_map = [];
        $sort = 'id desc';
        $return_type = 0;
        $is_now_user = 0;
        $show_mark = 1;
        // 获取参数
        $argList = func_get_args();
        // 常规查询条件
        $param = $this->input;

        if (!empty($argList)) {
            // 查询条件
            $map = (isset($argList[0]) && !empty($argList[0])) ? $argList[0] : [];
            // 排序
            $sort = (isset($argList[1]) && !empty($argList[1])) ? $argList[1] : 'id desc';
            // 是否打印SQL
            $is_now_user = isset($argList[2]) ? $argList[2] : 1;
            $return_type = isset($argList[3]) ? $argList[3] : 0;
            $show_mark = isset($argList[4]) ? $argList[4] : 1;
            if ($map) {
                foreach ($map as $val) {
                    $child_map[] = $val[0];
                }
            }
        }

        $url_type = explode("/", request()->url());
        if ($url_type[2] == "v2" && $is_now_user==0) {
            $token = request()->header("token");
            $jwt = new \Jwt();
            $jwt::$key = "show_index";
            $user_id = $jwt->verifyToken($token);
            $map[] = ["user_id", "=", $user_id];
        }
        if ($url_type[1] == "v2" && $is_now_user==0) {
            $token = request()->header("token");
            $jwt = new \Jwt();
            $jwt::$key = "show_index";
            $user_id = $jwt->verifyToken($token);
            $map[] = ["user_id", "=", $user_id];
        }

        $columnName = [];
        $table = $this->model->getColumnList();
        foreach ($table as $key => $val) {
            $columnName[] = $val["columnName"];
        }
        //查询出来的列表
        $show_list_value = (isset($param["show_array_value"]) && !empty($param["show_array_value"])) ? 1 : 0;
        if ($param) {
            foreach ($table as $key => $val) {
                if (!in_array($val["columnName"], $child_map)) {
                    if (isset($param[$val["columnName"]]) && $param[$val["columnName"]] != "") {
                        if ($val["dataType"] == "int") {
                            if (is_array($param[$val["columnName"]])) {
                                $map[] = [$val["columnName"], "in", $param[$val["columnName"]]];
                            } else {
                                $map[] = [$val["columnName"], "=", $param[$val["columnName"]]];
                            }
                        }
                        if ($val["dataType"] == "tinyint") {
                            if (is_array($param[$val["columnName"]])) {
                                $map[] = [$val["columnName"], "in", $param[$val["columnName"]]];
                            } else {
                                $map[] = [$val["columnName"], "=", $param[$val["columnName"]]];
                            }
                        }
                        if ($val["dataType"] == "varchar") {
                            $map[] = [$val["columnName"], 'like', "%" . $param[$val['columnName']] . "%"];
                        }
                    }
                }
            }

            // 筛选名称
            if (isset($param['name']) && $param['name']) {
                $map[] = ['name', 'like', "%{$param['name']}%"];
            }
            if (isset($param['id']) && $param['id']) {
                if (is_array($param['id'])) {
                    $map[] = ["id", "in", $param['id']];
                } else {
                    $map[] = ["id", "=", $param['id']];
                }
            }
            // 筛选标题
            if (isset($param['title']) && $param['title']) {
                $map[] = ['title', 'like', "%{$param['title']}%"];
            }
            // 筛选类型
            if (isset($param['type']) && $param['type']) {
                $map[] = ['type', '=', $param['type']];
            }
            // 手机号码
            if (isset($param['mobile']) && $param['mobile']) {
                $map[] = ['mobile', '=', $param['mobile']];
            }
        }
        if ($show_mark == 1) {
            // 设置查询条件
            if (is_array($map)) {
                $map[] = ['mark', '=', 1];
            } elseif ($map) {
                $map .= " AND mark=1 ";
            } else {
                $map .= " mark=1 ";
            }
        } else {
            if (is_array($map)) {
                $map[] = ['mark', '=', 0];
            } elseif ($map) {
                $map .= " AND mark=0 ";
            } else {
                $map .= " mark=0 ";
            }
        }

        if (input("sort")) {
            if (in_array(input("sort"), $columnName)) {
                $sort = input("sort") . " " . input("order");
            }
        }
        // 其他查询
        if (isset($param['keywords_type']) && $param['keywords_type'] && isset($param['keywords_word']) && $param['keywords_word']) {
            if (in_array($param['keywords_type'], $columnName)) {
                if (!in_array($param['keywords_type'], $child_map)) {
                    $map[] = [$param['keywords_type'], 'like', "%{$param['keywords_word']}%"];
                }

            }
        }

        //显示类型只显示id
        if ($show_list_value == 1) {
            $return = [];
            $result = $this->model->where($map)->order($sort)->field("id,name")->select();
            if ($result) {
                foreach ($result as $val) {
                    $return[] = [
                        "id" => $val["id"],
                        "name" => $val["name"],
                    ];
                }
            }

            $message = array(
                "msg" => '操作成功',
                "code" => 0,
                "data" => $return,
            );

            if ($return_type == 0) {
                return json($message);
            } else {
                return $return;
            }
        }

        foreach ($map as $key => $val) {
            if ($val[0] == "") {
                unset($map[$key]);
            }
        }
        $result = $this->model->where($map)->order($sort)->page(PAGE, PERPAGE)->column("id");
//        echo $this->model->getLastSql();

        $list = [];

        if (is_array($result)) {
            foreach ($result as $val) {
                $info = $this->model->getInfo($val);
                $list[] = $info;
            }
        }
        //获取数据总数
        $count = $this->model->where($map)->count();
        //返回结果
        $message = array(
            "msg" => '操作成功',
            "code" => 0,
            "data" => $list,
            "count" => $count,
        );
        if ($return_type == 0) {
            return json($message);
        } else {
            return $message;
        }

    }

    /**
     * 获取记录详情
     * @return array
     * @since 2020/11/15
     * @author 牧羊人
     */
    public function info()
    {
        // 记录ID
        $id = request()->param("id", 0);
        $info = [];
        if ($id) {
            $info = $this->model->getInfo($id);
        }
        return message("操作成功", true, $info);
    }

    /**
     * 添加或编辑
     * @return array
     * @since 2020/11/14
     * @author 牧羊人
     */
    public function edit()
    {
        // 获取参数
        $argList = func_get_args();
        // 查询条件
        $data = isset($argList[0]) ? $argList[0] : [];
        // 是否打印SQL
        $is_sql = isset($argList[1]) ? $argList[1] : false;

        //处理验证
        $is_validate = isset($argList[2]) ? $argList[2] : false;

        //需要验证的
        $has_validate = isset($argList[3]) ? $argList[3] : [];
        //不需要验证的
        $no_validate = isset($argList[4]) ? $argList[4] : [];

        if (!$data) {
            $data = $this->input;
        }

        if ($is_validate == true) {
            $validate_arr = $this->model->validate($this->model->name, $has_validate, $no_validate);
            $validate = getValidate($validate_arr["rule"], $validate_arr["content"]);
            if ($validate["code"] == 1) {
                return return_json($validate);
            }
        }
        $error = '';
        $rowId = $this->model->edit($data, $error, "");
        if ($rowId) {
            return message();
        }
        return message($error, false);
    }

    /**
     * 删除记录
     * @return array
     * @since 2020/11/12
     * @author 牧羊人
     */
    public function delete()
    {
        // 参数
        $param = $this->input;
        // 记录ID
        $ids = getter($param, "id");
        if (empty($ids)) {
            return message("记录ID不能为空", false);
        }
        if (is_array($ids)) {
            // 批量删除
            $result = $this->model->deleteDAll($ids);
            if (!$result) {
                return message("删除失败", false);
            }
            return message("删除成功");
        } else {
            // 单个删除
            $info = $this->model->getInfo($ids);
            if ($info) {
                $result = $this->model->drop($ids);
                if ($result !== false) {
                    return message();
                }
            }
            return message($this->model->getError(), false);
        }
    }

    /**
     * 设置状态
     * @return array
     * @since 2020/11/14
     * @author 牧羊人
     */
    public function status()
    {
        $data = $this->input;
        if (!$data['id']) {
            return message('记录ID不能为空', false);
        }
        if (!$data['status']) {
            return message('记录状态不能为空', false);
        }
        $error = '';
        $item = [
            'id' => $data['id'],
            'status' => $data['status']
        ];
        $rowId = $this->model->edit($item, $error);
        if (!$rowId) {
            return message($error, false);
        }
        return message();
    }


    //更新单个字段
    public function setattr()
    {
        $data = $this->input;

        $validate = getValidate([
            "attr" => "require|in:status,mark,sign,flag",
            "value" => "require|in:0,1,2",
            "id" => "require",
        ], [
            "attr.require" => '字段不能为空',
            "value.require" => '字段不能为空',
            "id.require" => 'id不能为空',
        ]);
        if ($validate["code"] == 1) {
            return return_json($validate);
        }
        $error = '';

        if (is_array($data['id'])) {
            $update = [];

            foreach ($data['id'] as $key => $val) {
                $update[] = [
                    'id' => $val,
                    $data["attr"] => $data['value']
                ];
            }
            // 批量修改
            $result = $this->model->saveDAll($update);
            if (!$result) {
                return message("修改失败", false);
            }
            return message("修改成功");
        } else {
            $item = [
                'id' => $data['id'],
                $data["attr"] => $data['value']
            ];

            $rowId = $this->model->edit($item, $error);
            if (!$rowId) {
                return message($error, false);
            }
            return message();
        }
    }

    //更新所有字段
    public function setallattr()
    {
        $argList = func_get_args();
        $is_return_array = isset($argList[0]) ? true : false;

        $data = $this->input;
        $validate = getValidate([
            "attr_value" => "require",
            "id" => "require",
        ], [
            "attr_value.require" => '字段不能为空',
            "id.require" => 'id不能为空',
        ]);
        if ($validate["code"] == 1) {
            if (!$is_return_array) {
                return return_json($validate);
            } else {
                return $validate;
            }
        }
        $error = '';
        if (!is_array($data["attr_value"])) {
            $data["attr_value"] = json_decode($data["attr_value"], true);
        }

        if (!is_array($data["attr_value"])) {

            if (!$is_return_array) {
                return return_json([
                    "code" => 1,
                    "msg" => "格式不正确"
                ]);
            } else {
                return [
                    "code" => 1,
                    "msg" => "格式不正确"
                ];
            }
        }
        if (is_array($data['id'])) {
            $update = [];
            foreach ($data['id'] as $key => $val) {
                $data["attr_value"]["id"] = $val;
                $update[] = $data["attr_value"];
            }
            // 批量修改
            $result = $this->model->saveDAll($update);
            if (!$result) {
                if (!$is_return_array) {
                    return return_json([
                        "code" => 1,
                        "msg" => "修改失败"
                    ]);
                } else {
                    return [
                        "code" => 1,
                        "msg" => "修改失败"
                    ];
                }
            }
            if (!$is_return_array) {
                return return_json([]);
            } else {
                return [];
            }
        } else {
            $data["attr_value"]["id"] = $data['id'];
            $rowId = $this->model->edit($data["attr_value"], $error);
            if (!$rowId) {
                if (!$is_return_array) {
                    return return_json([
                        "code" => 1,
                        "msg" => "修改失败"
                    ]);
                } else {
                    return [
                        "code" => 1,
                        "msg" => "修改失败"
                    ];
                }
            }
            if (!$is_return_array) {
                return return_json([]);
            } else {
                return [];
            }
        }
    }


    public function get_field($user_id)
    {
        return return_json($this->model->getColumnList($user_id));
    }

    public function setEnable()
    {
        // 验证器 验证
        $validate = getValidate([
            'id' => 'require',
            'mark' => 'require'
        ], [
            'id.require' => '请选择id',
            'id.mark' => '请传入状态',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $param = $this->input;
        $id = $param['id'];

        if (!is_array($id)) {
            $id = explode(',', $id);
        }
        $results = $this->model->where('id', 'in', $id)
            ->update(["mark" => $param['mark']]);
        if ($results) {
            return message('修改成功', true);
        } else {
            return message('修改失败', false);
        }

    }

    //设置审核状态
    public function setExamine($user_id)
    {
        // 验证器 验证
        $validate = getValidate([
            'id' => 'require',
            'examine_status' => 'require|in:0,1,2,3,4,5,6,7,8,9,10',
        ], [
            'id.require' => '请选择id',
            'examine_status.require' => '请选择状态',
        ]);
        if ($validate['code'] == 1) {
            return $validate;
        }
        $param = $this->input;
        $id = $param['id'];

        if (!is_array($id)) {
            $id = explode(',', $id);
        }

        //销售订单退回||发票转开票类型
        if ($param["examine_status"] == 10) {
            $results = $this->model->where('id', 'in', $id)
                ->update(return_update_user(["examine_status" => 10], $user_id));
        }
        //
        //0未审核 1待审核 2审核通过 3审核失败 4 审核退回 5作废
        if ($param["examine_status"] == 0) {
            if (isset($param["examine_reason"])) {
                $validate = getValidate([
                    'examine_reason' => 'require'
                ], [
                    'examine_reason.require' => '请填写退回理由'
                ]);
                if ($validate['code'] == 1) {
                    return $validate;
                }
                $results = $this->model->where('id', 'in', $id)
                    ->update(return_update_user(["examine_status" => 0, "examine_reason" => $param["examine_reason"]], $user_id));
            } else {
                $results = $this->model->where('id', 'in', $id)
                    ->update(return_update_user(["examine_status" => 0], $user_id));
            }

        }
        if ($param["examine_status"] == 1) {
            if (isset($param["examine_reason"])) {
                $validate = getValidate([
                    'examine_reason' => 'require'
                ], [
                    'examine_reason.require' => '请填写退回理由'
                ]);
                if ($validate['code'] == 1) {
                    return $validate;;
                }
                $results = $this->model->where('id', 'in', $id)
                    ->update(return_update_user(["examine_status" => 1, "examine_reason" => $param["examine_reason"]], $user_id));
            } else {
                $results = $this->model->where('id', 'in', $id)
                    ->update(return_update_user(["examine_status" => 1], $user_id));
            }

        }
        if ($param["examine_status"] == 2) {
            $results = $this->model //->whereIn('examine_status', [1])
            ->where('id', 'in', $id)
                ->update(return_update_user(["examine_status" => 2], $user_id));
        }
        if ($param["examine_status"] == 3) {

            $validate = getValidate([
                'examine_reason' => 'require'
            ], [
                'examine_reason.require' => '请填写审核不通过理由'
            ]);
            if ($validate['code'] == 1) {
                return $validate;
            }

            $results = $this->model //->whereIn('examine_status', [1])
            ->where('id', 'in', $id)
                ->update(return_update_user(["examine_status" => 3], $user_id));
        }
        if ($param["examine_status"] == 4) {
            $validate = getValidate([
                'examine_reason' => 'require'
            ], [
                'examine_reason.require' => '请填写退回理由'
            ]);
            if ($validate['code'] == 1) {
                return $validate;
            }

            $results = $this->model //->whereIn('examine_status', [0, 1, 3])
            ->where('id', 'in', $id)
                ->update(return_update_user(["examine_status" => 0, "examine_reason" => $param["examine_reason"]], $user_id));
        }
        if ($param["examine_status"] == 5) {
            //购货单据整单作废
            if ($this->model->name == "purchase_order_detail_cg") {
                $results = $this->model->where('id', 'in', $id)->select();
                foreach ($results as $val) {
                    $this->model->where(['new_contract_no_one' => $val["new_contract_no_one"]])->update(return_update_user(["examine_status" => 5], $user_id));
                }
            } else {
                if (isset($param["examine_reason"])) {
                    $results = $this->model->where('id', 'in', $id)
                        ->update(return_update_user(["examine_status" => 5, "examine_reason" => $param["examine_reason"]], $user_id));
                } else {
                    $results = $this->model->where('id', 'in', $id)
                        ->update(return_update_user(["examine_status" => 5], $user_id));
                }

            }
        }
        //转接单管理
        if ($param["examine_status"] == 6) {
            $results = $this->model->where('id', 'in', $id)
                ->update(return_update_user(["examine_status" => 6], $user_id));
        }
        if ($param["examine_status"] == 7) {
            $results = $this->model->where('id', 'in', $id)
                ->update(return_update_user(["examine_status" => 7], $user_id));
        }
        if ($param["examine_status"] == 8) {
            $results = $this->model->where('id', 'in', $id)
                ->update(return_update_user(["examine_status" => 8], $user_id));
        }
        if ($param["examine_status"] == 9) {
            $results = $this->model->where('id', 'in', $id)
                ->update(return_update_user(["examine_status" => 9], $user_id));
        }
        $returnData = [
            'code' => 1,
            'data' => [],
            'msg' => '操作失败'
        ];

        if ($results) {
            $returnData = [
                'code' => 0,
                'data' => ["examine_status" => $param["examine_status"], "id" => $id],
                'msg' => '操作成功',
            ];
        }
        return $returnData;
    }


    public function searchform($user_id)
    {
        $config = new Config();
        return $config->load(ROOT_PATH . 'config/form/' . $this->model->name . '.php');;
        //return create_form('添加', $f, "", [], 'PUT');
    }

    public function createform($user_id)
    {
        $config = new Config();
        $f = $config->load(ROOT_PATH . 'config/form/' . $this->model->name . '.php');
        foreach ($f as $key => &$val) {

        }
        //创建表单
        return create_form('添加', $f, "", [], 'PUT');
    }

    public function editform($user_id)
    {
        $list = $this->model->getColumnList($user_id);
        $id = request()->param("id", 0);
        $config = new Config();
        $f = $config->load(ROOT_PATH . 'config/form/' . $this->model->name . '.php');
        $info = $this->model->getInfo($id);
        foreach ($f as $key => &$val) {
            $val["value"] = $info[$val["field"]];
        }
        return create_form('编辑', $f, "", [], 'PUT');
    }
}
