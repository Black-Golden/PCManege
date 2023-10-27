<template>
  <div class="dict-data-page">
    <!-- 搜索表单 -->
    <el-form :model="table.where" class="ele-form-search ele-table-tool-default" size="small"
             @keyup.enter.native="$refs.table.reload()"
             @submit.native.prevent>
      <el-row :gutter="15">
        <el-col :md="6" :sm="12">
          <el-form-item>
            <el-input v-model="table.where.title" placeholder="请输入配置标题" clearable size="small"/>
          </el-form-item>
        </el-col>
        <el-col :md="12" :sm="12">
          <div class="ele-form-actions">
            <el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search" class="ele-btn-icon"
                       size="small">查询
            </el-button>
            <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:config:add')">添加
            </el-button>
            <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:config:delete')">删除
            </el-button>
          </div>
        </el-col>
      </el-row>
    </el-form>
    <!-- 数据表格 -->
    <ele-data-table ref="table" :config="table" :choose.sync="choose" :height="tbHeight" highlight-current-row>
      <el-table-column type="selection" width="45" align="center"/>
      <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
      <el-table-column prop="title" label="配置标题" sortable="custom" show-overflow-tooltip min-width="120"/>
      <el-table-column prop="code" label="配置编码" sortable="custom" show-overflow-tooltip min-width="110"/>
      <el-table-column prop="value" label="配置值" sortable="custom" show-overflow-tooltip min-width="110"/>
      <el-table-column label="配置类型" min-width="100" align="center">
        <template slot-scope="{row}">
          <el-tag v-if="row.type === 'readonly'" type="" size="small">只读文本</el-tag>
          <el-tag v-if="row.type === 'number'" type="success" size="small">数字</el-tag>
          <el-tag v-if="row.type === 'text'" type="info" size="small">单行文本</el-tag>
          <el-tag v-if="row.type === 'textarea'" type="warning" size="small">多行文本</el-tag>
          <el-tag v-if="row.type === 'array'" type="danger" size="small">数组</el-tag>
          <el-tag v-if="row.type === 'password'" type="" size="small">密码</el-tag>
          <el-tag v-if="row.type === 'radio'" type="success" size="small">单选框</el-tag>
          <el-tag v-if="row.type === 'checkbox'" type="info" size="small">复选框</el-tag>
          <el-tag v-if="row.type === 'select'" type="warning" size="small">下拉框</el-tag>
          <el-tag v-if="row.type === 'icon'" type="danger" size="small">字体图标</el-tag>
          <el-tag v-if="row.type === 'date'" type="" size="small">日期</el-tag>
          <el-tag v-if="row.type === 'datetime'" type="success" size="small">时间</el-tag>
          <el-tag v-if="row.type === 'image'" type="info" size="small">单张图片</el-tag>
          <el-tag v-if="row.type === 'images'" type="" size="small">多张图片</el-tag>
          <el-tag v-if="row.type === 'file'" type="success" size="small">单个文件</el-tag>
          <el-tag v-if="row.type === 'files'" type="warning" size="small">多个文件</el-tag>
          <el-tag v-if="row.type === 'ueditor'" type="danger" size="small">富文本编辑器</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="status" label="配置状态" sortable="custom" :resizable="false" min-width="120">
        <template slot-scope="{row}">
          <el-switch v-model="row.status" @change="editStatus(row)" :active-value="1" :inactive-value="2"/>
        </template>
      </el-table-column>
      <el-table-column prop="sort" label="排序号" sortable="custom" width="90px"/>
      <el-table-column prop="note" label="备注" min-width="200px"/>
      <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="110">
        <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
      </el-table-column>
      <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
        <template slot-scope="{row}">
          <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:config:edit')">修改
          </el-link>
          <el-popconfirm title="确定要删除此配置吗？" @confirm="remove(row)" class="ele-action">
            <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:config:delete')">删除
            </el-link>
          </el-popconfirm>
        </template>
      </el-table-column>
    </ele-data-table>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改配置':'添加配置'" :visible.sync="showEdit" width="600px"
               @closed="editForm={}" :destroy-on-close="true" custom-class="ele-dialog-form" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="96px">
        <el-row :gutter="15">
          <el-col :sm="12">
            <el-form-item label="配置标题:" prop="title">
              <el-input v-model="editForm.title" placeholder="请输入配置标题" clearable/>
            </el-form-item>
            <el-form-item label="配置类型:" prop="type">
              <el-select v-model="editForm.type" placeholder="请选择配置类型" class="ele-block" clearable>
                <el-option label="只读文本" value="readonly"/>
                <el-option label="数字" value="number"/>
                <el-option label="单行文本" value="text"/>
                <el-option label="多行文本" value="textarea"/>
                <el-option label="数组" value="array"/>
                <el-option label="密码" value="password"/>
                <el-option label="单选框" value="radio"/>
                <el-option label="复选框" value="checkbox"/>
                <el-option label="下拉框" value="select"/>
                <el-option label="字体图标" value="icon"/>
                <el-option label="日期" value="date"/>
                <el-option label="时间" value="datetime"/>
                <el-option label="单张图片" value="image"/>
                <el-option label="多张图片" value="images"/>
                <el-option label="单个文件" value="file"/>
                <el-option label="多个文件" value="files"/>
                <el-option label="富文本编辑器" value="ueditor"/>
              </el-select>
            </el-form-item>
            <el-form-item label="配置值:" prop="value">
              <el-input v-model="editForm.value" placeholder="请输入配置值" clearable/>
            </el-form-item>
          </el-col>
          <el-col :sm="12">
            <el-form-item label="配置编码:" prop="code">
              <el-input v-model="editForm.code" placeholder="请输入配置编码" clearable/>
            </el-form-item>
            <el-form-item label="状态:">
              <el-radio-group v-model="editForm.status">
                <el-radio :label="1">正常</el-radio>
                <el-radio :label="2">禁用</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="排序号:" prop="sort">
              <el-input-number v-model="editForm.sort" controls-position="right" :min="0"
                              placeholder="请输入排序号" class="ele-fluid ele-text-left"/>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="配置项:">
          <el-input v-model="editForm.options" placeholder="请输入配置项" :rows="3" type="textarea"/>
        </el-form-item>
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
import { mapGetters } from "vuex";
export default {
  name: "SysConfigData",
  props: {
    configId: Number,  // 配置id
    tbHeight: String  // 表格高度
  },
  data() {
    return {
      table: {url: '/configdata/index', where: {configId: this.configId}, page: true},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {status:1},  // 表单数据
      editRules: {  // 表单验证规则
        title: [
          {required: true, message: '请输入配置标题', trigger: 'blur'}
        ],
        code: [
          {required: true, message: '请输入配置编码', trigger: 'blur'}
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
          // 设置配置ID
          this.editForm = Object.assign({}, this.editForm, {
            config_id: this.configId
          })
          this.$http.post('/configdata/edit', this.editForm).then(res => {
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
        this.$confirm('确定要删除选中的配置吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/configdata/delete', {id: ids}).then(res => {
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
        this.$http.post('/configdata/delete', {id:row.id}).then(res => {
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
      this.$http.post('/configdata/status', params).then(res => {
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
  },
  watch: {
    /* 监听配置id变化 */
    configId() {
      this.table.where = {configId: this.configId};
      this.$refs.table.reload();
    }
  }
}
</script>

<style scoped>
</style>