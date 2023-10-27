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
            <el-form-item label="所属平台:">
              <el-select v-model="table.where.platform_id" placeholder="请选择所属平台" clearable class="ele-fluid">
                <el-option label="欧易" value="2"/>
                <el-option label="币安" value="3"/>

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
<!--        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantsetupdown:add')">添加-->
<!--        </el-button>-->
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantsetupdown:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
                  
          <el-table-column prop="user_name" label="用户" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="symbol" label="代币类型" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="rounds" label="补仓次数" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="down_stop_per" label="补仓百分比" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="exp" label="倍数" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
<!--          <el-table-column prop="platform_id" label="所属平台" sortable="custom" show-overflow-tooltip min-width="120"/>-->
          <el-table-column prop="platform_id" label="所属平台" sortable="custom" :resizable="false" min-width="120">

            <template slot-scope="{row}">

              <span v-if="row.platform_id==2" style="color: green">欧易</span>
              <span v-if="row.platform_id==3" style="color: blue">币安</span>
            </template>
          </el-table-column>
<!--          <el-table-column prop="platform_type" label="1是现货2是合约" sortable="custom" show-overflow-tooltip min-width="120"/>-->
          <el-table-column prop="platform_type" label="类别" sortable="custom" :resizable="false" min-width="120">

            <template slot-scope="{row}">

              <span v-if="row.platform_type==1" style="color: green">现货</span>
              <span v-if="row.platform_type==2" style="color: blue">合约</span>
            </template>
          </el-table-column>
                                
          <el-table-column prop="setup_id" label="关联setup_id" sortable="custom" show-overflow-tooltip min-width="120"/>

          <el-table-column prop="create_time" label="创建时间" sortable="custom" show-overflow-tooltip min-width="120"/>
          <el-table-column prop="update_time" label="更新时间" sortable="custom" show-overflow-tooltip min-width="120"/>
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
<!--              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantsetupdown:edit')">修改</el-link>-->
              <el-popconfirm title="确定要删除此补仓设置吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantsetupdown:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改补仓设置':'修改补仓设置'" :visible.sync="showEdit" width="450px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
                          
        <el-form-item label="用户：" prop="user_id">
            <el-input-number v-model="editForm.user_id" controls-position="right" :min="0"
                              placeholder="请输入用户" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="代币类型：" prop="symbol">
            <el-input v-model="editForm.symbol" placeholder="请输入代币类型" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="补仓次数：" prop="rounds">
            <el-input-number v-model="editForm.rounds" controls-position="right" :min="0"
                              placeholder="请输入补仓次数" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="补仓百分比：" prop="down_stop_per">
            <el-input-number v-model="editForm.down_stop_per" controls-position="right" :min="0"
                              placeholder="请输入补仓百分比" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="倍数：" prop="exp">
            <el-input-number v-model="editForm.exp" controls-position="right" :min="0"
                              placeholder="请输入倍数" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="所属平台：" prop="platform_id">
            <el-input-number v-model="editForm.platform_id" controls-position="right" :min="0"
                              placeholder="请输入所属平台" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="1是现货2是合约：" prop="platform_type">
            <el-input-number v-model="editForm.platform_type" controls-position="right" :min="0"
                              placeholder="请输入1是现货2是合约" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="关联setup_id：" prop="setup_id">
            <el-input-number v-model="editForm.setup_id" controls-position="right" :min="0"
                              placeholder="请输入关联setup_id" class="ele-fluid ele-text-left"/>
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
  name: "SysQuantSetupDown",
  data() {
    return {
      table: {url: '/quantsetupdown/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
              
        user_id: [
          {required: true, message: '请输入用户', trigger: 'blur'}
        ],
                                    
        symbol: [
          {required: true, message: '请输入代币类型', trigger: 'blur'}
        ],
                          
        rounds: [
          {required: true, message: '请输入补仓次数', trigger: 'blur'}
        ],
                          
        down_stop_per: [
          {required: true, message: '请输入补仓百分比', trigger: 'blur'}
        ],
                          
        exp: [
          {required: true, message: '请输入倍数', trigger: 'blur'}
        ],
                          
        platform_id: [
          {required: true, message: '请输入所属平台', trigger: 'blur'}
        ],
                          
        platform_type: [
          {required: true, message: '请输入1是现货2是合约', trigger: 'blur'}
        ],
                          
        setup_id: [
          {required: true, message: '请输入关联setup_id', trigger: 'blur'}
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
      this.$http.get('/quantsetupdown/info?id=' + row.id).then(res => {
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
          this.$http.post('/quantsetupdown/edit', this.editForm).then(res => {
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
        this.$confirm('确定要删除选中的补仓设置吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/quantsetupdown/delete', {id: ids}).then(res => {
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
        this.$http.post('/quantsetupdown/delete', {id:row.id}).then(res => {
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