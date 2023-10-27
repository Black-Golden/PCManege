<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->

      <!-- 操作按钮 -->

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
            prop="remark"
            label="名称"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            align="center"
            prop="detail"
            label="内容"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            align="center"
            label="创建时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">{{
              (row.create_time * 1000) | toDateString
            }}</template>
          </el-table-column>
          <el-table-column
            align="center"
            label="更新时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">{{
              (row.update_time * 1000) | toDateString
            }}</template>
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
                v-if="permission.includes('sys:defaultconfig:edit')"
                >修改</el-link
              >
              <el-popconfirm
                title="确定要删除此配置吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:defaultconfig:delete')"
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
      :title="editForm.id ? '修改配置' : '修改配置'"
      :visible.sync="showEdit"
      width="450px"
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
        <el-form-item label="名称：" prop="name">
          <el-input
            :disabled="true"
            v-model="editForm.remark"
            placeholder="请输入名称"
            clearable
          />
        </el-form-item>

        <el-form-item label="内容：" prop="detail">
          <el-input
            v-model="editForm.detail"
            placeholder="请输入内容"
            clearable
          />
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
import { mapGetters } from 'vuex'
export default {
  name: 'SysDefaultConfig',
  data() {
    return {
      table: {
        url: '/defaultconfig/index',
        where: {},
      }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        name: [
          {
            required: true,
            message: '请输入名称',
            trigger: 'blur',
          },
        ],

        detail: [
          {
            required: true,
            message: '请输入内容',
            trigger: 'blur',
          },
        ],
      },
    }
  },
  computed: {
    ...mapGetters(['permission']),
  },
  mounted() {},
  methods: {
    /**
     * 显示编辑
     */
    edit(row) {
      this.$http
        .get('/defaultconfig/info?id=' + row.id)
        .then((res) => {
          if (res.data.code === 0) {
            this.editForm = res.data.data
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
          const loading = this.$loading({
            lock: true,
          })
          this.$http
            .post('/defaultconfig/edit', this.editForm)
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
        this.$confirm('确定要删除选中的配置吗?', '提示', {
          type: 'warning',
        })
          .then(() => {
            const loading = this.$loading({
              lock: true,
            })
            this.$http
              .post('/defaultconfig/delete', {
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
          .post('/defaultconfig/delete', {
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
  },
}
</script>

<style scoped>
.ele-block >>> .el-upload,
.ele-block >>> .el-upload-dragger {
  width: 100%;
}
</style>
