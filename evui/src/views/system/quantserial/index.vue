<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
          <el-col :md="6" :sm="12">
            <el-form-item label="用户名:">
              <el-input v-model="table.where.user_name" placeholder="请输入用户名" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="激活码:">
              <el-input v-model="table.where.serno" placeholder="请输入激活码" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="是否使用:">
              <el-select v-model="table.where.is_used" placeholder="请选择审核状态" clearable class="ele-fluid">
                <el-option label="否" value="0"/>
                <el-option label="是" value="1"/>

              </el-select>
            </el-form-item>

          </el-col>
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
<!--        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantserial:add')">添加-->
<!--        </el-button>-->
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantserial:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
                  
          <el-table-column prop="user_name" label="用户名" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="serno" label="激活码" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
<!--          <el-table-column prop="is_used" label="是否使用" sortable="custom" show-overflow-tooltip min-width="120"/>-->
          <el-table-column prop="is_used" label="是否使用" sortable="custom" :resizable="false" min-width="120">

            <template slot-scope="{row}">
              <span v-if="row.is_used==0" style="color: red">否</span>
              <span v-if="row.is_used==1" style="color: green">是</span>

            </template>
          </el-table-column>
          <el-table-column prop="buy_time" label="购买时间" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="use_time" label="使用时间" sortable="custom" show-overflow-tooltip min-width="120"/>

          <el-table-column prop="create_time" label="创建时间" sortable="custom" show-overflow-tooltip min-width="120"/>
          <el-table-column prop="update_time" label="更新时间" sortable="custom" show-overflow-tooltip min-width="120"/>
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
<!--              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantserial:edit')">修改</el-link>-->
              <el-popconfirm title="确定要删除此激活码吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantserial:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改激活码':'修改激活码'" :visible.sync="showEdit" width="450px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
                          
        <el-form-item label="：" prop="user_id">
            <el-input-number v-model="editForm.user_id" controls-position="right" :min="0"
                              placeholder="请输入" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="：" prop="serno">
            <el-input v-model="editForm.serno" placeholder="请输入" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="：" prop="is_used">
            <el-input-number v-model="editForm.is_used" controls-position="right" :min="0"
                              placeholder="请输入" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="：" prop="buy_time">
            <el-input-number v-model="editForm.buy_time" controls-position="right" :min="0"
                              placeholder="请输入" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="：" prop="use_time">
            <el-input-number v-model="editForm.use_time" controls-position="right" :min="0"
                              placeholder="请输入" class="ele-fluid ele-text-left"/>
        </el-form-item>
                              
      </el-form>
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
  name: "SysQuantSerial",
  data() {
    return {
      table: {url: '/quantserial/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
              
        user_id: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                                    
        serno: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                          
        is_used: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                          
        buy_time: [
          {required: true, message: '请输入', trigger: 'blur'}
        ],
                          
        use_time: [
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
    /**
     * 显示编辑
     */
    edit(row) {
      this.$http.get('/quantserial/info?id=' + row.id).then(res => {
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
          this.$http.post('/quantserial/edit', this.editForm).then(res => {
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
        this.$confirm('确定要删除选中的激活码吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/quantserial/delete', {id: ids}).then(res => {
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
        this.$http.post('/quantserial/delete', {id:row.id}).then(res => {
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