<?php

namespace app\common\model;

use think\facade\Db;

class ConfigSc extends BaseModel
{

    public static function update1()
    {

        $tables = Db::query('show tables');
        $tables = array_column($tables, 'Tables_in_quant');
        foreach ($tables as $key => $val) {
            $ex = explode("_", $val);
            //if ($ex[0] == "shop") {
            $sql = "SELECT COLUMN_COMMENT comment,COLUMN_NAME attr  FROM
    information_schema.COLUMNS
WHERE TABLE_NAME = '{$val}'";

            $data = Db::query($sql);
            foreach ($data as $k => $v) {
                $data_val[$val][] = $v["attr"];
                $data_comment[$v["attr"]][] = $v["comment"];

            }
            //print_r($data_comment);
            // }
        }

//        print_r($data_val);
//        exit;
//
//        foreach ($data_val as $table => $val) {
//            if (in_array($table, ["yrnet_purchase_order_detail_cg", "yrnet_purchase_order_detail", "yrnet_order", "yrnet_purchase_order_examine"])) {
//                $a = Db::table($table)->select();
//                foreach ($a as $k => $v) {
//                    Db::table($table)->where(["id" => $v["id"]])->update(return_create_user([], $v["create_user_id"]));
//                    Db::table($table)->where(["id" => $v["id"]])->update(return_update_user([], $v["update_user_id"]));
//                }
//            }
//        }

        //      exit;


        foreach ($data_val as $table => $val) {

             if (!in_array($table, ["yrnet_menu", "yrnet_admin_user", "yrnet_admin_user_role", "yrnet_admin_config", "yrnet_role", "yrnet_role_menu", "yrnet_category", "yrnet_city", "yrnet_dept", "yrnet_dict", "yrnet_dict_data", "yrnet_level"])) {
                 if (!in_array("mark", $val)) {
                     Db::query("alter table {$table} add `mark` tinyint(1) DEFAULT '1' COMMENT '有效标识'");
                 }
                 if (!in_array("create_time", $val)) {
                     Db::query("alter table {$table} add `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'");
                 }
                 if (!in_array("update_time", $val)) {
                     Db::query("alter table {$table} add `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'");
                 }

                 if (in_array("add_time", $val)) {
                     Db::query("ALTER TABLE {$table} drop COLUMN `add_time`");
                 }
             }
//                if (!in_array("is_upload_pic", $val)) {
//                    Db::query("alter table {$table} add `is_upload_pic` tinyint(1) DEFAULT '0' COMMENT '是否上传图片'");
//                }
//                if (in_array("examine_status", $val)) {
//                    //0 未审核 1待审核 2审核中 3审核通过 4审核失败 5 审核退回 6作废
//                    Db::query("alter table {$table} MODIFY COLUMN `examine_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态：0未审核 1待审核 2审核通过 3审核失败 4 审核退回 5作废（mark=0是作废） '");
//                }
//                if (!in_array("examine_reason", $val)) {
//                    Db::query("alter table {$table} MODIFY `examine_reason` varchar(255) DEFAULT NULL COMMENT '审核备注 '");
//                }

//
//                if (!in_array("examine_status", $val)) {
//                    //0 未审核 1待审核 2审核中 3审核通过 4审核失败 5 审核退回 6作废
//                    Db::query("alter table {$table} add `examine_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态 0未审核 1待审核 2审核中 3审核通过 4审核失败 5 审核退回 6作废（mark=0是作废） '");
//                }
//                if (!in_array("examine_reason", $val)) {
//                    Db::query("alter table {$table} add `examine_reason` varchar(255) DEFAULT NULL COMMENT '审核备注 '");
//                }


//                if (in_array("admin_user_id", $val)) {
//                    Db::query("ALTER TABLE {$table} drop COLUMN `admin_user_id`");
//                }
//                if (in_array("update_admin_user_id", $val)) {
//                    Db::query("ALTER TABLE {$table} drop COLUMN `update_admin_user_id`");
//                }
//
//                if (in_array("examine_time", $val)) {
//                    Db::query("ALTER TABLE {$table} drop COLUMN `examine_time`");
//                }
//                if (in_array("examine_admin_user_id", $val)) {
//                    Db::query("ALTER TABLE {$table} drop COLUMN `examine_admin_user_id`");
//                }
//
//                //ALTER TABLE `tableName`  `columeName` DATETIME NOT NULL DEFAULT '0001-00-00 00:00:00' COMMENT '时间' AFTER `anotherColumeName`;
//
//                if (in_array("create_user_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `create_user_id` int(11) unsigned  DEFAULT '0' COMMENT '员工id'  ");
//                }
//                if (in_array("belong_clique_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `belong_clique_id` int(11) unsigned  DEFAULT '0' COMMENT '所属集团id'  ");
//                }
//                if (in_array("belong_company_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `belong_company_id` int(11) unsigned  DEFAULT '0' COMMENT '所属公司id'  ");
//                }
//
//                if (in_array("belong_dept_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `belong_dept_id` int(11) unsigned  DEFAULT '0' COMMENT '所属部门id'  ");
//                }
//
//                if (in_array("belong_group_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `belong_group_id` int(11) unsigned  DEFAULT '0' COMMENT '所属组别id'  ");
//                }
//
//
//                if (in_array("update_user_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `update_user_id` int(11) unsigned  DEFAULT '0' COMMENT '操作员工id'  ");
//                }
//                if (in_array("update_belong_clique_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `update_belong_clique_id` int(11) unsigned  DEFAULT '0' COMMENT '操作员集团id'  ");
//                }
//                if (in_array("update_belong_company_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `update_belong_company_id` int(11) unsigned  DEFAULT '0' COMMENT '操作员公司id'  ");
//                }
//
//                if (in_array("update_belong_dept_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `update_belong_dept_id` int(11) unsigned  DEFAULT '0' COMMENT '操作员部门id' ");
//                }
//
//                if (in_array("update_belong_group_id", $val)) {
//                    Db::query("alter table {$table} MODIFY COLUMN `update_belong_group_id` int(11) unsigned  DEFAULT '0' COMMENT '操作员组别id' ");
//                }

//                echo $table;
//                print_r($val);
//                exit;
//                if (!in_array("create_time", $val)) {
//                    Db::query("alter table {$table} add `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'");
//                }
//                if (!in_array("update_time", $val)) {
//                    Db::query("alter table {$table} add `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'");
//                }
//
//                if (!in_array("create_user_id", $val)) {
//                    Db::query("alter table {$table} add `create_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工id'");
//                }
//                if (!in_array("create_user_name", $val)) {
//                    Db::query("alter table {$table} add `create_user_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '员工编号'");
//                }
//
//                if (!in_array("belong_clique_id", $val)) {
//                    Db::query("alter table {$table} add `belong_clique_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属员工集团id'");
//                }
//                if (!in_array("belong_clique", $val)) {
//                    Db::query("alter table {$table} add `belong_clique` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '所属员工集团名称'");
//                }
//
//
//                if (!in_array("belong_company_id", $val)) {
//                    Db::query("alter table {$table} add `belong_company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属员工公司id'");
//                }
//                if (!in_array("belong_company", $val)) {
//                    Db::query("alter table {$table} add `belong_company` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '所属员工公司名称'");
//                }
//
//
//                if (!in_array("belong_dept_id", $val)) {
//                    Db::query("alter table {$table} add `belong_dept_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属员工部门id'");
//                }
//                if (!in_array("belong_dept", $val)) {
//                    Db::query("alter table {$table} add `belong_dept` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '所属员工部门名称'");
//                }
//
//
//                if (!in_array("belong_group_id", $val)) {
//                    Db::query("alter table {$table} add `belong_group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属员工组别id'");
//                }
//                if (!in_array("belong_group", $val)) {
//                    Db::query("alter table {$table} add `belong_group` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '所属员工组别名称'");
//                }
//
//
//                if (!in_array("update_user_id", $val)) {
//                    Db::query("alter table {$table} add `update_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员工id'");
//                }
//                if (!in_array("update_user_name", $val)) {
//                    Db::query("alter table {$table} add `update_user_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '操作员编号'");
//                }
//
//
//                if (!in_array("update_belong_clique_id", $val)) {
//                    Db::query("alter table {$table} add `update_belong_clique_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员工集团id'");
//                }
//                if (!in_array("update_belong_clique", $val)) {
//                    Db::query("alter table {$table} add `update_belong_clique` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '操作员工集团名称'");
//                }
//
//
//                if (!in_array("update_belong_company_id", $val)) {
//                    Db::query("alter table {$table} add `update_belong_company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员工公司id'");
//                }
//                if (!in_array("update_belong_company", $val)) {
//                    Db::query("alter table {$table} add `update_belong_company` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '操作员工公司名称'");
//                }
//
//
//                if (!in_array("update_belong_dept_id", $val)) {
//                    Db::query("alter table {$table} add `update_belong_dept_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员工部门id'");
//                }
//                if (!in_array("update_belong_dept", $val)) {
//                    Db::query("alter table {$table} add `update_belong_dept` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '操作员工部门名称'");
//                }
//
//
//                if (!in_array("update_belong_group_id", $val)) {
//                    Db::query("alter table {$table} add `update_belong_group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员工组别id'");
//                }
//                if (!in_array("update_belong_group", $val)) {
//                    Db::query("alter table {$table} add `update_belong_group` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  COMMENT '操作员工组别名称'");
//                }

        }

    }

    // print_r($tables);
    //exit;
    //   }
}
