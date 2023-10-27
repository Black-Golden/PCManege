<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
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
<!--        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantcatprivate:add')">添加-->
<!--        </el-button>-->
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantcatprivate:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
                  
<!--          <el-table-column prop="cat_id" label="cat_id" sortable="custom" show-overflow-tooltip min-width="120"/>-->
                                

          <el-table-column prop="cat_name" label="用户名" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="parent_name" label="上级用户名" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
<!--          <el-table-column prop="depth" label="" sortable="custom" show-overflow-tooltip min-width="120"/>-->
<!--                                -->
<!--          <el-table-column prop="full_path" label="" sortable="custom" show-overflow-tooltip min-width="120"/>-->

          <el-table-column prop="create_time" label="创建时间" sortable="custom" show-overflow-tooltip min-width="120"/>
          <el-table-column prop="update_time" label="更新时间" sortable="custom" show-overflow-tooltip min-width="120"/>

<!--          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">-->
<!--            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>-->
<!--          </el-table-column>-->
<!--          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">-->
<!--            <template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>-->
<!--          </el-table-column>-->
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
<!--              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantcatprivate:edit')">修改</el-link>-->
              <el-link @click="edit_ck(row)" icon="el-icon-view" type="primary" :underline="false">记录
              </el-link>
              <el-popconfirm title="确定要删除此属性关联吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantcatprivate:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改属性关联':'修改属性关联'" :visible.sync="showEdit" width="450px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
                          
        <el-form-item label="：" prop="cat_id">
            <el-input-number v-model="editForm.cat_id" controls-position="right" :min="0"
                              placeholder="请输入" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="：" prop="user_id">
            <el-input-number v-model="editForm.user_id" controls-position="right" :min="0"
                              placeholder="请输入" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="：" prop="cat_name">
            <el-input v-model="editForm.cat_name" placeholder="请输入" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="：" prop="parent_id">
            <el-input-number v-model="editForm.parent_id" controls-position="right" :min="0"
                              placeholder="请输入" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="：" prop="depth">
            <el-input-number v-model="editForm.depth" controls-position="right" :min="0"
                              placeholder="请输入" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="：" prop="full_path">
            <el-input v-model="editForm.full_path" placeholder="请输入" clearable/>
        </el-form-item>
                          
      </el-form>
      <div slot="footer">
        <el-button @click="showEdit=false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </div>
    </el-dialog>
    <!--查看用户上级-->
    <el-dialog :title="'查看跟进记录'" :visible.sync="showEdit_ck" width="70%" :lock-scroll="false">
      <el-form :model="editForm" ref="" :rules="editRules" label-width="100px">
        <el-table :data="tableData1" style="width: 100% ;">
          <template slot-scope="{index}">

            <el-table-column prop="cat_name" label="上级用户名" width="500">
            </el-table-column>
            <el-table-column  label="上级层级" prop="level" min-width="200" />

          </template>
        </el-table>
      </el-form>
      <div slot="footer">
        <el-button @click="showEdit_ck=false">取消</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "SysQuantCatPrivate",
  data() {
    return {
      table: {url: '/quantcatprivate/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit_ck: false,
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
              
        cat_id: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                          
        user_id: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                                    
        cat_name: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                          
        parent_id: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                          
        depth: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                                    
        full_path: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
              
      },
    }
  },
computed: {
    ...mapGetters(["permission"]),
},
  mounted() {
  },
  methods: {
    edit_ck(row) {
      console.log(row);
      this.$http.post('/quantcatprivate/father', {
        id: row.cat_id,
      }).then(res => {
        if (res.data.code === 0) {
          this.showEdit_ck = true;
          this.tableData1 = res.data.data
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
      this.$http.get('/quantcatprivate/info?id=' + row.id).then(res => {
        if (res.data.code === 0) {
          this.editForm = res.data.data;
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
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({lock: true});
          this.$http.post('/quantcatprivate/edit', this.editForm).then(res => {
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
        } else {
          return false;
        }
      });
    },
    /**
     * 刪除(批量刪除)
     */
    remove(row) {
      console.log(row)
      if (!row) {  // 批量删除
        if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
        let ids = this.choose.map(d => d.id);
        this.$confirm('确定要删除选中的属性关联吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/quantcatprivate/delete', {id: ids}).then(res => {
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
        this.$http.post('/quantcatprivate/delete', {id:row.id}).then(res => {
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