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

use app\common\model\Menu;
use think\facade\Db;

/**
 * 代码生成器-服务类
 * @author 牧羊人
 * @since 2020/11/15
 * Class GenerateService
 * @package app\common\service
 */
class GenerateService extends BaseService
{

    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取数据表
     * @return array
     * @since 2020/11/15
     * @author 牧羊人
     */
    public function getList()
    {
        // 查询SQL
        $sql = "SHOW TABLE STATUS WHERE 1";
        // 请求参数
        $param = request()->param();
        // 表名称
        $name = getter($param, "name");
        if ($name) {
            $sql .= " AND NAME like \"%{$name}%\" ";
        }
        // 表描述
        $comment = getter($param, "comment");
        if ($comment) {
            $sql .= " AND COMMENT like \"%{$comment}%\" ";
        }
        $list = Db::query($sql);
        $list = array_map('array_change_key_case', $list);

        return $message = array(
            "msg" => '操作成功',
            "code" => 0,
            "data" => $list,
            "count" => count($list),
        );
    }

    /**
     * 一键生成
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/15
     */
    public function generate()
    {
        // 参数
        $param = $this->input;//request()->param();
        // 数据表名
        $name = getter($param, "name");
        if (!$name) {
            return message("数据表名称不能为空", false);
        }
        // 数据表名称
        $tableName = str_replace(DB_PREFIX, null, $name);
        // 模型名称
        $moduleName = str_replace(' ', null, ucwords(strtolower(str_replace('_', ' ', $tableName))));
        // 控制器名称
        $controllerName = ucfirst(strtolower(str_replace('_', '', $tableName)));
        // 控制器名称
//        $controllerIndexName = ucfirst(strtolower(str_replace('_', '', "font_".$tableName)));
        $controllerIndexName = ucfirst(strtolower(str_replace('_', '', $tableName)));
        // 数据表描述
        $comment = getter($param, "comment");
        $comment = explode("|", $comment)[0];
        if (!$comment) {
            return message("数据表名称不能为空", false);
        }
        // 设置表名
        $menuName = $comment;
        // 去除表描述中的`表`
        if (strpos($comment, "表") !== false) {
            $comment = str_replace("表", null, $comment);
            $menuName = $comment;
        }
        // 去除表描述中的`管理`
        if (strpos($comment, "管理") !== false) {
            $comment = str_replace("管理", null, $comment);
            $menuName = $comment;
        }
        // 作者花名
        $author = "测试";
        //生成config
//        $this->generateTable($author, $moduleName, $comment, $tableName);
        // 生成模型
        $this->generateModel($author, $moduleName, $comment, $tableName);

        // 生成服务类
        $this->generateService($author, $moduleName, $comment, $tableName);
        // 生成控制器
//        $this->generateController($author, $controllerName,$moduleName, $comment, $tableName);
        //生成前端控制器
//        $this->generateIndexController($author, $controllerIndexName,$moduleName, $comment, $tableName);
//        $this->generateTable($author, $moduleName, $comment, $tableName);
//        $this->generateVueFormIndex($author, $controllerIndexName,$moduleName, $comment, $tableName);
        // 生成列表文件
        $this->generateVueIndex($comment, $moduleName, $tableName);
        // 生成菜单
        $this->generateMenu(strtolower(str_replace('_', '', $tableName)), $menuName);

        return message("模块生成成功");
    }


    /**
     * 一键生成
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since 2020/11/15
     */
    public function generateconfig()
    {
        // 参数
        $param = $this->input;//request()->param();
        // 数据表名
        $name = getter($param, "name");
        if (!$name) {
            return message("数据表名称不能为空", false);
        }
        // 数据表名称
        $tableName = str_replace(DB_PREFIX, null, $name);
        // 模型名称
        $moduleName = str_replace(' ', null, ucwords(strtolower(str_replace('_', ' ', $tableName))));
        // 控制器名称
        $controllerName = ucfirst(strtolower(str_replace('_', '', $tableName)));
        // 控制器名称
        //$controllerIndexName = ucfirst(strtolower(str_replace('_', '', "font_".$tableName)));
        $controllerIndexName = ucfirst(strtolower(str_replace('_', '', $tableName)));
        // 数据表描述
        $comment = getter($param, "comment");
        $comment = explode("|", $comment)[0];
        if (!$comment) {
            return message("数据表名称不能为空", false);
        }
        // 设置表名
        $menuName = $comment;
        // 去除表描述中的`表`
        if (strpos($comment, "表") !== false) {
            $comment = str_replace("表", null, $comment);
            $menuName = $comment;
        }
        // 去除表描述中的`管理`
        if (strpos($comment, "管理") !== false) {
            $comment = str_replace("管理", null, $comment);
            $menuName = $comment;
        }
        // 作者花名
        $author = "测试";
        //生成config
        $this->generateTable($author, $moduleName, $comment, $tableName);

        // 生成列表文件
        $this->generateVueFormIndex($comment, $moduleName, $tableName);
        // 生成菜单
        $this->generateMenu(strtolower(str_replace('_', '', $tableName)), $menuName);

        return message("模块生成成功");
    }


    /**
     * 生成模型
     * @param $author 作者
     * @param $moduleName 模块名
     * @param $moduleTitle 模块标题
     * @param $tableName 数据表名
     * @author 牧羊人
     * @since 2020/7/15
     */
    public function generateModel($author, $moduleName, $moduleTitle, $tableName)
    {
        // 判断是否有图片
        $moduleImage = false;
        // 获取数据列表
        $columnList = $this->getColumnList(DB_PREFIX . "{$tableName}");
        if ($columnList) {
            foreach ($columnList as &$val) {
                // 图片字段处理
                if (strpos($val['columnName'], "cover") !== false ||
                    strpos($val['columnName'], "avatar") !== false ||
                    strpos($val['columnName'], "image") !== false ||
                    strpos($val['columnName'], "logo") !== false ||
                    strpos($val['columnName'], "pic") !== false) {
                    $val['columnImage'] = true;
                    $moduleImage = true;
                }
            }
        }
        // 参数
        $param = [
            'author' => $author,
            'since' => date('Y/m/d', time()),
            'moduleName' => $moduleName,
            'moduleTitle' => $moduleTitle,
            'tableName' => $tableName,
            'columnList' => $columnList,
            'moduleImage' => $moduleImage,
        ];
        // 存储目录
        $FILE_PATH = APP_PATH . '/common/model/';
        if (!is_dir($FILE_PATH)) {
            // 创建目录并赋予权限
            mkdir($FILE_PATH, 0777, true);
        }
        // 文件名
        $filename = $FILE_PATH . "/{$moduleName}.php";
        // 拆解参数
        extract($param);
        // 开启缓冲区
        ob_start();
        // 引入模板文件
        require(APP_PATH . '/common/templates/model.tpl.php');
        // 获取缓冲区内容
        $out = ob_get_clean();
        // 打开文件
        $f = fopen($filename, 'w');
        // 写入内容
        fwrite($f, "<?php " . $out);
        // 关闭
        fclose($f);
    }


    /**
     * 生成模型
     * @param $author 作者
     * @param $moduleName 模块名
     * @param $moduleTitle 模块标题
     * @param $tableName 数据表名
     * @author 牧羊人
     * @since 2020/7/15
     */
    public function generateTable($author, $moduleName, $moduleTitle, $tableName)
    {
        // 判断是否有图片
        $moduleImage = false;
        // 获取数据列表
        $columnList = $this->getColumnList(DB_PREFIX . "{$tableName}");
        if ($columnList) {
            foreach ($columnList as &$val) {
                // 图片字段处理
                if (strpos($val['columnName'], "cover") !== false ||
                    strpos($val['columnName'], "avatar") !== false ||
                    strpos($val['columnName'], "image") !== false ||
                    strpos($val['columnName'], "logo") !== false ||
                    strpos($val['columnName'], "pic") !== false) {
                    $val['columnImage'] = true;
                    $moduleImage = true;
                }
            }
        }
//        //unset($columnList["is_online"]);
//        // 参数
//        $param = [
//            'author' => $author,
//            'since' => date('Y/m/d', time()),
//            'moduleName' => $moduleName,
//            'moduleTitle' => $moduleTitle,
//            'tableName' => $tableName,
//            'columnList' => $columnList,
//            'moduleImage' => $moduleImage,
//        ];
        $arr = [];
        foreach ($columnList as $k => $v) {
            if ($v['columnName'] != "mark" &&$v['columnName'] != "create_time" &&$v['columnName'] != "update_time" ) {
                $row = [
                    "type" => "input",
                    "title" => $v["columnComment"],
                    "field" => $v["columnName"],
                    "field_type" => $v["dataType"],
                    "props" => [
                        "multiple"=>"false",
                        "placeholder"=>"请输入".$v["columnComment"],
                    ],
                    "validate"=>[
                        "type"=>"string",
                        "required"=>"true",
                        "message"=>"请输入".$v["columnComment"],
                    ]
                ];
                if ($v["dataType"] == "tinyint" || $v["dataType"] == "int") {
                    // $row["validate"]["type"] = "number";
                }

                if ($v["columnName"] == "id") {
                    $row["type"] = "hidden";
                } else if ($v["dataType"] == "tinyint") {
                    $row["type"] = "radio";
                    $row["props"]["placeholder"] = "请选择".$v["columnComment"];
                    // $row["validate"]["message"] = "请选择".$v["columnComment"];
                    unset($row["validate"]);
                }
                //print_r($v["columnValue"]);
                if (isset($v["columnValue"])) {
                    $row["options"] =[];
                    foreach ($v["columnValue"] as $option){
                        if($option["value"]!="" && $option["name"]){
                            $row["options"][] =[
                                "value"=>(int)$option["value"],
                                "label"=>$option["name"],
                            ];
                        }
                    }
                }
                $arr[] = $row;
            }

        }


        // 存储目录
        $FILE_PATH = ROOT_PATH . 'config/form/';
        if (!is_dir($FILE_PATH)) {
            // 创建目录并赋予权限
            mkdir($FILE_PATH, 0777, true);
        }

        // exit;
        // 文件名
        $filename = $FILE_PATH . "/{$tableName}.php";

        $f = fopen($filename, 'w');
        // 写入内容
        $text = ' $rows=' . var_export($arr, true) . '; return $rows;';
        fwrite($f, "<?php " . $text);
        // 关闭
        fclose($f);

        // print_r($columnList);


    }


    /**
     * 生成服务类
     * @param $author 作者
     * @param $moduleName 模块名
     * @param $moduleTitle 模块标题
     * @param $tableName 数据表
     * @since 2020/7/15
     * @author 牧羊人
     */
    public function generateService($author, $moduleName, $moduleTitle, $tableName)
    {
        // 判断是否有图片
        $moduleImage = false;
        // 查询条件
        $queryList = [];
        // 获取数据列表
        $columnList = $this->getColumnList(DB_PREFIX . "{$tableName}");
        if ($columnList) {
            foreach ($columnList as &$val) {
                // 图片字段处理
                if (strpos($val['columnName'], "cover") !== false ||
                    strpos($val['columnName'], "avatar") !== false ||
                    strpos($val['columnName'], "image") !== false ||
                    strpos($val['columnName'], "logo") !== false ||
                    strpos($val['columnName'], "pic") !== false) {
                    $val['columnImage'] = true;
                    $moduleImage = true;
                }
                // 下拉筛选
                if (isset($val['columnValue'])) {
                    $queryList[] = $val;
                }
                // 名称
                if ($val['columnName'] == "name") {
                    $queryList[] = $val;
                }
                // 标题
                if ($val['columnName'] == "title") {
                    $queryList[] = $val;
                }
            }
        }

        // 参数
        $param = [
            'author' => $author,
            'since' => date('Y/m/d', time()),
            'moduleName' => $moduleName,
            'moduleTitle' => $moduleTitle,
            'columnList' => $columnList,
            'moduleImage' => $moduleImage,
            'queryList' => $queryList,
        ];
        // 存储目录
        $FILE_PATH = APP_PATH . '/common/service/';
        if (!is_dir($FILE_PATH)) {
            // 创建目录并赋予权限
            mkdir($FILE_PATH, 0777, true);
        }
        // 文件名
        $filename = $FILE_PATH . "/{$moduleName}Service.php";
        // 拆解参数
        extract($param);
        // 开启缓冲区
        ob_start();
        // 引入模板文件
        require(APP_PATH . '/common/templates/service.tpl.php');
        // 获取缓冲区内容
        $out = ob_get_clean();
        // 打开文件
        $f = fopen($filename, 'w');
        // 写入内容
        fwrite($f, "<?php " . $out);
        // 关闭
        fclose($f);
    }

    /**
     * 生成控制器
     * @param $author 作者
     * @param $moduleName 模块名
     * @param $moduleTitle 模块标题
     * @param $tableName 数据表名
     * @since 2020/7/15
     * @author 牧羊人
     */
    public function generateController($author, $controllerName, $moduleName, $moduleTitle, $tableName)
    {
        // 获取数据列表
        $columnList = $this->getColumnList(DB_PREFIX . "{$tableName}");
        // 参数
        $param = [
            'author' => $author,
            'since' => date('Y/m/d', time()),
            'controllerName' => $controllerName,
            'moduleName' => $moduleName,
            'moduleTitle' => $moduleTitle,
            'columnList' => $columnList,
        ];
        // 存储目录
        $FILE_PATH = APP_PATH . '/adminapi/controller/';
        if (!is_dir($FILE_PATH)) {
            // 创建目录并赋予权限
            mkdir($FILE_PATH, 0777, true);
        }
        // 文件名
        $filename = $FILE_PATH . "/{$param['controllerName']}.php";
        // 拆解参数
        extract($param);
        // 开启缓冲区
        ob_start();
        // 引入模板文件
        require(APP_PATH . '/common/templates/controller.tpl.php');
        // 获取缓冲区内容
        $out = ob_get_clean();
        // 打开文件
        $f = fopen($filename, 'w');
        // 写入内容
        fwrite($f, "<?php " . $out);
        // 关闭
        fclose($f);
    }

    /**
     * 生成前端控制器
     * @param $author 作者
     * @param $moduleName 模块名
     * @param $moduleTitle 模块标题
     * @param $tableName 数据表名
     * @since 2020/7/15
     * @author 牧羊人
     */
    public function generateIndexController($author, $controllerName, $moduleName, $moduleTitle, $tableName)
    {
        // 获取数据列表
        $columnList = $this->getColumnList(DB_PREFIX . "{$tableName}");
        // 参数
        $param = [
            'author' => $author,
            'since' => date('Y/m/d', time()),
            'controllerName' => $controllerName,
            'moduleName' => $moduleName,
            'moduleTitle' => $moduleTitle,
            'columnList' => $columnList,
        ];
        // 存储目录
        $FILE_PATH = APP_PATH . '/api/controller/';
        if (!is_dir($FILE_PATH)) {
            // 创建目录并赋予权限
            mkdir($FILE_PATH, 0777, true);
        }
        // 文件名
        $filename = $FILE_PATH . "/{$param['controllerName']}.php";
        // 拆解参数
        extract($param);
        // 开启缓冲区
        ob_start();
        // 引入模板文件
        require(APP_PATH . '/common/templates/indexcontroller.tpl.php');
        // 获取缓冲区内容
        $out = ob_get_clean();
        // 打开文件
        $f = fopen($filename, 'w');
        // 写入内容
        fwrite($f, "<?php " . $out);
        // 关闭
        fclose($f);
    }

    /**
     * 生成列表文件
     * @param $moduleTitle 模块标题
     * @param $tableName 数据表名
     * @author 牧羊人
     * @since 2020/7/15
     */
    public function generateVueIndex($moduleTitle, $moduleName, $tableName)
    {
        // 获取数据列表
        $columnList = $this->getColumnList(DB_PREFIX . "{$tableName}");
        $queryList = [];
        if ($columnList) {
            foreach ($columnList as $val) {
                // 下拉筛选
                if (isset($val['columnValue'])) {
                    $queryList[] = $val;
                }
                // 名称
                if ($val['columnName'] == "name") {
                    $queryList[] = $val;
                }
                // 标题
                if ($val['columnName'] == "title") {
                    $queryList[] = $val;
                }
            }
        }
        // 获取编辑表单数据源
        // 剔除非表单呈现字段
        $arrayList = [];
        $tempList = [];
        $rowList = [];
        $columnSplit = false;
        if ($columnList) {
            foreach ($columnList as $val) {
                // 记录ID
                if ($val['columnName'] == "id") {
                    continue;
                }
                // 创建人
                if ($val['columnName'] == "create_user") {
                    continue;
                }
                // 创建时间
                if ($val['columnName'] == "create_time") {
                    continue;
                }
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
        if (count($arrayList) + count($tempList) + count($rowList) > 105) {
            $dataList = [];
            // 分两个一组
            $dataList = array_chunk($arrayList, 2);
            // 图片
            if (count($tempList) > 0) {
                array_unshift($dataList, $tempList);
            }
            // 多行文本
            if (count($rowList) > 0) {
                foreach ($rowList as $val) {
                    $dataList[][] = $val;
                }
            }
            $columnList = $dataList;
            $columnSplit = true;
        } else {
            $dataList = $arrayList;
//            // 图片
//            if (count($tempList) > 0) {
//                array_unshift($dataList, $tempList);
//            }
            // 多行文本
            if (count($rowList) > 0) {
                foreach ($rowList as $val) {
                    $dataList[][] = $val;
                }
            }
            $columnList = $dataList;
            $columnSplit = false;
        }

        // 参数
        $param = [
            'moduleName' => $moduleName,
            'moduleName2' => strtolower($moduleName),
            'moduleTitle' => $moduleTitle,
            'queryList' => $queryList,
            'columnList' => $columnList,
        ];
        // 存储目录
        if (strpos($tableName, "_") !== false) {
            $tableName = str_replace('_', null, $tableName);
        }
        $FILE_PATH = ROOT_PATH . '/evui/src/views/system/' . strtolower($tableName);
        if (!is_dir($FILE_PATH)) {
            // 创建目录并赋予权限
            mkdir($FILE_PATH, 0777, true);
        }
        // 文件名
        $filename = $FILE_PATH . "/index.vue";
        // 拆解参数
        extract($param);
        // 开启缓冲区
        ob_start();
        // 引入模板文件
        require(APP_PATH . '/common/templates/index.vue.php');
        // 获取缓冲区内容
        $out = ob_get_clean();
        // 打开文件
        $f = fopen($filename, 'w');
        // 写入内容
        fwrite($f, $out);
        // 关闭
        fclose($f);
    }


    /**
     * 生成列表文件
     * @param $moduleTitle 模块标题
     * @param $tableName 数据表名
     * @author 牧羊人
     * @since 2020/7/15
     */
    public function generateVueFormIndex($moduleTitle, $moduleName, $tableName)
    {
        // 获取数据列表
        $columnList = $this->getColumnList(DB_PREFIX . "{$tableName}");
        $queryList = [];
        if ($columnList) {
            foreach ($columnList as $val) {
                // 下拉筛选
                if (isset($val['columnValue'])) {
                    $queryList[] = $val;
                }
                // 名称
                if ($val['columnName'] == "name") {
                    $queryList[] = $val;
                }
                // 标题
                if ($val['columnName'] == "title") {
                    $queryList[] = $val;
                }
            }
        }
        // 获取编辑表单数据源
        // 剔除非表单呈现字段
        $arrayList = [];
        $tempList = [];
        $rowList = [];
        $columnSplit = false;
        if ($columnList) {
            foreach ($columnList as $val) {
                // 记录ID
                if ($val['columnName'] == "id") {
                    continue;
                }
                // 创建人
                if ($val['columnName'] == "create_user") {
                    continue;
                }
                // 创建时间
                if ($val['columnName'] == "create_time") {
                    continue;
                }
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
        if (count($arrayList) + count($tempList) + count($rowList) > 105) {
            $dataList = [];
            // 分两个一组
            $dataList = array_chunk($arrayList, 2);
            // 图片
            if (count($tempList) > 0) {
                array_unshift($dataList, $tempList);
            }
            // 多行文本
            if (count($rowList) > 0) {
                foreach ($rowList as $val) {
                    $dataList[][] = $val;
                }
            }
            $columnList = $dataList;
            $columnSplit = true;
        } else {
            $dataList = $arrayList;
//            // 图片
//            if (count($tempList) > 0) {
//                array_unshift($dataList, $tempList);
//            }
            // 多行文本
            if (count($rowList) > 0) {
                foreach ($rowList as $val) {
                    $dataList[][] = $val;
                }
            }
            $columnList = $dataList;
            $columnSplit = false;
        }

        // 参数
        $param = [
            'moduleName' => $moduleName,
            'moduleName2' => strtolower($moduleName),
            'moduleTitle' => $moduleTitle,
            'queryList' => $queryList,
            'columnList' => $columnList,
        ];
        // 存储目录
        if (strpos($tableName, "_") !== false) {
            $tableName = str_replace('_', null, $tableName);
        }
        $FILE_PATH = ROOT_PATH . '/evui/src/views/system/' . strtolower($tableName);
        if (!is_dir($FILE_PATH)) {
            // 创建目录并赋予权限
            mkdir($FILE_PATH, 0777, true);
        }
        // 文件名
        $filename = $FILE_PATH . "/index.vue";
        // 拆解参数
        extract($param);
        // 开启缓冲区
        ob_start();
        // 引入模板文件
        require(APP_PATH . '/common/templates/index_createform.vue.php');
        // 获取缓冲区内容
        $out = ob_get_clean();
        // 打开文件
        $f = fopen($filename, 'w');
        // 写入内容
        fwrite($f, $out);
        // 关闭
        fclose($f);
    }


    /**
     * 获取表字段列表
     * @param $tableName 数据表名
     * @return array
     * @author 牧羊人
     * @since 2020/7/15
     */
    public function getColumnList($tableName)
    {
        // 获取表列字段信息
        $columnList = Db::query("SELECT COLUMN_NAME,COLUMN_DEFAULT,DATA_TYPE,COLUMN_TYPE,COLUMN_COMMENT FROM information_schema.`COLUMNS` where TABLE_SCHEMA = '" . env('database.database') . "' AND TABLE_NAME = '{$tableName}'");
        $fields = [];
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
        return $fields;
    }

    /**
     * 生成模块菜单
     * @param $moduleName 模块名称
     * @param $moduleTitle 模块标题
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @since 2020/7/16
     * @author 牧羊人
     */
    public function generateMenu($moduleName, $moduleTitle)
    {
        // 查询已存在的菜单
        $menuModel = new Menu();
        $info = $menuModel->getOne([
            ['permission', '=', "sys:{$moduleName}:view"],
        ]);
        $data = [
            'id' => isset($info['id']) ? intval($info['id']) : 0,
            'title' => $moduleTitle,
            'icon' => 'el-icon-house',
            'path' => "/system/{$moduleName}",
            'component' => "/system/{$moduleName}",
            'pid' => 154,
            'type' => 0,
            'permission' => "sys:{$moduleName}:view",
        ];
        $result = $menuModel->edit($data);
        if ($result) {
            // 去除表描述中的`管理`
            if (strpos($moduleTitle, "管理") !== false) {
                $moduleTitle = str_replace("管理", null, $moduleTitle);
            }
            // 创建节点
            $funcList = [1, 5, 10, 15, 25, 30];
            foreach ($funcList as $val) {
                $item = [];
                if ($val == 1) {
                    // 查询列表
                    $menuModel = new Menu();
                    $info = $menuModel->getOne([
                        ['pid', '=', $result],
                        ['title', '=', "查询" . $moduleTitle]
                    ]);
                    $item = [
                        'id' => isset($info['id']) ? intval($info['id']) : 0,
                        'title' => "查询" . $moduleTitle,
                        'permission' => "sys:{$moduleName}:index",
                        'pid' => $result,
                        'type' => 1,
                        'status' => 1,
                        'sort' => $val,
                    ];
                } else if ($val == 5) {
                    // 添加
                    $menuModel = new Menu();
                    $info = $menuModel->getOne([
                        ['pid', '=', $result],
                        ['title', '=', "添加" . $moduleTitle]
                    ]);
                    $item = [
                        'id' => isset($info['id']) ? intval($info['id']) : 0,
                        'title' => "添加" . $moduleTitle,
                        'permission' => "sys:{$moduleName}:add",
                        'pid' => $result,
                        'type' => 1,
                        'status' => 1,
                        'sort' => $val,
                    ];
                } else if ($val == 10) {
                    // 修改
                    $menuModel = new Menu();
                    $info = $menuModel->getOne([
                        ['pid', '=', $result],
                        ['title', '=', "修改" . $moduleTitle]
                    ]);
                    $item = [
                        'id' => isset($info['id']) ? intval($info['id']) : 0,
                        'title' => "修改" . $moduleTitle,
                        'permission' => "sys:{$moduleName}:edit",
                        'pid' => $result,
                        'type' => 1,
                        'status' => 1,
                        'sort' => $val,
                    ];
                } else if ($val == 15) {
                    // 删除
                    $menuModel = new Menu();
                    $info = $menuModel->getOne([
                        ['pid', '=', $result],
                        ['title', '=', "删除" . $moduleTitle]
                    ]);
                    $item = [
                        'id' => isset($info['id']) ? intval($info['id']) : 0,
                        'title' => "删除" . $moduleTitle,
                        'permission' => "sys:{$moduleName}:delete",
                        'pid' => $result,
                        'type' => 1,
                        'status' => 1,
                        'sort' => $val,
                    ];
                } else if ($val == 20) {
                    // 状态
                    $menuModel = new Menu();
                    $info = $menuModel->getOne([
                        ['pid', '=', $result],
                        ['title', '=', "设置状态"]
                    ]);
                    $item = [
                        'id' => isset($info['id']) ? intval($info['id']) : 0,
                        'title' => "设置状态",
                        'permission' => "sys:{$moduleName}:status",
                        'pid' => $result,
                        'type' => 1,
                        'status' => 1,
                        'sort' => $val,
                    ];
                } else if ($val == 25) {
                    // 批量删除
                    $info = $menuModel->getOne([
                        ['pid', '=', $result],
                        ['title', '=', "批量删除"]
                    ]);
                    $item = [
                        'id' => isset($info['id']) ? intval($info['id']) : 0,
                        'title' => "批量删除",
                        'permission' => "sys:{$moduleName}:dall",
                        'pid' => $result,
                        'type' => 1,
                        'status' => 1,
                        'sort' => $val,
                    ];
                }
                if (empty($item['title'])) {
                    continue;
                }
                $menuModel = new Menu();
                $menuModel->edit($item);
            }
        }
    }

}
