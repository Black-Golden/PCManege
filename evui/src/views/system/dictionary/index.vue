<template>
  <div class="ele-body">
    <el-card shadow="never" body-style="padding-bottom:0;">
      <el-row :gutter="15">
        <el-col :md="6" style="margin-bottom:15px;">
          <!-- 操作按钮 -->
          <div class="ele-table-tool ele-table-tool-default">
            <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:dictionary:add')">添加
            </el-button>
            <el-button @click="edit" type="warning" icon="el-icon-edit" class="ele-btn-icon"
                       :disabled="!current" size="small" v-if="permission.includes('sys:dictionary:edit')">修改
            </el-button>
            <el-button @click="remove" type="danger" icon="el-icon-delete" class="ele-btn-icon"
                       :disabled="!current" size="small" v-if="permission.includes('sys:dictionary:delete')">删除
            </el-button>
          </div>
          <!-- 数据表格 -->
          <ele-data-table ref="table" :config="table" :current.sync="current" :choose="[]" @done="done" highlight-current-row
                          :height="tbHeight" class="dict-table">
            <el-table-column type="index" label="编号" width="60" align="center"/>
            <el-table-column prop="name" label="字典名称" show-overflow-tooltip>
              <template slot-scope="{row}">{{ row.name }}({{ row.code }})</template>
            </el-table-column>
          </ele-data-table>
        </el-col>
        <el-col :md="18" style="margin-bottom:15px;">
          <dict-data v-if="current" :dict-id="current.id" :tb-height="tbHeight"/>
        </el-col>
      </el-row>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改字典':'添加字典'" :visible.sync="showEdit" width="400px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="82px">
        <el-form-item label="字典名称:" prop="name">
          <el-input v-model="editForm.name" placeholder="请输入字典名称" clearable/>
        </el-form-item>
        <el-form-item label="字典值:" prop="code">
          <el-input v-model="editForm.code" placeholder="请输入字典值" clearable/>
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
import DictData from './dict-data'
import { mapGetters } from "vuex";
export default {
  name: "SysDictionary",
  components: {DictData},
  data() {
    return {
      table: {url: '/dict/index', page: true, where: {}},  // 表格配置
      current: null,  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
        name: [
          {required: true, message: '请输入字典名称', trigger: 'blur'}
        ],
        code: [
          {required: true, message: '请输入字典值', trigger: 'blur'}
        ],
        sort: [
          {required: true, message: '请输入排序号', trigger: 'blur'}
        ]
      }
    }
  },
  computed: {
    ...mapGetters(["permission"]),
    
    /* 表格固定高度 */
    tbHeight() {
      return this.$store.state.theme.screenWidth < 992 ? undefined : 'calc(100vh - 265px)';
    }
  },
  methods: {
    /* 表格渲染完成回调 */
    done(res) {
      if (res.data.length > 0) this.$refs.table.setCurrentRow(res.data[0]);
    },
    /* 显示编辑 */
    edit() {
      this.editForm = Object.assign({}, this.current);
      this.showEdit = true;
    },
    /* 保存编辑 */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({lock: true});
          this.$http.post('/dict/edit', this.editForm).then(res => {
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
    remove() {
      this.$confirm('确定要删除选中的字典吗?', '提示', {type: 'warning'}).then(() => {
        const loading = this.$loading({lock: true});
        this.$http.post('/dict/delete?id=' + this.current.id).then(res => {
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
    }
  }
}
</script>

<style scoped>
.dict-table >>> .el-table__row {
  cursor: pointer;
}

.dict-table >>> .el-table__row > td:last-child:after {
  content: "\e6e0";
  font-family: element-icons !important;
  font-style: normal;
  font-variant: normal;
  text-transform: none;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  line-height: 1;
  position: absolute;
  right: 10px;
  top: 50%;
  margin-top: -7px;
}

.dict-table >>> .el-table__row > td:last-child .cell {
  padding-right: 20px;
}
</style>