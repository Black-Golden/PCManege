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
        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantrank:add')">添加
        </el-button>
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantrank:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
                  
          <el-table-column prop="rank_name" label="等级名称" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="rank" label="等级类型" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="num_recommend" label="直推人数" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="num_child" label="达成人数" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="per_active" label="激活码分成" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="per_token" label="量化分润" sortable="custom" show-overflow-tooltip min-width="120"/>
                  
          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">
            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">
            <template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantrank:edit')">修改</el-link>
              <el-popconfirm title="确定要删除此用户等级吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantrank:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改用户等级':'修改用户等级'" :visible.sync="showEdit" width="450px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
                          
        <el-form-item label="等级名称：" prop="rank_name">
            <el-input v-model="editForm.rank_name" placeholder="请输入等级名称" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="等级类型：" prop="rank">
            <el-input-number v-model="editForm.rank" controls-position="right" :min="0"
                              placeholder="请输入等级类型" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="直推人数：" prop="num_recommend">
            <el-input-number v-model="editForm.num_recommend" controls-position="right" :min="0"
                              placeholder="请输入直推人数" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="达成人数：" prop="num_child">
            <el-input-number v-model="editForm.num_child" controls-position="right" :min="0"
                              placeholder="请输入达成人数" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="激活码分成：" prop="per_active">
            <el-input-number v-model="editForm.per_active" controls-position="right" :min="0"
                              placeholder="请输入激活码分成" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="量化分润：" prop="per_token">
            <el-input-number v-model="editForm.per_token" controls-position="right" :min="0"
                              placeholder="请输入量化分润" class="ele-fluid ele-text-left"/>
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
  name: "SysQuantRank",
  data() {
    return {
      table: {url: '/quantrank/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
                        
        rank_name: [
          {required: true, message: '请输入等级名称', trigger: 'blur'}
        ],
                          
        rank: [
          {required: true, message: '请输入等级类型', trigger: 'blur'}
        ],
                          
        num_recommend: [
          {required: true, message: '请输入直推人数', trigger: 'blur'}
        ],
                          
        num_child: [
          {required: true, message: '请输入达成人数', trigger: 'blur'}
        ],
                          
        per_active: [
          {required: true, message: '请输入激活码分成', trigger: 'blur'}
        ],
                          
        per_token: [
          {required: true, message: '请输入量化分润', trigger: 'blur'}
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
      this.$http.get('/quantrank/info?id=' + row.id).then(res => {
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
          this.$http.post('/quantrank/edit', this.editForm).then(res => {
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
        this.$confirm('确定要删除选中的用户等级吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/quantrank/delete', {id: ids}).then(res => {
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
        this.$http.post('/quantrank/delete', {id:row.id}).then(res => {
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