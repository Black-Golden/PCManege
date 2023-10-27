<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form
        :model="table.where"
        label-width="77px"
        class="ele-form-search"
        @keyup.enter.native="$refs.table.reload()"
        @submit.native.prevent
      >
        <el-row :gutter="15">
          <el-col :md="5" :sm="12">
            <el-form-item label="咨询标题:">
              <el-input
                v-model="table.where.title"
                placeholder="请输入咨询标题"
                clearable
              />
            </el-form-item>
          </el-col>
          <el-col :md="12" :sm="10">
            <div class="block">
              <el-form-item label="搜索时间">
                <el-date-picker
                  @change="rq_change"
                  value-format="yyyy-MM-dd HH:mm"
                  style="margin-left:10px"
                  v-model="create_time"
                  type="datetimerange"
                  start-placeholder="开始日期"
                  end-placeholder="结束日期"
                >
                </el-date-picker
              ></el-form-item>
            </div>
          </el-col>
          <el-col :md="6" :sm="12">
            <div class="ele-form-actions">
              <el-button
                type="primary"
                @click="$refs.table.reload()"
                icon="el-icon-search"
                class="ele-btn-icon"
                >查询
              </el-button>
              <el-button @click=";(table.where = {}) && $refs.table.reload()"
                >重置</el-button
              >
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 操作按钮 -->
      <div class="ele-table-tool ele-table-tool-default">
        <el-button
          @click="showEdit = true"
          type="primary"
          icon="el-icon-plus"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:consult:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:consult:dall')"
          >批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table
        ref="table"
        :config="table"
        :choose.sync="choose"
        height="calc(100vh - 315px)"
        highlight-current-row
      >
        <template slot-scope="{ index }">
          <el-table-column
            type="selection"
            width="45"
            align="center"
            fixed="left"
          />
          <el-table-column
            type="index"
            :index="index"
            label="编号"
            width="60"
            align="center"
            fixed="left"
            show-overflow-tooltip
          />

          <el-table-column
            prop="title"
            label="咨询标题"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="content"
            label="咨询内容"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />
          <!--                                          -->
          <!--          <el-table-column prop="is_top" label="是否置顶" sortable="custom" :resizable="false" min-width="120">-->
          <!--              <template slot-scope="{row}">-->
          <!--                  <el-switch v-model="row.is_top" @change="setIsTop(row)" :active-value="1" :inactive-value="2"/>-->
          <!--              </template>-->
          <!--          </el-table-column>-->

          <!--          <el-table-column prop="browse" label="阅读量" sortable="custom" show-overflow-tooltip min-width="120"/>-->

          <el-table-column
            label="创建时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">{{ row.create_time }}</template>
          </el-table-column>
          <el-table-column
            label="更新时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">{{ row.update_time }}</template>
          </el-table-column>
          <el-table-column
            label="操作"
            width="130px"
            align="center"
            :resizable="false"
            fixed="right"
          >
            <template slot-scope="{ row }">
              <el-link
                @click="edit(row)"
                icon="el-icon-edit"
                type="primary"
                :underline="false"
                v-if="permission.includes('sys:consult:edit')"
                >修改</el-link
              >
              <el-popconfirm
                title="确定要删除此咨询吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:consult:delete')"
                  >删除</el-link
                >
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog
      :title="editForm.id ? '编辑资讯' : '新增资讯'"
      :visible.sync="showEdit"
      width="700px"
      @closed="editForm = {}"
      :destroy-on-close="true"
      :lock-scroll="false"
    >
      <el-form
        :model="editForm"
        ref="editForm"
        :rules="editRules"
        label-width="100px"
      >
        <el-form-item label="咨询标题：" prop="title">
          <el-input
            v-model="editForm.title"
            placeholder="请输入咨询标题"
            clearable
          />
        </el-form-item>

        <el-form-item label="咨询内容：" prop="content">
          <template>
            <quill-editor
              v-model="editForm.content"
              :options="editorOption"
            ></quill-editor>
          </template>
          <!-- <el-input
            v-model="editForm.content"
            controls-position="right"
            :min="0"
            placeholder="请输入咨询内容"
            class="ele-fluid ele-text-left"
            type="textarea"
          /> -->
        </el-form-item>

        <!--        <el-form-item label="是否置顶：" prop="is_top">-->
        <!--          <el-switch-->
        <!--                  v-model="editForm.is_top"-->
        <!--                  active-text="是"-->
        <!--                  inactive-text="否">-->
        <!--          </el-switch>-->
        <!--        </el-form-item>-->
      </el-form>
      <div slot="footer">
        <el-button @click="showEdit = false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
export default {
  name: 'SysConsult',
  data() {
    return {
      create_time: '',
      table: { url: '/consult/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        title: [{ required: true, message: '请输入通知标题', trigger: 'blur' }],

        content: [
          { required: true, message: '请输入通知内容', trigger: 'blur' },
        ],

        is_top: [
          { required: true, message: '请选择是否置顶', trigger: 'blur' },
        ],

        browse: [{ required: true, message: '请输入阅读量', trigger: 'blur' }],

        status: [{ required: true, message: '请选择状态', trigger: 'blur' }],
      },
      editorOption: {
        // 富文本编辑器 定义功能
        // 占位配置
        placeholder: '',
        modules: {
          // 配置工具栏
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ header: 1 }, { header: 2 }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ indent: '-1' }, { indent: '+1' }],
            ['image'],
          ],
        },
      },
    }
  },
  computed: {
    ...mapGetters(['permission']),
  },
  components: {
    quillEditor, // 富文本编辑器
  },
  mounted() {},
  methods: {
    rq_change(e) {
      this.table.where.start_time = e[0]
      this.table.where.end_time = e[1]
    },
    /**
     * 显示编辑
     */
    edit(row) {
      this.$http
        .get('/consult/info?id=' + row.id)
        .then((res) => {
          console.log(res)
          if (res.status === 200) {
            this.editForm = res.data
            this.showEdit = true
          } else {
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          this.$message.error(e.message)
        })
    },
    /**
     * 保存编辑
     */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({ lock: true })
          this.$http
            .post('/consult/edit', this.editForm)
            .then((res) => {
              loading.close()
              if (res.data.code === 0) {
                this.showEdit = false
                this.$message({ type: 'success', message: res.data.msg })
                this.$refs.table.reload()
              } else {
                this.$message.error(res.data.msg)
              }
            })
            .catch((e) => {
              loading.close()
              this.$message.error(e.message)
            })
        } else {
          return false
        }
      })
    },
    /**
     * 刪除(批量刪除)
     */
    remove(row) {
      console.log(row)
      if (!row) {
        // 批量删除
        if (this.choose.length === 0)
          return this.$message.error('请至少选择一条数据')
        let ids = this.choose.map((d) => d.id)
        this.$confirm('确定要删除选中的咨询吗?', '提示', { type: 'warning' })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/consult/delete', { id: ids })
              .then((res) => {
                loading.close()
                if (res.data.code === 0) {
                  this.$message({ type: 'success', message: res.data.msg })
                  this.$refs.table.reload()
                } else {
                  this.$message.error(res.data.msg)
                }
              })
              .catch((e) => {
                loading.close()
                this.$message.error(e.message)
              })
          })
          .catch(() => 0)
      } else {
        // 单个删除
        const loading = this.$loading({ lock: true })
        this.$http
          .post('/consult/delete', { id: row.id })
          .then((res) => {
            loading.close()
            if (res.data.code === 0) {
              this.$message({ type: 'success', message: res.data.msg })
              this.$refs.table.reload()
            } else {
              this.$message.error(res.data.msg)
            }
          })
          .catch((e) => {
            loading.close()
            this.$message.error(e.message)
          })
      }
    },

    /**
     * 更改是否置顶
     */
    setIsTop(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/consult/setIsTop', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({ type: 'success', message: res.data.msg })
          } else {
            row.is_top = !row.is_top ? 2 : 1
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          loading.close()
          this.$message.error(e.message)
        })
    },

    /**
     * 更改状态
     */
    status(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/consult/status', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({ type: 'success', message: res.data.msg })
          } else {
            row.status = !row.status ? 2 : 1
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          loading.close()
          this.$message.error(e.message)
        })
    },
  },
}
</script>

<style scoped>
.ele-block >>> .el-upload,
.ele-block >>> .el-upload-dragger {
  width: 100%;
}
/deep/.ql-editor {
  min-height: 300px;
}
</style>
