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
            <el-form-item label="补仓次数:">
              <el-input
                v-model="table.where.rounds"
                placeholder="请输入补仓次数"
                clearable
              />
            </el-form-item>
          </el-col>

          <el-col :md="6" :sm="12">
            <el-form-item label="倍数:">
              <el-input
                v-model="table.where.lever"
                placeholder="请输入倍数"
                clearable
              />
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="策略方向:">
              <el-select
                clearable
                v-model="table.where.type_id"
                placeholder="请选择策略方向"
              >
                <el-option
                  v-for="item in options"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select>
            </el-form-item>
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
          v-if="permission.includes('sys:singleleverconfig:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:singleleverconfig:dall')"
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
            prop="rounds"
            label="补仓次数"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="up_stop_per"
            label="止盈百分比"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="scope">
              <div class="green">{{ scope.row.up_stop_per * 100 }}%</div>
            </template>
          </el-table-column>

          <el-table-column
            prop="down_stop_per"
            label="补仓百分比"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="scope">
              <div class="red">{{ scope.row.down_stop_per * 100 }}%</div>
            </template>
          </el-table-column>

          <el-table-column
            prop="lever"
            label="倍数"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="type_id"
            label="策略方向"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <div>
                {{
                  row.type_id == 1
                    ? '高频'
                    : row.type_id == 2
                    ? '激进'
                    : row.type_id == 3
                    ? '保守'
                    : row.type_id == 4
                    ? '超级保守'
                    : row.type_id == 5
                    ? '自定义'
                    : '系统默认'
                }}
              </div>
            </template>
          </el-table-column>
          <!-- 
          <el-table-column
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
            label="更新时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">{{
              (row.update_time * 1000) | toDateString
            }}</template>
          </el-table-column> -->
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
                icon="el-icon-view"
                type="primary"
                :underline="false"
                v-if="permission.includes('sys:singleleverconfig:edit')"
                >查看</el-link
              >
              <!-- <el-popconfirm
                title="确定要删除此交易策略吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:singleleverconfig:delete')"
                  >删除</el-link
                >
              </el-popconfirm> -->
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog
      :title="editForm.id ? '查看交易策略' : '查看交易策略'"
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
        label-width="auto"
      >
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="补仓次数："
              prop="rounds"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.rounds"
                placeholder="请输入补仓次数"
                class="ele-fluid ele-text-left"
              ></el-input>
            </el-form-item>
          </el-col>
          <el-col :sm="11">
            <el-form-item
              label="止盈百分比："
              prop="up_stop_per"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.up_stop_per * 100 + '%'"
                placeholder="请输入止盈百分比"
                class="ele-fluid ele-text-left green"
              ></el-input> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="补仓百分比："
              prop="down_stop_per"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.down_stop_per * 100 + '%'"
                placeholder="请输入补仓百分比"
                class="ele-fluid ele-text-left"
              ></el-input> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="倍数："
              prop="lever"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.lever"
                placeholder="请输入倍数"
                class="ele-fluid ele-text-left"
              ></el-input> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item label="策略：" prop="type_id">
              <el-select
                disabled
                v-model="editForm.type_id"
                placeholder="请选择策略"
              >
                <el-option
                  v-for="item in options"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :sm="11"> </el-col>
        </el-row>
      </el-form>
      <div slot="footer">
        <el-button type="primary" @click="showEdit = false">关闭</el-button>
        <!-- <el-button type="primary" @click="save">保存</el-button> -->
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  name: 'SysSingleLeverConfig',
  data() {
    return {
      table: { url: '/singleleverconfig/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        rounds: [
          { required: true, message: '请输入补仓次数', trigger: 'blur' },
        ],

        up_stop_per: [
          { required: true, message: '请输入止盈百分比', trigger: 'blur' },
        ],

        down_stop_per: [
          { required: true, message: '请输入补仓百分比', trigger: 'blur' },
        ],

        lever: [{ required: true, message: '请输入倍数', trigger: 'blur' }],

        type_id: [
          {
            required: true,
            message: '请输入1高频2激进3保守4超级保守',
            trigger: 'blur',
          },
        ],
      },
      options: [
        {
          value: 1,
          label: '高频',
        },
        {
          value: 2,
          label: '激进',
        },
        {
          value: 3,
          label: '保守',
        },
        {
          value: 4,
          label: '超级保守',
        },
        {
          value: 5,
          label: '自定义',
        },
        {
          value: 6,
          label: '系统默认',
        },
      ],
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
        .get('/singleleverconfig/info?id=' + row.id)
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
            .post('/singleleverconfig/edit', this.editForm)
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
        this.$confirm('确定要删除选中的交易策略吗?', '提示', {
          type: 'warning',
        })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/singleleverconfig/delete', { id: ids })
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
          .post('/singleleverconfig/delete', { id: row.id })
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
.red {
  color: red;
}
.green {
  color: green;
}
</style>
