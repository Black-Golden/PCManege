
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;


/**
 * <?php echo $moduleTitle?>-模型
 * @author <?php echo $author?>

 * @since: <?php echo $since?>

 * Class <?php echo $moduleName?>

 * @package app\adminapi\model
 */
class <?php echo $moduleName?> extends BaseModel
{
    // 设置数据表名
    public $name = "<?php echo $tableName?>";

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return \app\common\model\数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author <?php echo $author?>

     * @since: <?php echo $since?>

     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id); // TODO: Change the autogenerated stub
        if ($info) {
    <?php if ($columnList) {?>
    <?php foreach ($columnList as $val) { ?>
        <?php if (isset($val['columnImage']) && $val['columnImage']) {?>

            // <?php echo $val['columnComment']?>

            $info['<?php echo $val['columnName']?>'] = get_image_url($info['<?php echo $val['columnName']?>']);
        <?php } ?>
    <?php } ?>
    <?php } ?>

        }
        return $info;
    }


}