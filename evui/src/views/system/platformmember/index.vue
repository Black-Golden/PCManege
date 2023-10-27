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
          <el-col :md="6" :sm="12">
            <el-form-item label="用户名:">
              <el-input
                v-model="table.where.user_name"
                placeholder="请输入用户名"
                clearable
              />
            </el-form-item>
          </el-col>

          <el-col :md="6" :sm="12">
            <el-form-item label="平台:">
              <el-select
                clearable
                v-model="table.where.platform_id"
                placeholder="请选择平台"
              >
                <el-option
                  v-for="item in option"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :md="9" :sm="19">
            <div class="block">
              <!-- <span class="demonstration">搜索时间</span> -->
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
      <!-- <div class="ele-table-tool ele-table-tool-default">
        <el-button
          @click="showEdit = true"
          type="primary"
          icon="el-icon-plus"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:platformmember:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:platformmember:dall')"
          >批量删除
        </el-button>
      </div> -->
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
            prop="user_name"
            label="用户名"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="platform_id"
            label="平台名称"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <div>{{ row.platform_id == 2 ? '欧易' : '币安' }}</div>
            </template>
          </el-table-column>

          <el-table-column
            prop="key"
            label="API Key"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="secret"
            label="Secret Key"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="passphrase"
            label="Passphrase"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="create_time "
            label="创建时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="row"
              ><div>{{ row.row.create_time }}</div></template
            >
          </el-table-column>

          <el-table-column
            prop="update_time"
            label="更新时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          />

          <!-- <el-table-column
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
                v-if="permission.includes('sys:platformmember:edit')"
                >修改</el-link
              >
              <el-popconfirm
                title="确定要删除此API配置吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:platformmember:delete')"
                  >删除</el-link
                >
              </el-popconfirm>
            </template>
          </el-table-column> -->
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog
      :title="editForm.id ? '修改API配置' : '修改API配置'"
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
        label-width="auto"
      >
        <el-form-item label="用户名：" prop="user_name">
          <el-input
            v-model="editForm.user_name"
            placeholder="请输入用户名"
            clearable
          />
        </el-form-item>

        <el-form-item label="平台名称：" prop="platform_id">
          <!-- <el-input-number
            v-model="editForm.platform_id"
            controls-position="right"
            :min="0"
            placeholder="请输入平台名称"
            class="ele-fluid ele-text-left"
          /> -->
          <el-select
            v-model="editForm.platform_id"
            clearable
            placeholder="请选择平台"
          >
            <el-option
              v-for="item in option"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="API Key:" prop="key">
          <el-input v-model="editForm.key" placeholder="请输入" clearable />
        </el-form-item>

        <el-form-item label="Secret Key:" prop="secret">
          <el-input v-model="editForm.secret" placeholder="请输入" clearable />
        </el-form-item>

        <el-form-item label="Passphrase:" prop="passphrase">
          <el-input
            v-model="editForm.passphrase"
            placeholder="请输入Passphrase"
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
  name: 'SysPlatformMember',
  data() {
    return {
      create_time: '',
      table: { url: '/platformmember/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        user_name: [
          { required: true, message: '请输入用户名', trigger: 'blur' },
        ],

        platform_id: [
          { required: true, message: '请输入平台名称', trigger: 'blur' },
        ],

        account_id: [
          { required: true, message: '请输入火币需要用到', trigger: 'blur' },
        ],

        key: [{ required: true, message: '请输入', trigger: 'blur' }],

        secret: [{ required: true, message: '请输入', trigger: 'blur' }],

        passphrase: [
          { required: true, message: '请输入ok需要用到的', trigger: 'blur' },
        ],
      },
      option: [
        {
          value: 2,
          label: '欧易',
        },
        {
          value: 3,
          label: '币安',
        },
      ],
    }
  },
  computed: {
    ...mapGetters(['permission']),
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
        .get('/platformmember/info?id=' + row.id)
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
          const loading = this.$loading({ lock: true })
          this.$http
            .post('/platformmember/edit', this.editForm)
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
        this.$confirm('确定要删除选中的API配置吗?', '提示', { type: 'warning' })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/platformmember/delete', { id: ids })
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
          .post('/platformmember/delete', { id: row.id })
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
  },
}
</script>

<style scoped>
.ele-block >>> .el-upload,
.ele-block >>> .el-upload-dragger {
  width: 100%;
}
</style>
