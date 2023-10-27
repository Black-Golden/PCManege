<template>
  <div class="dict-data-page">
    <!-- 搜索表单 -->
    <el-form :model="table.where" class="ele-form-search ele-table-tool-default" size="small"
             @keyup.enter.native="$refs.table.reload()"
             @submit.native.prevent>
      <el-row :gutter="15">
        <el-col :md="6" :sm="12">
          <el-form-item>
            <el-input v-model="table.where.name" placeholder="请输入字典项名称" clearable size="small"/>
          </el-form-item>
        </el-col>
        <el-col :md="6" :sm="12">
          <el-form-item>
            <el-input v-model="table.where.code" placeholder="请输入字典项值" clearable size="small"/>
          </el-form-item>
        </el-col>
        <el-col :md="12" :sm="12">
          <div class="ele-form-actions">
            <el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search" class="ele-btn-icon"
                       size="small">查询
            </el-button>
            <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:dictionary:add')">添加
            </el-button>
            <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:dictionary:delete')">删除
            </el-button>
          </div>
        </el-col>
      </el-row>
    </el-form>
    <!-- 数据表格 -->
    <ele-data-table ref="table" :config="table" :choose.sync="choose" :height="tbHeight" highlight-current-row>
      <el-table-column type="selection" width="45" align="center"/>
      <el-table-column prop="name" label="字典项名称" sortable="custom" show-overflow-tooltip min-width="120"/>
      <el-table-column prop="code" label="字典项值" sortable="custom" show-overflow-tooltip min-width="110"/>
      <el-table-column prop="sort" label="排序号" sortable="custom" width="90px"/>
      <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="110">
        <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
      </el-table-column>
      <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
        <template slot-scope="{row}">
          <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:dictionary:edit')">修改
          </el-link>
          <el-popconfirm title="确定要删除此字典项吗？" @confirm="remove(row)" class="ele-action">
            <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:dictionary:delete')">删除
            </el-link>
          </el-popconfirm>
        </template>
      </el-table-column>
    </ele-data-table>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改字典项':'添加字典项'" :visible.sync="showEdit" width="400px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="96px">
        <el-form-item label="字典项名称:" prop="name">
          <el-input v-model="editForm.name" placeholder="请输入字典项名称" clearable/>
        </el-form-item>
        <el-form-item label="字典项值:" prop="code">
          <el-input v-model="editForm.code" placeholder="请输入字典项值" clearable/>
        </el-form-item>
        <el-form-item label="排序号:" prop="sort">
          <el-input-number v-model="editForm.sort" controls-position="right" :min="0"
                           placeholder="请输入排序号" class="ele-fluid ele-text-left"/>
        </el-form-item>
        <el-form-item label="备注:">
          <el-input v-model="editForm.note" placeholder="请输入备注" :rows="4" type="textarea"/>
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
  name: "SysDictData",
  props: {
    dictId: Number,  // 字典id
    tbHeight: String  // 表格高度
  },
  data() {
    return {
      table: {url: '/dictdata/index', where: {dictId: this.dictId}, page: true},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
        name: [
          {required: true, message: '请输入字典项名称', trigger: 'blur'}
        ],
        code: [
          {required: true, message: '请输入字典项值', trigger: 'blur'}
        ],
        sort: [
          {required: true, message: '请输入排序号', trigger: 'blur'}
        ]
      }
    }
  },
  computed: {
    ...mapGetters(["permission"]),
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
          // 设置字典ID
          this.editForm = Object.assign({}, this.editForm, {
            dict_id: this.dictId
          })
          this.$http.post('/dictdata/edit', this.editForm).then(res => {
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
        this.$confirm('确定要删除选中的字典项吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/dictdata/delete', {id: ids}).then(res => {
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
        this.$http.post('/dictdata/delete', {id:row.id}).then(res => {
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
    }
  },
  watch: {
    /* 监听字典id变化 */
    dictId() {
      this.table.where = {dictId: this.dictId};
      this.$refs.table.reload();
    }
  }
}
</script>

<style scoped>
</style>