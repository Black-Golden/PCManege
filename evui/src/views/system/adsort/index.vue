<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
            
            <el-col :md="6" :sm="12">
              <el-form-item label="广告位名称:">
                <el-input v-model="table.where.name" placeholder="请输入广告位名称" clearable/>
              </el-form-item>
            </el-col>
                                
            <el-col :md="6" :sm="12">
              <el-form-item label="站点类型:">
                <el-select v-model="table.where.platform" placeholder="请选择站点类型" clearable class="ele-fluid">
                                      <el-option label="PC网站" value="1"/>
                                      <el-option label="WAP手机站" value="2"/>
                                      <el-option label="微信小程序" value="3"/>
                                      <el-option label="APP移动端" value="4"/>
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
        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:adsort:add')">添加
        </el-button>
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:adsort:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
                  
          <el-table-column prop="name" label="广告位名称" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="description" label="广告位描述" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="item_id" label="站点ID" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="cate_id" label="栏目ID" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="loc_id" label="广告页面位置" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="platform" label="站点类型" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="sort" label="广告位排序" sortable="custom" show-overflow-tooltip min-width="120"/>
                  
          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">
            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">
            <template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:adsort:edit')">修改</el-link>
              <el-popconfirm title="确定要删除此广告位吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:adsort:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改广告位':'修改广告位'" :visible.sync="showEdit" width="450px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
                          
        <el-form-item label="广告位名称：" prop="name">
            <el-input v-model="editForm.name" placeholder="请输入广告位名称" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="广告位描述：" prop="description">
            <el-input v-model="editForm.description" placeholder="请输入广告位描述" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="站点ID：" prop="item_id">
            <el-input-number v-model="editForm.item_id" controls-position="right" :min="0"
                              placeholder="请输入站点ID" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="栏目ID：" prop="cate_id">
            <el-input-number v-model="editForm.cate_id" controls-position="right" :min="0"
                              placeholder="请输入栏目ID" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="广告页面位置：" prop="loc_id">
            <el-input-number v-model="editForm.loc_id" controls-position="right" :min="0"
                              placeholder="请输入广告页面位置" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                  
        <el-form-item label="站点类型：" prop="platform">
          <el-select v-model="editForm.platform" placeholder="请选择站点类型" class="ele-block" clearable>
            <el-option
                    v-for="item in []"
                    :key="item.value"
                    :label="item.name"
                    :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
                                                  
        <el-form-item label="广告位排序：" prop="sort">
            <el-input-number v-model="editForm.sort" controls-position="right" :min="0"
                              placeholder="请输入广告位排序" class="ele-fluid ele-text-left"/>
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
  name: "SysAdSort",
  data() {
    return {
      table: {url: '/adsort/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
                        
        name: [
          {required: true, message: '请输入广告位名称', trigger: 'blur'}
        ],
                                    
        description: [
          {required: true, message: '请输入广告位描述', trigger: 'blur'}
        ],
                          
        item_id: [
          {required: true, message: '请输入站点ID', trigger: 'blur'}
        ],
                          
        cate_id: [
          {required: true, message: '请输入栏目ID', trigger: 'blur'}
        ],
                          
        loc_id: [
          {required: true, message: '请输入广告页面位置', trigger: 'blur'}
        ],
                          
        platform: [
          {required: true, message: '请选择站点类型', trigger: 'blur'}
        ],
                          
        sort: [
          {required: true, message: '请输入广告位排序', trigger: 'blur'}
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
      this.$http.get('/adsort/info?id=' + row.id).then(res => {
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
          this.$http.post('/adsort/edit', this.editForm).then(res => {
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
        this.$confirm('确定要删除选中的广告位吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/adsort/delete', {id: ids}).then(res => {
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
        this.$http.post('/adsort/delete', {id:row.id}).then(res => {
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