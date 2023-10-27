<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
    <?php foreach ($queryList as $val) {?>
        <?php if (isset($val['columnValue'])) {?>
            <?php if (isset($val['columnSwitch']) && $val['columnSwitch']) {?>

            <?php } elseif ($val['dataType'] == 'bigint' || $val['dataType'] == 'int' || $val['dataType'] == 'smallint' || $val['dataType'] == 'tinyint') {?>

            <el-col :md="6" :sm="12">
              <el-form-item label="<?php echo $val['columnComment']?>:">
                <el-select v-model="table.where.<?php echo $val['columnName']?>" placeholder="请选择<?php echo $val['columnComment']?>" clearable class="ele-fluid">
                  <?php foreach ($val['columnValue'] as $v) { ?>
                    <el-option label="<?php echo $v["name"]?>" value="<?php echo $v["value"]?>"/>
                  <?php } ?>
                </el-select>
              </el-form-item>
            </el-col>
            <?php } ?>
        <?php } else {?>

            <el-col :md="6" :sm="12">
              <el-form-item label="<?php echo $val['columnComment']?>:">
                <el-input v-model="table.where.<?php echo $val['columnName']?>" placeholder="请输入<?php echo $val['columnComment']?>" clearable/>
              </el-form-item>
            </el-col>
        <?php } ?>
    <?php } ?>
  <el-col :md="6" :sm="12">
            <div class="ele-form-actions">
              <el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search" class="ele-btn-icon">查询
              </el-button>
              <el-button @click="(table.where={})&&$refs.table.reload()">重置</el-button>
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 操作按钮 -->
      <div class="ele-table-tool ele-table-tool-default">
        <el-button @click="create()" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:<?php echo $moduleName2?>:add')">添加
        </el-button>
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:<?php echo $moduleName2?>:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
    <?php foreach ($columnList as $val) {?>
      <?php if ($val['columnName'] != 'id' && $val['columnName'] != 'create_user' && $val['columnName'] != 'create_time' && $val['columnName'] != 'update_time' && $val['columnName'] != 'mark') {?>
        <?php if (isset($val['columnSwitch']) && $val['columnSwitch']) {?>

          <el-table-column prop="<?php echo $val['columnName']?>" label="<?php echo $val['columnComment']?>" sortable="custom" :resizable="false" min-width="120">
              <template slot-scope="{row}">
                  <el-switch v-model="row.<?php echo $val['columnName']?>" @change="<?php echo $val['columnSwitchName']?>(row)" :active-value="1" :inactive-value="2"/>
              </template>
          </el-table-column>
        <?php } elseif (isset($val['columnImage']) && $val['columnImage']) {?>

          <el-table-column label="<?php echo $val['columnComment']?>" min-width="100" align="center">
              <template slot-scope="{row}">
                  <el-avatar shape="square" :size="35" :src="row.<?php echo $val['columnName']?>"/>
              </template>
          </el-table-column>
        <?php } else {?>

          <el-table-column prop="<?php echo $val['columnName']?>" label="<?php echo $val['columnComment']?>" sortable="custom" show-overflow-tooltip min-width="120"/>
        <?php } ?>
      <?php } ?>
    <?php } ?>

          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">
            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">
            <template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:<?php echo $moduleName2?>:edit')">修改</el-link>
              <el-popconfirm title="确定要删除此<?php echo $moduleTitle?>吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:<?php echo $moduleName2?>:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改<?php echo $moduleTitle?>':'添加<?php echo $moduleTitle?>'" :visible.sync="showEdit" width="450px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
        <template>
            <form-create
                    v-model="fapi"
                    :rule="rule"
                    :option="option"
            ></form-create>
        </template>
      <div slot="footer">
        <el-button @click="showEdit=false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
    import { mapGetters } from "vuex";
    export default {
        name: "SysDevice",
        data() {
            return {
                fapi: null,
                rule: [],
                option: {},
                table: {url: '/<?php echo $moduleName2?>/index', where: {}},  // 表格配置
                choose: [],  // 表格选中数据
                showEdit: false,  // 是否显示表单弹窗
                editForm: {},  // 表单数据
            }
        },
        computed: {
            ...mapGetters(["permission"]),
        },
        mounted() {
        },
        methods: {
            create(){
                this.$http.get('/<?php echo $moduleName2?>/createform').then(res => {
                    if (res.data.code === 0) {
                        this.rule = res.data.data.rule;
                        this.option = res.data.data.option;
                        this.showEdit = true;
                    } else {
                        this.$message.error(res.data.msg);
                    }
                }).catch(e => {
                    this.$message.error(e.message);
                });
            },
            /**
             * 显示编辑
             */
            edit(row) {
                this.$http.get('/<?php echo $moduleName2?>/editform?id=' + row.id).then(res => {
                    if (res.data.code === 0) {
                        this.rule = res.data.data.rule;
                        this.option = res.data.data.option;
                        this.editForm.id = row.id;
                        this.showEdit = true;
                    } else {
                        this.$message.error(res.data.msg);
                    }
                }).catch(e => {
                    this.$message.error(e.message);
                });
            },
            /**
             * 保存编辑
             */
            save() {
                this.fapi.submit((formData, fapi)=>{
                    const loading = this.$loading({lock: true});
                    this.$http.post('/<?php echo $moduleName2?>/edit', formData).then(res => {
                        loading.close();
                        if (res.data.code === 0) {
                            this.showEdit = false;
                            this.$message({type: 'success', message: res.data.msg});
                            this.$refs.table.reload();
                        } else {
                            this.$message.error(res.data.msg);
                        }
                    }).catch(e => {
                        loading.close();
                        this.$message.error(e.message);
                    });
                })
            },
            /**
             * 刪除(批量刪除)
             */
            remove(row) {
                console.log(row)
                if (!row) {  // 批量删除
                    if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
                    let ids = this.choose.map(d => d.id);
                    this.$confirm('确定要删除选中的设备吗?', '提示', {type: 'warning'}).then(() => {
                        const loading = this.$loading({lock: true});
                        this.$http.post('/<?php echo $moduleName2?>/delete', {id: ids}).then(res => {
                            loading.close();
                            if (res.data.code === 0) {
                                this.$message({type: 'success', message: res.data.msg});
                                this.$refs.table.reload();
                            } else {
                                this.$message.error(res.data.msg);
                            }
                        }).catch(e => {
                            loading.close();
                            this.$message.error(e.message);
                        });
                    }).catch(() => 0);
                } else {  // 单个删除
                    const loading = this.$loading({lock: true});
                    this.$http.post('/<?php echo $moduleName2?>/delete', {id:row.id}).then(res => {
                        loading.close();
                        if (res.data.code === 0) {
                            this.$message({type: 'success', message: res.data.msg});
                            this.$refs.table.reload();
                        } else {
                            this.$message.error(res.data.msg);
                        }
                    }).catch(e => {
                        loading.close();
                        this.$message.error(e.message);
                    });
                }
            },
        }
    }
</script>

<style scoped>
    .ele-block >>> .el-upload, .ele-block >>> .el-upload-dragger {
        width: 100%;
    }
</style>
