<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <!-- 搜索表单 -->
      <el-form
        :model="table.where"
        label-width="77px"
        class="ele-form-search"
        @keyup.enter.native="$refs.table.reload()"
        @submit.native.prevent
      >
        <el-row :gutter="15">
          <el-col :md="7" :sm="12">
            <el-form-item label="用户昵称:">
              <el-input
                v-model="table.where.user_name"
                placeholder="用户昵称"
                clearable
              />
            </el-form-item>
          </el-col>

          <el-col :md="8" :sm="12">
            <el-form-item label="流水类型:">
              <el-select
                clearable
                v-model="table.where.type_id"
                placeholder="请选择流水类型"
              >
                <el-option
                  v-for="item in contractType"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

          <el-col :md="8" :sm="12">
            <el-form-item label="开仓方向:">
              <el-select
                clearable
                v-model="table.where.open_type"
                placeholder="请选择开仓方向"
              >
                <el-option
                  v-for="item in directionoptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="15">
          <el-col :md="7" :sm="12">
            <el-form-item label="代币种类:">
              <el-input
                v-model="table.where.symbol"
                placeholder="请输入代币种类"
                clearable
              />
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="订单号:">
              <el-input
                v-model="table.where.order_id"
                placeholder="请输入订单号"
                clearable
              />
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
          v-if="permission.includes('sys:tradeprofit:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:tradeprofit:dall')"
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
            label="用户昵称"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="type_id"
            label="流水类型"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <div>{{ row.type_id == 1 ? '现货' : '合约' }}</div>
            </template>
          </el-table-column>

          <el-table-column
            prop="profit_usdt"
            label="盈利金额"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="platform_fee"
            label="扣除平台手续费"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="symbol"
            label="代币种类"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="order_id"
            label="平台订单号"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="open_type"
            label="开仓方向"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <div>{{ row.open_type == 2 ? '做多' : '做空' }}</div>
            </template>
          </el-table-column>

          <el-table-column
            prop="setup_id"
            label="setup_id"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="trade_time"
            label="卖出时间"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">{{
              (row.trade_time * 1000) | toDateString
            }}</template>
          </el-table-column>

          <el-table-column
            label="创建时间"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">{{
              (row.create_time * 1000) | toDateString
            }}</template>
          </el-table-column>
          <el-table-column
            label="更新时间"
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
                icon="el-icon-view"
                type="primary"
                :underline="false"
                v-if="permission.includes('sys:tradeprofit:edit')"
                >查看</el-link
              >
              <!-- <el-popconfirm
                title="确定要删除此交易日志吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:tradeprofit:delete')"
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
      :title="editForm.id ? '查看交易日志' : '新增交易日志'"
      :visible.sync="showEdit"
      width="900px"
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
              label="用户昵称:"
              prop="user_name"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.user_name"
                placeholder="请输入"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="11"
            ><el-form-item
              label="卖出时间："
              prop="trade_time"
              style="margin-bottom:20px;"
            >
              <template>{{
                (editForm.trade_time * 1000) | toDateString
              }}</template>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="盈利金额："
              prop="profit_usdt"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.profit_usdt"
                placeholder="请输入盈利金额"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="11">
            <el-form-item
              label="扣除手续费："
              prop="platform_fee"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.platform_fee"
                placeholder="请输入扣除平台手续费"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="代币种类："
              prop="symbol"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.symbol"
                placeholder="请输入代币种类"
                clearable
              /> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="平台订单号："
              prop="order_id"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.order_id"
                placeholder="请输入平台订单号"
                clearable
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="setup_id："
              prop="setup_id"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.setup_id"
                placeholder="请输入setup_id"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="6"
            ><el-form-item
              label="流水类型:"
              prop="type_id"
              style="margin-bottom:20px;"
            >
              <el-select
                disabled
                v-model="editForm.type_id"
                placeholder="请选择"
              >
                <el-option
                  v-for="item in contractType"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select> </el-form-item
          ></el-col>
          <el-col :sm="6">
            <el-form-item
              label="开仓方向:"
              prop="open_type"
              style="margin-bottom:20px;"
            >
              <el-select
                disabled
                v-model="editForm.open_type"
                placeholder="请选择"
              >
                <el-option
                  v-for="item in directionoptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select> </el-form-item
          ></el-col>
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
  name: 'SysTradeProfit',
  data() {
    return {
      table: { url: '/tradeprofit/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        user_id: [{ required: true, message: '请输入', trigger: 'blur' }],

        type_id: [
          {
            required: true,
            message: '请输入流水类型1现货 2合约',
            trigger: 'blur',
          },
        ],

        profit_usdt: [
          { required: true, message: '请输入盈利金额', trigger: 'blur' },
        ],

        platform_fee: [
          { required: true, message: '请输入扣除平台手续费', trigger: 'blur' },
        ],

        symbol: [
          { required: true, message: '请输入代币种类', trigger: 'blur' },
        ],

        order_id: [
          { required: true, message: '请输入平台订单号', trigger: 'blur' },
        ],

        open_type: [
          {
            required: true,
            message: '请输入开仓方向（针对合约使用）2做多 3做空',
            trigger: 'blur',
          },
        ],

        setup_id: [
          { required: true, message: '请输入setup_id', trigger: 'blur' },
        ],

        trade_time: [
          { required: true, message: '请输入卖出时间', trigger: 'blur' },
        ],
      },
      // 流水类型
      contractType: [
        {
          value: 1,
          label: '现货',
        },
        {
          value: 2,
          label: '合约',
        },
      ],
      // 开仓方向
      directionoptions: [
        {
          value: 2,
          label: '做多',
        },
        {
          value: 3,
          label: '做空',
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
        .get('/tradeprofit/info?id=' + row.id)
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
            .post('/tradeprofit/edit', this.editForm)
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
        this.$confirm('确定要删除选中的交易日志吗?', '提示', {
          type: 'warning',
        })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/tradeprofit/delete', { id: ids })
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
          .post('/tradeprofit/delete', { id: row.id })
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
