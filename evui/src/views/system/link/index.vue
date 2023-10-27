<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
          <el-col :md="6" :sm="12">
            <el-form-item label="友链名称:">
              <el-input v-model="table.where.name" placeholder="请输入友链名称" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="友链类型:">
              <el-select v-model="table.where.type" placeholder="请选择友链类型" clearable class="ele-fluid">
                <el-option label="友情链接" value="1"/>
                <el-option label="合作伙伴" value="2"/>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="所属平台:">
              <el-select v-model="table.where.platform" placeholder="请选择所属平台" clearable class="ele-fluid">
                <el-option label="PC站" value="1"/>
                <el-option label="WAP站" value="2"/>
                <el-option label="微信小程序" value="3"/>
                <el-option label="APP应用" value="4"/>
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
        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:link:add')">添加
        </el-button>
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:link:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
          <el-table-column prop="name" label="友链名称" show-overflow-tooltip align="center" min-width="200"/>
          <el-table-column label="友链类型" min-width="100" align="center">
            <template slot-scope="{row}">
              <el-tag :type="['','success'][row.type-1]" size="mini">{{ ['友情链接', '合作伙伴'][row.type-1] }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="友链平台" min-width="100" align="center">
            <template slot-scope="{row}">
              <el-tag :type="['','primary','success','warning'][row.platform-1]" size="mini">{{ ['PC站', 'WAP站', '微信小程序', 'APP应用'][row.platform-1] }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="url" label="友链URL" show-overflow-tooltip align="center" min-width="200"/>
          <el-table-column label="友链图片" min-width="100" align="center">
            <template slot-scope="{row}">
              <el-avatar shape="square" :size="35" :src="row.image"/>
            </template>
          </el-table-column>
          <el-table-column prop="form" label="友链形式" align="center" min-width="100">
            <template slot-scope="{row}">
              <ele-dot :type="['', 'success'][row.form-1]" :ripple="row.form===0"
                        :text="['文字链接','图片链接'][row.form-1]"/>
            </template>
          </el-table-column>
          <el-table-column prop="status" label="友链状态" :resizable="false" align="center" min-width="100">
            <template slot-scope="{row}">
              <el-switch v-model="row.status" @change="editStatus(row)" :active-value="1" :inactive-value="2"/>
            </template>
          </el-table-column>
          <el-table-column prop="note" label="备注" show-overflow-tooltip align="center" min-width="250"/>
          <el-table-column prop="sort" label="排序" sortable="custom" align="center" show-overflow-tooltip min-width="100"/>
          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip align="center" min-width="160">
            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip align="center" min-width="160">
            <template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:link:edit')">修改</el-link>
              <el-popconfirm title="确定要删除此友链吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:link:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改友链':'修改友链'" :visible.sync="showEdit" width="600px"
               @closed="editForm={}" :destroy-on-close="true" custom-class="ele-dialog-form" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="82px">
        <el-form-item label="友链图片:">
          <uploadImage :limit="1" v-model="editForm.image"></uploadImage>
        </el-form-item>
        <el-row :gutter="15">
          <el-col :sm="12">
            <el-form-item label="友链名称:" prop="name">
              <el-input v-model="editForm.name" placeholder="请输入友链名称" clearable/>
            </el-form-item>
            <el-form-item label="友链URL:" prop="url">
              <el-input v-model="editForm.url" placeholder="请输入友链URL" clearable/>
            </el-form-item>
            <el-form-item label="友链形式:">
              <el-radio-group v-model="editForm.form">
                <el-radio :label="1">文字链接</el-radio>
                <el-radio :label="2">图片链接</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="排序号:" prop="sort">
              <el-input-number v-model="editForm.sort" controls-position="right" :min="0"
                                placeholder="请输入排序号" class="ele-fluid ele-text-left"/>
            </el-form-item>
          </el-col>
          <el-col :sm="12">
            <el-form-item label="友链类型:" prop="type">
              <el-select v-model="editForm.type" placeholder="请选择友链类型" class="ele-block" clearable>
                <el-option label="友情链接" :value="1"/>
                <el-option label="合作伙伴" :value="2"/>
              </el-select>
            </el-form-item>
            <el-form-item label="友链平台:" prop="platform">
              <el-select v-model="editForm.platform" placeholder="请选择友链平台" class="ele-block" clearable>
                <el-option label="PC站" :value="1"/>
                <el-option label="WAP站" :value="2"/>
                <el-option label="微信小程序" :value="3"/>
                <el-option label="APP应用" :value="4"/>
              </el-select>
            </el-form-item>
            <el-form-item label="友链状态:">
              <el-radio-group v-model="editForm.status">
                <el-radio :label="1">正常</el-radio>
                <el-radio :label="2">禁用</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="备注:">
          <el-input v-model="editForm.note" placeholder="请输入备注" :rows="3" type="textarea"/>
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
import uploadImage from '@/components/uploadImage'
import { mapGetters } from "vuex";
export default {
  name: "SysLink",
  components: {uploadImage},
  data() {
    return {
      table: {url: '/link/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {status:1, form:1},  // 表单数据
      editRules: {  // 表单验证规则
        name: [
          {required: true, message: '请输入友链名称', trigger: 'blur'}
        ],
        type: [
          {required: true, message: '请选择友链类型', trigger: 'blur'}
        ],
        url: [
          {required: true, message: '请输入友链地址', trigger: 'blur'}
        ],
        platform: [
          {required: true, message: '请选择友链平台', trigger: 'blur'}
        ],
        status: [
          {required: true, message: '请选择友链状态', trigger: 'blur'}
        ],
        sort: [
          {required: true, message: '请输入排序', trigger: 'blur'}
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
    /* 显示编辑 */
    edit(row) {
      this.editForm = Object.assign({}, row);
      this.showEdit = true;
    },
    /* 保存编辑 */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({lock: true});
          this.$http.post('/link/edit', this.editForm).then(res => {
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
    /* 删除 */
    remove(row) {
      if (!row) {  // 批量删除
        if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
        let ids = this.choose.map(d => d.id);
        this.$confirm('确定要删除选中的友链吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/link/delete', {id: ids}).then(res => {
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
        this.$http.post('/link/delete', {id:row.id}).then(res => {
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
    /* 更改状态 */
    editStatus(row) {
      const loading = this.$loading({lock: true});
      let params = Object.assign({}, row);
      this.$http.post('/link/status', params).then(res => {
        loading.close();
        if (res.data.code === 0) {
          this.$message({type: 'success', message: res.data.msg});
        } else {
          row.status = !row.status ? 2 : 1;
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        loading.close();
        this.$message.error(e.message);
      });
    },
  }
}
</script>

<style scoped>
.ele-block >>> .el-upload, .ele-block >>> .el-upload-dragger {
  width: 100%;
}
</style>