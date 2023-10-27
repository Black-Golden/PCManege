<?php

namespace app\common\model;

use think\facade\Db;

/**
 * 模型基类
 *
 * @author 牧羊人
 * @since 2020-04-21
 */
abstract class  BaseModel extends CacheModel
{
    // 自动写入时间戳字段,true开启false关闭
    protected $autoWriteTimestamp = true;
    // 创建时间字段自定义,默认create_time
    protected $createTime = 'create_time';
    // 更新时间字段自定义,默认update_time
    protected $updateTime = 'update_time';
    // 用户ID
    protected $user_id = 0;
    protected $status;
    protected $flag;
    protected $source;

//    protected $globalScope = ['role'];

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        // 解析Token获取用户ID
        $token = request()->header("Authorization");
        $token = str_replace("Bearer ", null, $token);
        $jwt = new \Jwt();
        $this->user_id = $jwt->verifyToken($token);
        //
    }

    public function scopeRole($query)
    {
        $role_id = Db::name('admin_user')->where(['id' => $this->user_id])->field('id,group_role_id,role_type')->find();
        if ($this->user_id != 1) {
            $role_type = $role_id['role_type'];
            $group_role_id = $role_id['group_role_id'];
            $this->input = request()->param();
            if ($role_type == 1) {
                if (isset($this->input["is_show"])) {
                    if ($this->input["is_show"] == 0) {
                        $query->where('create_user_id|update_user_id', 'in', [$this->user_id]);
                    }
                } else {
                    $query->where('create_user_id|update_user_id', 'in', [$this->user_id]);
                }
            }
            if ($role_type == 2) {
                if ($group_role_id) {
                    $group_role_id = explode(',', $group_role_id);
                    $query->where('belong_clique_id|belong_company_id|belong_group_id|belong_dept_id', 'in', $group_role_id)->whereOr('create_user_id|update_user_id','in', [$this->user_id]);
                   // $query->where('belong_clique_id|belong_company_id|belong_group_id|belong_dept_id|update_belong_clique_id|update_belong_company_id|update_belong_dept_id|update_belong_group_id', 'in', $group_role_id)->whereOr('create_user_id|update_user_id','in', [$this->user_id]);
                } else {
                    $query->where('create_user_id|update_user_id','in', [$this->user_id]);
                }
            }
        }
    }


//    public function setCreateTimeAttr($value)
//    {
//        if($value){
//            return date('Y-m-d H:i:s', $value);
//        }
//        return '';
//    }
    public function getPicUrlAttr($value)
    {
        if($value){
            return date('Y-m-d H:i:s', $value);
        }
        return '';

    }

    public function getAvatarAttr($value)
    {
        return get_image_url($value);
    }


    /**
     * 添加或编辑
     *
     * @param array $data 数据源
     * @param string $error 错误提示
     * @param bool $is_sql 是否打印SQL
     * @return int|string 返回记录ID
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function edit($data = [], &$error = '', $is_sql = false)
    {
        // 基础参数设置
        $id = isset($data[$this->getPk()]) ? (int)$data[$this->getPk()] : 0;
        if ($id) {


            // 更新时间
            $data['update_time'] = time();
            // 更新人
          //  $data['update_user'] = $this->user_id;
//            // 置空添加时间
            unset($data['create_time']);
//            // 置空添加人
//            unset($data['create_user']);
        } else {
            // 添加时间
            $data['create_time'] = time();
            // 添加人
         //   $data['create_user'] = $this->user_id;
        }

        // 格式化表数据
        $this->formatData($data, $id);

//        // 创建数据,并验证
//        if (!$this->create($data)) {
//            // 验证失败
//            $error = $this->getError();
//        }
        // 入库处理
        if ($id) {
            //修改数据
            $result = $this->update($data, [$this->getPk() => $id]);
            $this->resetCacheFunc('info', $id);
            // 更新ID
            $rowId = $id;
        } else {
            // 新增数据
            $result = $this->insertGetId($data);
            // 新增ID
            $rowId = $result;
        }
        #todu
        $this->resetCacheFunc('info', $id);

        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }

        if ($result !== false) {
            // 重置缓存
            $data[$this->getPk()] = $rowId;
            $this->cacheReset($rowId, $data, $id);
        }
        return $rowId;
    }

    /**
     * 格式化表数据
     *
     * @param array $data 数据源
     * @param int $id 记录ID
     * @param string $table 表字段数据
     * @return array 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    private function formatData(&$data = [], $id = 0, $table = '')
    {

        $data_list = [];
        $tables = $table ? explode(",", $table) : array($this->getTable());
        $item_data = [];
        foreach ($tables as $table) {
            $temp_data = [];
            $table_fields_list = $this->getTableFieldsList($table);
            foreach ($table_fields_list as $field => $field_info) {
                if ($field == "id") {
                    continue;
                }
                // 强制类型转换
                if (isset($data[$field])) {
                    if ($field_info['Type'] == "int") {
                        $item_data[$field] = (int)$data[$field];
                    } else {
                        if (is_array($data[$field])) {
                            $output = json_encode($data[$field], JSON_UNESCAPED_UNICODE);
                        } else {
                            $output = (string)$data[$field];
                        }
                        $item_data[$field] = $output;
                    }
                }
                if (!isset($data[$field]) && in_array($field, array('update_time', 'create_time'))) {
                    continue;
                }
                // 插入数据-设置默认值
                if (!$id && !isset($data[$field])) {
                    $item_data[$field] = $field_info['Default'];
                }
                if (isset($item_data[$field])) {
                    $temp_data[$field] = $item_data[$field];
                }
            }
            $data_list[] = $temp_data;
        }
        $data = $item_data;
        return $data_list;
    }

    /**
     * 获取表字段
     *
     * @param string $table 数据表名称
     * @return array 表字段
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    private function getTableFieldsList($table = '')
    {
        $table = $table ? $table : $this->getTable();
        $field_list = Db::query("SHOW FIELDS FROM {$table}");
        $info_list = [];
        foreach ($field_list as $row) {
            if ((strpos($row['Type'], "int") === false) ||
                (strpos($row['Type'], "bigint") !== false)
            ) {
                $type = "string";
                $default = $row['Default'] ? $row['Default'] : "";
            } else {
                $type = "int";
                $default = $row['Default'] ? $row['Default'] : 0;
            }
            $info_list[$row['Field']] = array(
                'Type' => $type,
                'Default' => $default
            );
        }
        return $info_list;
    }


    /**
     * 获取表字段列表
     * @param $tableName 数据表名
     * @return array
     * @author 牧羊人
     * @since 2020/7/15
     */
    public function getColumnList()
    {
        // 获取表列字段信息
        $tableName = $this->getTable();
        $columnList = Db::query("SELECT COLUMN_NAME,COLUMN_DEFAULT,DATA_TYPE,COLUMN_TYPE,COLUMN_COMMENT FROM information_schema.`COLUMNS` where TABLE_SCHEMA = '" . env('database.database') . "' AND TABLE_NAME = '{$tableName}'");
        $fields = [];
//        $arrayList= [];
        if ($columnList) {
            foreach ($columnList as $val) {
                $column = [];
                // 列名称
                $column['columnName'] = $val['COLUMN_NAME'];
                // 列默认值
                $column['columnDefault'] = $val['COLUMN_DEFAULT'];
                // 数据类型
                $column['dataType'] = $val['DATA_TYPE'];
                // 列描述
                if (strpos($val['COLUMN_COMMENT'], '：') !== false) {
                    $item = explode("：", $val['COLUMN_COMMENT']);
                    $column['columnComment'] = $item[0];

                    // 拆解字段描述
                    $param = explode(" ", $item[1]);
                    $columnValue = [];
                    $columnValueList = [];
                    foreach ($param as $vo) {
                        // 键值
                        $key = preg_replace('/[^0-9]/', '', $vo);
                        // 键值内容
                        $value = str_replace($key, null, $vo);
                        $columnValue[] = [
                            'value' => $key,
                            'name' => $value,
                        ];//"{$key}={$value}";
                        $columnValueList[] = $value;
                    }
                    $column['columnValue'] = $columnValue;//implode(',', $columnValue);
                    if ($val['COLUMN_NAME'] == "status" || substr($val['COLUMN_NAME'], 0, 3) == "is_") {
                        $column['columnSwitch'] = true;
                        $column['columnSwitchValue'] = implode('|', $columnValueList);
                        if ($val['COLUMN_NAME'] == "status") {
                            $column['columnSwitchName'] = "status";
                        } else {
                            $column['columnSwitchName'] = 'set' . str_replace(' ', null, ucwords(strtolower(str_replace('_', ' ', $val['COLUMN_NAME']))));
                        }
                    } else {
                        $column['columnSwitch'] = false;
                    }
                } else {
                    $column['columnComment'] = $val['COLUMN_COMMENT'];
                }
                $fields[] = $column;
            }
        }
        if ($fields) {
            foreach ($fields as $val) {
                // 记录ID
                if ($val['columnName'] == "id") {
                    continue;
                }
                // 创建人
                if ($val['columnName'] == "create_user") {
                    continue;
                }
                // 创建时间
//                if ($val['columnName'] == "create_time") {
//                    continue;
//                }
                // 更新人
                if ($val['columnName'] == "update_user") {
                    continue;
                }
                // 更新时间
                if ($val['columnName'] == "update_time") {
                    continue;
                }
                // 有效标识
                if ($val['columnName'] == "mark") {
                    continue;
                }
                // 图片字段处理
                if (strpos($val['columnName'], "cover") !== false ||
                    strpos($val['columnName'], "avatar") !== false ||
                    strpos($val['columnName'], "image") !== false ||
                    strpos($val['columnName'], "logo") !== false ||
                    strpos($val['columnName'], "pic") !== false) {
                    $val['columnUpload'] = true;
                    $tempList[] = $val;
                    continue;
                }
                // 多行文本输入框
                /*if (strpos($val['columnName'], "note") !== false ||
                    strpos($val['columnName'], "content") !== false ||
                    strpos($val['columnName'], "description") !== false ||
                    strpos($val['columnName'], "intro") !== false) {
                    $val['columnRow'] = true;
                    $rowList[] = $val;
                    continue;
                }*/
                // 由于目前时间字段采用int类型，所以这里根据字段描述模糊确定是否是时间选择
                if (strpos($val['columnComment'], "时间") !== false) {
                    $val['dataType'] = 'datetime';
                } elseif (strpos($val['columnComment'], "日期") !== false) {
                    $val['dataType'] = 'date';
                }

                // 图片字段处理
                if (strpos($val['columnName'], "cover") !== false ||
                    strpos($val['columnName'], "avatar") !== false ||
                    strpos($val['columnName'], "image") !== false ||
                    strpos($val['columnName'], "logo") !== false ||
                    strpos($val['columnName'], "pic") !== false) {
                    $val['columnImage'] = true;
                }
                $arrayList[] = $val;
            }
        }

        return $arrayList;
    }


    /**
     * 删除记录
     *
     * @param int $id 记录ID
     * @param bool $is_sql 是否打印SQL
     * @return int 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function drop($id, $is_sql = false)
    {
        // 设置mark值为0
        $result = $this->where([$this->getPk() => $id])->data(['mark' => 0])->save();
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        if ($result !== false) {
            //删除成功
            $this->cacheDelete($id);
        }
        return $result;
    }

    /**
     * 获取缓存信息
     *
     * @param int $id 记录ID
     * @return mixed 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getInfo($id)
    {
        // 获取参数(用户提取操作人信息)
        $argList = func_get_args();
        $with = isset($argList[1]) ? $argList[1] : false;
        // 获取缓存信息
        $info = $this->getCacheFunc("info", $id);

        if ($info) {
            // 获取系统人员缓存
            $userModel = new AdminUser();
            $userAll = $userModel->getAll([], false, true);

            // 添加人
            if (isset($info['create_user']) && $info['create_user']) {
                $info['create_user_name'] = $userAll[$info['create_user']]['realname'];
            }
//            // 添加时间
//            if (isset($info['create_time']) && $info['create_time']) {
//                if($info['create_time']){
//                    $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
//                }
//
//            }

            // 更新人
            if (isset($info['update_user']) && $info['update_user']) {
                $info['update_user_name'] = $userAll[$info['update_user']]['realname'];
            }
//            // 更新时间
//            if (isset($info['update_time']) && $info['update_time']) {
//                if($info['update_time']){
//                $info['update_time'] = date('Y-m-d H:i:s', $info['update_time']);
//                }
//
//            }


            // 格式化信息
            if (method_exists($this, 'formatInfo')) {
                $info = $this->formatInfo($info, $with);
            }

        }
//        var_dump($info);
        return $info;
    }

    /**
     * 格式化信息
     *
     * @param $info 数据信息
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function formatInfo($info, $with = [])
    {
        // 基类方法可不做任何操作，在子类重写即可


        $keys = array_keys($info);

        foreach ($keys as $val) {
            //if (in_array($val, ["status", "flag", "source", "type"])) {
//            if (isset(config('attr.public')[$val])) {
//                if ($info[$val]) {
//
//                    $info[$val . '_name'] = config('attr.public')[$val][$info[$val]];
//
//                } else {
//                    $info[$val . '_name'] = "";
//                }
//            }
            if (isset(config('attr')[$this->name][$val])) {

                if ($info[$val]) {
                    $info[$val . '_name'] = config('attr')[$this->name][$val][$info[$val]];
                } else {
                    $info[$val . '_name'] = "";
                }
            }
        }
        foreach ($info as $key => $val) {
            //处理一对一
            if (strstr($key, '_id') && $key != "id" && !strstr($key, '_ids')) {
                $model = toCamelCaseUnsetId($key);
                $className = 'app\\model\\' . $model;
                if (class_exists($className)) {
                    $self = new $className();
                    $self_data = $self->find((int)$val);
                    $self_data = !empty($self_data) ? $self_data->toArray() : [];
                    $key_name = str_replace("_id", "_name", $key);
                    $info[$key_name] = "";
                    if ($self_data) {
                        if (isset($self_data["name"])) {
                            $info[$key_name] = $self_data["name"];
                        }
                        if (isset($self_data["nickname"])) {
                            $info[$key_name] = $self_data["nickname"];
                        }
                    }
                }
            }
            if (strstr($key, '_ids')) {
                $model = toCamelCaseUnsetId($key);
                $className = 'app\\model\\' . $model;
                if (class_exists($className)) {
                    $self = new $className();
                    $self_data = $self->whereIn("id", explode(",", $val))->field("id,name")->select();
                    if ($self_data) {
                        foreach ($self_data as $v) {
                            $return[] = [
                                "id" => $v["id"],
                                "name" => $v["name"],
                            ];
                        }
                    }
                    $key_name = str_replace("_ids", "_names", $key);
                    if ($self_data) {
                        $info[$key_name] = $self_data;
                    } else {
                        $info[$key_name] = [];
                    }
                }
            }
        }

        if (!$with) {
            //判断是否有关联的数据
            if (isset(config('with')[$this->name])) {
                foreach (config('with')[$this->name] as $key => $val) {
                    $model = toCamelCase($val);
                    $className = 'app\\model\\' . $model;
                    if (class_exists($className)) {
                        $self = new $className();
                        $map[] = [$this->name . "_id", '=', $info["id"]];
                        $self_data = $self->getList($map);
                        $info[$val] = $self_data;
                    }
                }
            }
        } else {
            if (isset($with["show_detail"]) && $with["show_detail"] = 1) {
                foreach ($with["with"] as $key => $val) {
                    $model = toCamelCase($val);
                    $className = 'app\\model\\' . $model;
                    if (class_exists($className)) {
                        $self = new $className();
                        $map[] = [$this->name . "_id", '=', $info["id"]];
                        $self_data = $self->getList($map);
                        $info[$val] = $self_data;
                    }
                }
            }
        }


        return $info;
    }

    /**
     * 查询记录总数
     *
     * @param array $map 查询条件
     * @param string $fields 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return int 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getCount($map = [], $fields = '', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        if ($fields) {
            $count = $query->count($fields);
        } else {
            $count = $query->count();
        }
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return (int)$count;
    }

    /**
     * 获取某个字段的求和值
     *
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return float 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getSum($map = [], $field = '', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->sum($field);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某个字段的最大值
     *
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $force 是否强制true或false
     * @param bool $is_sql 是否打印SQL
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getMax($map = [], $field = '', $force = true, $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->max($field, $force);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某个字段的最小值
     *
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $force 是否强制true或false
     * @param bool $is_sql 是否打印SQL
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getMin($map = [], $field = '', $force = true, $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->min($field, $force);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某个字段的平均值
     *
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return float 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getAvg($map = [], $field = '', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->avg($field);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

//    /**
//     * 获取某个字段的值
//     *
//     * @param array $map 查询条件
//     * @param string $field 字段名称
//     * @param bool $is_sql 是否打印SQL
//     * @return mixed 返回结果
//     * @author 牧羊人
//     * @date 2020-04-21
//     */
//    public function getValue2($map = [], $field = '', $is_sql = false)
//    {
//        // 闭包查询条件格式化
//        $query = $this->formatQuery($this, $map);
//        // 链式操作
//        $result = $query->value($field);
//        // 打印SQL
//        if ($is_sql) {
//            echo $this->getLastSql();
//        }
//        return $result;
//    }

    /**
     * 查询单条记录
     *
     * @param array $map 查询条件
     * @param bool $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return array 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getOne($map = [], $field = true, $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->field($field)->find();
        // 对象转数组
        $result = $result ? $result->toArray() : [];
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 根据ID获取某一行的值
     *
     * @param int $id 记录ID
     * @param bool $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return array 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getRow($id, $field = true, $is_sql = false)
    {
        // 链式操作
        $result = $this->where($this->getPk(), $id)->field($field)->find();
        $result = $result ? $result->toArray() : [];
        // 打印SQL
        if ($is_sql) {
            $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某一列的值
     *
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param string $key 数组键名
     * @param bool $is_sql 是否打印SQL
     * @return array 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getColumn($map = [], $field = '', $key = '', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        if ($key) {
            $result = $query->column($field, $key);
        } else {
            $result = $query->column($field);
        }
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 根据条件查询单条缓存记录
     *
     * @param array $map 查询条件
     * @param array $fields 字段名
     * @param array $sort 排序方式
     * @param int $id 记录ID
     * @return array|mixed 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getInfoByAttr($map = [], $fields = [], $sort = "id desc", $id = 0)
    {
        // 排除主键
        if ($id) {
            if (is_array($map)) {
                $map[] = [$this->getPk(), '!=', $id];
            } elseif ($map) {
                $map .= " AND " . $this->getPk() . " != {$id}";
            } else {
                $map .= $this->getPk() . " != {$id}";
            }
        }

        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);

        // 排序
        if (is_array($sort)) {
            // 闭包解析排序
            $query = $query->when($sort, function ($query, $sort) {
                foreach ($sort as $v) {
                    $query->order($v[0], $v[1]);
                }
            });
        } else {
            // 普通排序
            $query->order($sort);
        }

        // 链式操作
        $result = $query->field($this->getPk())->find();

        // 对象转数组
        $result = $result ? $result->toArray() : [];

        // 查询缓存
        $data = [];
        if ($result) {
            $info = $this->getInfo($result[$this->getPk()]);
            if ($info && !empty($fields) && $fields != "*") {
                // 逗号','分隔字段转数组
                if (!is_array($fields)) {
                    $fields = explode(',', $fields);
                }
                foreach ($fields as $val) {
                    $data[trim($val)] = $info[trim($val)];
                }
                unset($info);
            } else {
                $data = $info;
            }
        }
        return $data;
    }

    /**
     * 获取数据列表
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getDataList()
    {
        // 获取参数
        $argList = func_get_args();
        // 查询参数
        $map = isset($argList[0]['query']) ? $argList[0]['query'] : [];
        // 排序
        $order = isset($argList[0]['order']) ? $argList[0]['order'] : [];
        // 获取条数
        $limit = isset($argList[0]['limit']) ? $argList[0]['limit'] : '';
        // 回调方法名
        $func = isset($argList[1]) ? $argList[1] : "Short";
        // 自定义MODEL
        $model = isset($argList[2]) ? $argList[2] : $this;
        // 闭包查询条件格式化
        $query = $this->formatQuery($model, $map);
        // 排序(支持多重排序)
        if (!empty($order)) {
            $query = $query->when($order, function ($query, $order) {
                foreach ($order as $v) {
                    $query->order($v);
                }
            });
        }
        // 查询数据源
        if ($limit) {
            list($offset, $page_size) = explode(',', $limit);
            $query->limit($offset, $page_size);
        } else {
            // TODO...
        }
        // 查询数据并转为数组
        $result = $query->field($this->getPk())->select();
        $list = [];
        if ($result) {
            foreach ($result as $val) {
                $info = $model->getInfo($val[$this->getPk()]);
                if (!$info) {
                    continue;
                }
                if (is_object($func)) {
                    // 方法函数
                    $data = $func($info);
                } else {
                    // 直接返回
                    $data = $info;
                }
                $list[] = $data;
            }
        }
        return $list;
    }

    /**
     * 获取分页数据列表
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function pageData()
    {
        // 获取参数
        $argList = func_get_args();
        // 查询参数
        $map = isset($argList[0]['query']) ? $argList[0]['query'] : [];
        // 排序
        $order = isset($argList[0]['order']) ? $argList[0]['order'] : [];
        // 页码
        $page = isset($argList[0]['page']) ? $argList[0]['page'] : 1;
        // 每页数
        $perpage = isset($argList[0]['perpage']) ? $argList[0]['perpage'] : 20;
        // 回调方法名
        $func = isset($argList[1]) ? $argList[1] : "Short";
        // 自定义MODEL
        $model = isset($argList[2]) ? $argList[2] : $this;

        // 分页设置
        $start = ($page - 1) * $perpage;
        $limit = "{$start},{$perpage}";

        // 闭包查询条件格式化
        $query = $this->formatQuery($model, $map);

        // 查询总数
        $count = $query->count();

        // 排序(支持多重排序)
        if (!empty($order)) {
            $query = $query->when($order, function ($query, $order) {
                foreach ($order as $v) {
                    $query->order($v);
                }
            });
        }

        // 分页设置
        list($offset, $page_size) = explode(',', $limit);
        $result = $query->limit($offset, $page_size)->field($this->getPk())->select();
        $list = [];
        if ($result) {
            foreach ($result as $val) {
                $info = $model->getInfo($val[$this->getPk()]);
                if (!$info) {
                    continue;
                }
                if (is_object($func)) {
                    //方法函数
                    $data = $func($info);
                } else {
                    // 直接返回
                    $data = $info;
                }
                $list[] = $data;
            }
        }

        // 返回结果
        $result = array(
            'count' => $count,
            'perpage' => $perpage,
            'page' => $page,
            'list' => $list,
        );
        return $result;
    }

    /**
     * 获取分页数据列表
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function pageList()
    {
        // 获取参数
        $argList = func_get_args();
        // 查询参数
        $map = isset($argList[0]['query']) ? $argList[0]['query'] : [];
        // 排序
        $order = isset($argList[0]['order']) ? $argList[0]['order'] : [];
        // 页码
        $page = isset($argList[0]['page']) ? $argList[0]['page'] : 1;
        // 每页数
        $perpage = isset($argList[0]['perpage']) ? $argList[0]['perpage'] : 20;
        // 回调方法名
        $func = isset($argList[1]) ? $argList[1] : "Short";
        // 自定义MODEL
        $model = isset($argList[2]) ? $argList[2] : $this;

        // 分页设置
        $start = ($page - 1) * $perpage;
        $limit = "{$start},{$perpage}";

        // 闭包查询条件格式化
        $query = $this->formatQuery($model, $map);

        // 查询总数
        $count = $query->count();

        // 排序(支持多重排序)
        if (!empty($order)) {
            $query = $query->when($order, function ($query, $order) {
                foreach ($order as $v) {
                    $query->order($v);
                }
            });
        }

        // 分页设置
        list($offset, $page_size) = explode(',', $limit);
        $data = $query->limit($offset, $page_size)->select()->toArray();
        $list = [];
        if ($data) {
            foreach ($data as $val) {
                if (is_object($func)) {
                    //方法函数
                    $data = $func($val);
                } else {
                    // 直接返回
                    $data = $val;
                }
                $list[] = $data;
            }
        }
        // 返回结果
        $result = array(
            'count' => $count,
            'perpage' => $perpage,
            'page' => $page,
            'list' => $list,
        );
        return $result;
    }

    /**
     * 获取全部数据表
     *
     * @return array 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getTablesList()
    {
        $tables = [];
        $database = strtolower(env('database.database'));
        $sql = 'SHOW TABLES';
        $data = Db::query($sql);
        foreach ($data as $v) {
            $tables[] = $v["Tables_in_{$database}"];
        }
        return $tables;
    }

    /**
     * 检查表是否存在
     *
     * @param string $table 数据表名称
     * @return bool 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function tableExists($table)
    {
        $tables = $this->getTablesList();
        if (strpos($table, DB_PREFIX) === false) {
            $table = DB_PREFIX . $table;
        }
        return in_array($table, $tables) ? true : false;
    }

    /**
     * 删除数据表
     *
     * @param string $table 数据表名称
     * @return mixed 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function dropTable($table)
    {
        if (strpos($table, DB_PREFIX) === false) {
            $table = DB_PREFIX . $table;
        }
        return Db::query("DROP TABLE {$table}");
    }

    /**
     * 获取数据表字段
     *
     * @param string $table 数据表名称
     * @return array 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getFieldsList($table)
    {

        if (strpos($table, DB_PREFIX) === false) {
            $table = DB_PREFIX . $table;
        }
        $fields = [];
        $data = Db::query("SHOW COLUMNS FROM {$table}");
        foreach ($data as $v) {
            $fields[$v['Field']] = $v['Type'];
        }
        return $fields;
    }


    /**
     * 获取数据表字段
     *
     * @param string $table 数据表名称
     * @return array 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getFieldsCommentList($table)
    {
        if (strpos($table, DB_PREFIX) === false) {
            $table = DB_PREFIX . $table;
        }
        $fields = [];
        $data = Db::query("SHOW FULL COLUMNS FROM {$table}");
        foreach ($data as $v) {
            $comment = explode("：", $v['Comment']);
            $fields[] = [
                "type" => $v['Type'],
                "field" => $v["Field"],
                "comment" => $comment[0],
            ];
        }
        return $fields;
    }

    //返回正则数据
    public function validate($table, $need_array = [], $no_array = [])
    {
        $list = $this->getFieldsCommentList($table);
        $no_array = array_merge($no_array, ["create_user", "create_time", "update_user", "update_time", "mark", "status"]);
        $return["rule"] = $return["content"] = [];
        foreach ($list as $key => $value) {
            if ($need_array) {
                if (in_array($value["field"], $need_array)) {
                    $return["rule"][$value["field"]] = "require";
                    $return["content"][$value["field"] . ".require"] = $value["comment"] . "不能为空";
                }
            } else {
                if (!in_array($value["field"], $no_array) && $value["field"] != "id") {
                    $return["rule"][$value["field"]] = "require";
                    $return["content"][$value["field"] . ".require"] = $value["comment"] . "不能为空";
                }
            }
        }
        return $return;

    }


    /**
     * 检查字段是否存在
     *
     * @param string $table 数据表名称
     * @param string $field 字段名称
     * @return bool 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function fieldExists($table, $field)
    {
        $fields = $this->getFieldsList($table);
        return array_key_exists($field, $fields);
    }

    /**
     * 插入数据
     *
     * @param array $data 数据源
     * @param bool $get_id 是否返回记录ID,默认true
     * @param bool $is_sql 是否打印SQL
     * @return int|string 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function doInsert($data, $get_id = true, $is_sql = false)
    {
        if ($get_id) {
            // 插入数据并返回主键
            $result = $this->insertGetId($data);
        } else {
            // 返回影响数据的条数，没修改任何数据返回 0
            $result = $this->insert($data);
        }
        // 打印SQL
        if ($is_sql) {
            $this->getLastSql();
        }
        return $result;
    }

    /**
     * 更新数据
     *
     * @param array $data 数据源
     * @param $where 查询条件
     * @param bool $is_sql 是否打印SQL
     * @return int|string 返回结果
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function doUpdate($data, $where, $is_sql = false)
    {
        $result = $this->where($where)->update($data);
        // 打印SQL
        if ($is_sql) {
            $this->getLastSql();
        }
        return $result;
    }

    /**
     * 物理删除数据
     *
     * @param $where 查询条件
     * @param bool $is_sql 是否打印SQL
     * @return int 返回结果
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function doDelete($where, $is_sql = false)
    {
        $result = $this->where($where)->delete();
        // 打印SQL
        if ($is_sql) {
            $this->getLastSql();
        }
        return $result;
    }

    /**
     * 更新字段
     *
     * @param array $data 数据源
     * @param string $error 错误信息
     * @param bool $is_sql 是否打印SQL
     * @return bool|int|string 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function doEdit($data = [], &$error = '', $is_sql = false)
    {
        // 基础参数设置
        $id = isset($data[$this->getPk()]) ? (int)$data[$this->getPk()] : 0;
        if (!$id) {
            return false;
        }
        // 更新时间
        if (empty($data['update_time'])) {
            $data['update_time'] = time();
        }
        // 更新人
        if (empty($data['update_user'])) {
            $data['update_user'] = session('user_id');
        }

        // 格式化表数据
        $this->formatData($data, $id);

        // 入库处理
        if ($id) {
            //修改数据
            $result = $this->update($data, [$this->getPk() => $id]);
            // 更新ID
            $rowId = $id;
        } else {
            // 新增数据
            $result = $this->insertGetId($data);
            // 新增ID
            $rowId = $result;
        }

        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }

        if ($result !== false) {
            // 重置缓存
            $data[$this->getPk()] = $rowId;
            $this->cacheReset($rowId, $data, $id);
        }
        return $rowId;
    }

    /**
     * 批量插入数据
     *
     * @param array $data 数据源
     * @param bool $is_cache 是否设置缓存true或false
     * @return bool|int|string 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function insertDAll($data, $is_cache = true)
    {
        if (!is_array($data)) {
            return false;
        }
        if ($is_cache) {
            // 插入数据并设置缓存
            $num = 0;
            foreach ($data as $val) {
                $result = $this->edit($val);
                if ($result) {
                    $num++;
                }
            }
            return $num ? true : false;
        } else {
            // 插入数据不设置缓存
            return $this->insertAll($data);
        }
        return false;
    }

    /**
     * 批量更新数据
     *
     * @param array $data 数据源
     * @param bool $is_cache 是否设置缓存true或false
     * @return bool 返回结果
     * @throws \Exception
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function saveDAll($data, $is_cache = true)
    {
        if (!is_array($data)) {
            return false;
        }

        // 受影响行数
        $num = 0;
        if (!$is_cache) {
            // 批量更新数据(不设置缓存)
            $result = $this->saveAll($data);
            $num = $result;
        } else {
            // 批量更新数据(同步更新缓存)
            foreach ($data as $val) {
                if (!isset($val[$this->getPk()]) || empty($val[$this->getPk()])) {
                    continue;
                }
                // 更新数据并设置缓存
                $result = $this->edit($val);
                if ($result) {
                    $num++;
                }
            }
        }
        return $num ? true : false;
    }

    /**
     * 批量删除
     *
     * @param array $data 记录ID(数组或逗号','分隔ID)
     * @param bool $is_force 是否物理删除true或false
     * @return bool 返回结果
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function deleteDAll($data, $is_force = false)
    {
        if (empty($data)) {
            return false;
        }
        if (!is_array($data)) {
            $data = explode(',', $data);
        }

        $num = 0;
        foreach ($data as $val) {
            if ($is_force) {
                // 物理删除
                $result = $this->where($this->getPk(), $val)->delete();
                if ($result) {
                    $this->cacheDelete($val);
                }
            } else {
                // 软删除
                $result = $this->drop($val);
            }
            if ($result) {
                $num++;
            }
        }
        return $num ? true : false;
    }

    /**
     * 查询多条记录
     *
     * @param array $map 查询条件
     * @param string $order 排序
     * @param string $limit 限制条数
     * @param bool $is_sql 是否打印SQL
     * @return array 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function getList($map = [], $order = '', $limit = '', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);

        // 链式操作
        if (!empty($order)) {
            $query = $query->order($order);
        }


        // 分页设置
        if ($limit) {
            list($offset, $page_size) = explode(',', $limit);
            $query = $query->limit($offset, $page_size);
        }
        // 查询结果
        $result = $query->column($this->getPk());

        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }

        $list = [];
        if ($result) {
            foreach ($result as $val) {
                $info = $this->getInfo($val);
                if (!$info) {
                    continue;
                }
                $list[] = $info;
            }
        }
        return $list;
    }

    /**
     * 格式化查询条件
     *
     * @param $model 当前模型
     * @param array $map 查询条件
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function formatQuery($model, $map = [])
    {
        if (is_array($map)) {
            $map[] = ['mark', '=', 1];
        } elseif ($map) {
            $map .= " AND mark=1 ";
        } else {
            $map .= " mark=1 ";
        }
        $query = $model->where($map);

        return $query;
    }

    /**
     * 启动事务
     *
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function startTrans()
    {
        // 事务-缓存相关处理
        $GLOBALS['trans'] = true;
        $transId = uniqid("trans_");
        $GLOBALS['trans_id'] = $transId;
        $GLOBALS['trans_keys'] = [];
        $info = debug_backtrace();
        $this->setCache($transId, $info[0]);

        // 启动事务
        Db::startTrans();
    }

    /**
     * 回滚事务
     *
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function rollback()
    {
        // 回滚事务
        Db::rollback();

        // 回滚缓存处理
        foreach ($GLOBALS['trans_keys'] as $key) {
            $this->deleteCache($key);
        }
        $this->deleteCache($GLOBALS['trans_id']);
        $GLOBALS['trans'] = false;
        $GLOBALS['trans_keys'] = [];
    }

    /**
     * 提交事务
     *
     * @author 牧羊人
     * @date 2020-04-21
     */
    public function commit()
    {
        // 提交事务
        Db::commit();

        // 事务缓存同步删除
        $GLOBALS['trans'] = false;
        $GLOBALS['trans_keys'] = [];
        $this->deleteCache($GLOBALS['trans_id']);
    }
}
