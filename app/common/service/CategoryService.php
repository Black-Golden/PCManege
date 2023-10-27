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
use mydogger\pinyin\Pinyin;

/**
 * 产品分类管理-服务类
 * @author 测试
 * @since: 2021/09/18
 * Class CategoryService
 * @package app\adminapi\service
 */
class CategoryService extends BaseService
{

    protected $customer_service;

    /**
     * 构造函数
     * LevelService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Category();
        $this->customer_service = new CustomerService();
    }

	/**
     * 获取数据列表
     * @return array
     * @since 2021/09/18
     * @author 测试
     */
    public function getList()
    {
        $param = $this->input;

        // 查询条件
        $map = [];

	
	    // 分类名称
        $name = isset($param['name']) ? trim($param['name']) : '';
        if ($name) {
            $map[] = ['name', 'like', "%{$name}%"];
        }

        $map[] = ['pid', '=', 0];
        $data = parent::getList($map); // TODO: Change the autogenerated stub


        $data = json_decode($data->getContent(),true);

        $id = array_column($data['data'],'id');

        $towArr = $this->model->where('mark','=',1)->whereIn('pid',$id)->select()->toArray();

        $newArr = [];
        foreach ($towArr as $value){
            $newArr[$value['pid']][] = $value;
        }

        foreach ($data['data'] as &$val){
            $val['two'] = isset($newArr[$val['id']]) ? $newArr[$val['id']] : [] ;
        }

        return  json($data);

        return parent::getList($map); // TODO: Change the autogenerated stub
    }

    /**
     * 添加主营分类
     */
    public function addCategory(){
        $param = $this->input;
        // 验证器 验证
        $validate = getValidate([
            'name' => 'require',
            'pid' => 'require',
        ],[
            'name.require' => '请填写分类名称',
            'pid.require' => '请选择父级分类',
        ]);

        if($validate['code'] == 1){
            return return_json($validate);
        }

        $param['type_id'] = 1;

        if($param['pid'] > 0){
            $param['type_id'] = 2;
        }
        // 姓名拼音获取
        $py = Pinyin::first($param['name']);

        $param['sort_name'] = strtoupper($py);

        // 逻辑判断
        // 判断该公司是否存在
        $CategoryName = $this->model->where(['name'=>$param['name']])->find();

        if($CategoryName){
            return return_json([
                'code' => 1,
                'msg' => '该分类名称已录入，无法重复录入'
            ]);
        }

        // 编辑
        if(isset($param['id'])){
            $param['update_time'] = time();

            $id = $param['id'];
            unset($param['id']);

            $py = strtoupper(Pinyin::first($param['name']));

            $update = ['name'=>$param['name'],'sort_name'=>$py];

            $res = $this->model
                ->where(['id'=>$id])
                ->update($update);
            $returnData = [
                'code' => 1,
                'msg' => '编辑失败'
            ];
            if($res){
                $returnData = [
                    'code' => 0,
                    'msg' => '编辑成功'
                ];
            }
        }else{
            // 添加
            $param['create_time'] = time();
            $param['update_time'] = time();

            $res = $this->model
                ->create($param);
            $returnData = [
                'code' => 1,
                'msg' => '添加失败'
            ];
            if($res){
                $returnData = [
                    'code' => 0,
                    'msg' => '添加成功'
                ];
            }
        }

        return return_json($returnData);
    }

    /**
     * 查询父级分类
     */
    public function selParent(){

        $param = $this->input;

        $pid = empty($param['pid']) ? 0 : $param['pid'] ;

        $other = $this->model->where(['mark'=>1,'pid'=>$pid])->select()->toArray();
        $returnData = [
            'code' => 0,
            'msg' => 'success',
            'data'=>$other
        ];
        return return_json($returnData);
    }

    /**
     * 主营分类导入
     */
    public function export()
    {
        // 验证器 验证
        $validate = getValidate([
            "files" => "require",
        ], [
            "files.require" => '请传入文件',
        ]);
        if ($validate["code"] == 1) {
            return return_json($validate);
        }
        $list = explode(",", $this->input["files"]);


        if ($list) {
            foreach ($list as $key1 => $val1) {
                $info = @file_get_contents(PUBLIC_PATH."/uploads" . $val1);
                if (!$info) {
                    return return_json([
                        "code" => 1,
                        "msg" => $this->input["files"] . "文件不存在"
                    ]);
                }

//                if (($handle = fopen(PUBLIC_PATH."/uploads". $val, "r")) !== FALSE) {
//                    setlocale(LC_ALL,array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
//
//                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//                        $num = count($data);
//                        for ($c = 0; $c < $num; $c++) {
//                            $data[$c] = mb_convert_encoding($str, "UTF-8", "GBK");
//
//                            $arr[] = $data[$c];//iconv('GBK','UTF-8',$data[$c]);
//                        }
//                        $datas[] = $arr;
//                        unset($arr);
//                    }
//                    fclose($handle);
//                }
                $datas = $this->customer_service->importExcel(PUBLIC_PATH."/uploads". $val1);
                unset($datas[0]);
                // 处理数据
                foreach ($datas as $key=>&$val){
                    // 循环处理数据
                    $main_category = $val[0];
                    $children_category = $val[1];
                    // 判断父类是否导入
                    $main_category_info = $this->model
                        ->where(['pid'=>0,'name'=>$main_category,'type_id'=>1,'mark'=>1])
                        ->find();

                    if(!$main_category_info){
                        $py = Pinyin::first($main_category);
                        $py = strtoupper($py);
                        // 添加主营大类
                        $main_category_info = $this->model
                            ->create([
                                'name' => $main_category,
                                'type_id' => 1,
                                'pid' => 0,
                                'sort_name' => $py
                            ]);
                    }
                    // 判断主营子类是否存在
                    $children_category_info = $this->model
                        ->where(['pid'=>$main_category_info['id'],'type_id'=>2,'name'=>$children_category,'mark'=>1])
                        ->find();
                    // 添加子类数据
                    if(!$children_category_info){
                        $py = Pinyin::first($children_category);
                        $py = strtoupper($py);
                        $this->model
                            ->create([
                                'name' => $children_category,
                                'type_id' => 2,
                                'pid' => $main_category_info['id'],
                                'sort_name' => $py
                            ]);
                    }
                }

            }

        }
        return  return_json([
            'code' => 0,
            'msg' => '导入成功'
        ]);
    }

    /**
     * 主营分类是否存在
     */
    public function categoryIsExists($name,$pid=0)
    {
        $info = $this->model
            ->where(['pid'=>$pid,'name'=>trim($name),'mark'=>1])
			// ->cache(true,10)
            ->find();

        return $info;
    }
}
