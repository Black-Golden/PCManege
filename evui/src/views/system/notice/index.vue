<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->

      <!-- 操作按钮 -->
      <div class="ele-table-tool ele-table-tool-default">
        <el-button
          @click="showEdit = true"
          type="primary"
          icon="el-icon-plus"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:notice:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:notice:dall')"
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
            align="center"
            prop="title"
            label="通知标题"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <!-- <el-table-column  align="center" prop="content" label="通知内容" sortable="custom" show-overflow-tooltip min-width="120" /> -->

          <el-table-column
            align="center"
            label="通知内容"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">
              <div v-html="row.content"></div>
            </template>
          </el-table-column>

          <el-table-column
            prop="create_time"
            align="center"
            label="创建时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          />
          <el-table-column
            prop="update_time"
            align="center"
            label="更新时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          />

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
                v-if="permission.includes('sys:notice:edit')"
                >修改</el-link
              >
              <el-popconfirm
                title="确定要删除此公告吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:notice:delete')"
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
      title="编辑公告"
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
        <el-form-item label="通知标题：" prop="title">
          <el-input
            v-model="editForm.title"
            placeholder="请输入通知标题"
            clearable
          />
        </el-form-item>

        <el-form-item label="通知内容：" prop="content">
          <!-- <el-input v-model="editForm.content" placeholder="请输入通知内容" /> -->
          <template>
            <quill-editor
              v-model="editForm.content"
              :options="editorOption"
            ></quill-editor>
          </template>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="showEdit = false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

import { mapGetters } from 'vuex'
export default {
  name: 'SysNotice',
  data() {
    return {
      table: {
        url: '/notice/index',
        where: {},
      }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        title: [
          {
            required: true,
            message: '请输入通知标题',
            trigger: 'blur',
          },
        ],

        content: [
          {
            required: true,
            message: '请输入通知内容',
            trigger: 'blur',
          },
        ],
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
    /**
     * 显示编辑
     */
    edit(row) {
      this.$http
        .get('/notice/info?id=' + row.id)
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
      //   .then((res) => {
      //     console.log(res)
      //     if (res.data.code === 0) {
      //       this.editForm = res.data
      //       this.showEdit = true
      //     } else {
      //       this.$message.error(res.data.msg)
      //     }
      //   })
      //   .catch((e) => {
      //     this.$message.error(e.message)
      //   })
    },
    /**
     * 保存编辑
     */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            lock: true,
          })
          this.$http
            .post('/notice/edit', this.editForm)
            .then((res) => {
              loading.close()
              if (res.data.code === 0) {
                this.showEdit = false
                this.$message({
                  type: 'success',
                  message: res.data.msg,
                })
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
        this.$confirm('确定要删除选中的公告吗?', '提示', {
          type: 'warning',
        })
          .then(() => {
            const loading = this.$loading({
              lock: true,
            })
            this.$http
              .post('/notice/delete', {
                id: ids,
              })
              .then((res) => {
                loading.close()
                if (res.data.code === 0) {
                  this.$message({
                    type: 'success',
                    message: res.data.msg,
                  })
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
        const loading = this.$loading({
          lock: true,
        })
        this.$http
          .post('/notice/delete', {
            id: row.id,
          })
          .then((res) => {
            loading.close()
            if (res.data.code === 0) {
              this.$message({
                type: 'success',
                message: res.data.msg,
              })
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
      const loading = this.$loading({
        lock: true,
      })
      let params = Object.assign({}, row)
      this.$http
        .post('/notice/setIsTop', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({
              type: 'success',
              message: res.data.msg,
            })
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
      const loading = this.$loading({
        lock: true,
      })
      let params = Object.assign({}, row)
      this.$http
        .post('/notice/status', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({
              type: 'success',
              message: res.data.msg,
            })
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
