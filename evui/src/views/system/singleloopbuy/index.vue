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
                placeholder="用户名"
                clearable
              />
            </el-form-item>
          </el-col>

          <el-col :md="6" :sm="12">
            <el-form-item label="所属平台:">
              <el-select
                clearable
                v-model="table.where.platform_id"
                placeholder="请选择所属平台"
              >
                <el-option
                  v-for="item in platformoptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

          <el-col :md="6" :sm="12">
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
          <el-col :md="6" :sm="12">
            <el-form-item label="是否成交:">
              <el-select
                clearable
                v-model="table.where.is_deal"
                placeholder="请选择是否成交"
              >
                <el-option
                  v-for="item in isoptions"
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
          <el-col :md="6" :sm="12">
            <el-form-item label="订单号:">
              <el-input
                v-model="table.where.order_id"
                placeholder="请输入订单号"
                clearable
              />
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
            <el-form-item label="代币种类:">
              <el-input
                v-model="table.where.symbol"
                placeholder="请输入代币种类"
                clearable
              />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="15">
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
          v-if="permission.includes('sys:singleloopbuy:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:singleloopbuy:dall')"
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
            prop="rounds"
            label="层级"
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
            prop="qb_usdt"
            label="USDT数量"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="price"
            label="购买行情价"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="up_stop_per"
            label="止盈百分比"
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
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="scope">
              <div class="red">{{ scope.row.down_stop_per * 100 }}%</div>
            </template>
          </el-table-column>

          <el-table-column
            prop="up_stop_price"
            label="止盈价格"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="down_stop_price"
            label="补仓价格"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="qb_token"
            label="剩余数量"
            show-overflow-tooltip
            min-width="120"
          />

          <!-- <el-table-column
            prop="last_qb_token"
            label="购买数量剩余"
            show-overflow-tooltip
            min-width="120"
          /> -->

          <el-table-column
            prop="is_deal"
            label="是否成交"
            :resizable="false"
            min-width="120"
          >
            <template slot-scope="{ row }">
              <!-- <el-switch
                v-model="row.is_deal"
                @change="setIsDeal(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
              <div
                :style="{
                  color:
                    row.is_deal == 0
                      ? 'yellow'
                      : row.is_deal == 2
                      ? 'green'
                      : 'red',
                }"
              >
                {{
                  row.is_deal == 0
                    ? '未成交'
                    : row.is_deal == 2
                    ? '已成交'
                    : '成交失败'
                }}
              </div>
            </template>
          </el-table-column>

          <el-table-column
            prop="is_sell"
            label="是否卖出"
            :resizable="false"
            min-width="120"
          >
            <template slot-scope="{ row }">
              <!-- <el-switch
                v-model="row.is_sell"
                @change="setIsSell(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
              <div
                :style="{
                  color:
                    row.is_sell == 0
                      ? '#ffcc40'
                      : row.is_sell == 1
                      ? 'green'
                      : 'red',
                }"
              >
                {{
                  row.is_sell == 0
                    ? '未卖出'
                    : row.is_sell == 1
                    ? '已卖出'
                    : '卖出失败'
                }}
              </div>
            </template>
          </el-table-column>

          <!-- <el-table-column
            prop="is_next_buy"
            label="是否下次补仓"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <div>{{ row.is_next_buy == 1 ? '是' : '否' }}</div>
            </template>
          </el-table-column> -->

          <el-table-column
            prop="qb_fee"
            label="手续费"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="platform_id"
            label="所属平台"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <div>{{ row.platform_id == 2 ? '欧易' : '币安' }}</div>
            </template>
          </el-table-column>

          <el-table-column
            prop="lever"
            label="合约倍数"
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
              <div :style="{ color: row.open_type == 2 ? 'green' : 'red' }">
                {{ row.open_type == 2 ? '做多' : '做空' }}
              </div>
            </template>
          </el-table-column>

          <!-- <el-table-column
            prop="error"
            label="错误"
            show-overflow-tooltip
            min-width="120"
          /> -->

          <!-- <el-table-column
            prop="param"
            label="备注"
            show-overflow-tooltip
            min-width="120"
          /> -->

          <!-- <el-table-column
            prop="setup_id"
            label="setup_id"
            show-overflow-tooltip
            min-width="120"
          /> -->

          <!-- <el-table-column
            prop="is_auto"
            label="自动手动"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <div>
                {{ row.is_auto == 1 ? '自动' : '手动' }}
              </div>
            </template>
          </el-table-column> -->

          <el-table-column
            prop="create_time"
            label="创建时间"
            show-overflow-tooltip
            min-width="160"
          />
          <el-table-column
            prop="update_time"
            label="更新时间"
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
                icon="el-icon-view"
                type="primary"
                :underline="false"
                v-if="permission.includes('sys:singleloopbuy:edit')"
                >查看</el-link
              >
              <el-popconfirm
                title="确定要删除此交易买入吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:singleloopbuy:delete')"
                  >删除</el-link
                >
              </el-popconfirm>
            </template>
          </el-table-column> -->
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <!-- <el-dialog
      :title="editForm.id ? '查看交易买入' : '新增交易买入'"
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
              label="用户昵称："
              prop="user_name"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.user_name"
                placeholder="请输入用户昵称"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="11"
            ><el-form-item
              label="代币种类："
              prop="symbol"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.symbol"
                placeholder="请输入代币种类"
                clearable
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="补仓次数："
              prop="rounds"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.rounds"
                placeholder="请输入补仓次数"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              disabled
              label="平台订单号："
              prop="order_id"
              style="margin-bottom:20px"
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
              label="USDT数量："
              prop="qb_usdt"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.qb_usdt"
                placeholder="请输入USDT数量"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="11">
            <el-form-item
              label="购买行情价："
              prop="price"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.price"
                placeholder="请输入购买行情价"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="止盈百分比："
              prop="up_stop_per"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.up_stop_per"
                placeholder="请输入止盈百分比"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="补仓百分比："
              prop="down_stop_per"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.down_stop_per"
                placeholder="请输入补仓百分比"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="止盈价格"
              prop="up_stop_price"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.up_stop_price"
                placeholder="请输入止盈价格"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="补仓价格："
              prop="down_stop_price"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.down_stop_price"
                placeholder="请输入补仓价格"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="购买数量："
              prop="qb_token"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.qb_token"
                placeholder="请输入购买数量"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="11"
            ><el-form-item
              label="购买数量剩余："
              prop="last_qb_token"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.last_qb_token"
                placeholder="请输入购买数量剩余"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="是否成交："
              prop="is_deal"
              style="margin-bottom:20px"
            >
              <el-select
                disabled
                v-model="editForm.is_deal"
                placeholder="请选择"
              >
                <el-option
                  v-for="item in isoptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select> </el-form-item
          ></el-col>
          <el-col :sm="11"
            ><el-form-item
              label="是否卖出："
              prop="is_sell"
              style="margin-bottom:20px"
            >
              <el-select
                disabled
                v-model="editForm.is_sell"
                placeholder="请选择"
              >
                <el-option
                  v-for="item in saleoptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="是否执行下次补仓："
              prop="is_next_buy"
              style="margin-bottom:20px"
            >
              <el-select
                disabled
                v-model="editForm.is_next_buy"
                placeholder="请选择"
              >
                <el-option
                  v-for="item in whetheroptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="对应的手续费："
              prop="qb_fee"
              style="margin-bottom:20px"
            >
              <el-input
                v-model="editForm.qb_fee"
                placeholder="请输入对应的手续费"
                class="ele-fluid ele-text-left"
                disabled
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="所属平台："
              prop="platform_id"
              style="margin-bottom:20px"
            >
              <el-select
                disabled
                v-model="editForm.platform_id"
                placeholder="请选择"
              >
                <el-option
                  v-for="item in platformoptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="合约倍数："
              prop="lever"
              style="margin-bottom:20px"
            >
              <el-input
                v-model="editForm.lever"
                placeholder="请输入合约倍数"
                class="ele-fluid ele-text-left"
                disabled
              />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="开仓方向："
              prop="open_type"
              style="margin-bottom:20px"
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
          <el-col :sm="11">
            <el-form-item
              label="错误："
              prop="error"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.error"
                placeholder="请输入错误"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11"
            ><el-form-item
              label="自动手动："
              prop="is_auto"
              style="margin-bottom:20px"
            >
              <el-select
                disabled
                v-model="editForm.is_auto"
                placeholder="请选择"
              >
                <el-option
                  v-for="item in options"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                >
                </el-option>
              </el-select> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="setup_id："
              prop="setup_id"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                v-model="editForm.setup_id"
                placeholder="请输入setup_id"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-rpw>
          <el-col :sm="22">
            <el-form-item
              label="备注："
              prop="param"
              style="margin-bottom:20px"
            >
              <el-input
                disabled
                type="textarea"
                v-model="editForm.param"
                placeholder="请输入备注"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
        </el-rpw>
      </el-form>
      <div slot="footer">
        <el-button type="primary" @click="showEdit = false">关闭</el-button>
      </div>
    </el-dialog> -->
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  name: 'SysSingleLoopBuy',
  data() {
    return {
      create_time: '',
      table: { url: '/singleloopbuy/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        user_id: [
          { required: true, message: '请输入管理用户', trigger: 'blur' },
        ],

        symbol: [
          { required: true, message: '请输入代币种类', trigger: 'blur' },
        ],

        rounds: [
          { required: true, message: '请输入补仓次数', trigger: 'blur' },
        ],

        order_id: [
          { required: true, message: '请输入平台订单号', trigger: 'blur' },
        ],

        qb_usdt: [
          { required: true, message: '请输入USDT数量', trigger: 'blur' },
        ],

        price: [
          { required: true, message: '请输入购买行情价', trigger: 'blur' },
        ],

        up_stop_per: [
          { required: true, message: '请输入止盈百分比', trigger: 'blur' },
        ],

        down_stop_per: [
          { required: true, message: '请输入补仓百分比', trigger: 'blur' },
        ],

        up_stop_price: [
          { required: true, message: '请输入止盈价格', trigger: 'blur' },
        ],

        down_stop_price: [
          { required: true, message: '请输入补仓价格', trigger: 'blur' },
        ],

        qb_token: [
          { required: true, message: '请输入购买数量', trigger: 'blur' },
        ],

        last_qb_token: [
          { required: true, message: '请输入购买数量剩余', trigger: 'blur' },
        ],

        is_deal: [
          { required: true, message: '请选择是否成交', trigger: 'blur' },
        ],

        is_sell: [
          { required: true, message: '请选择是否卖出', trigger: 'blur' },
        ],

        is_next_buy: [
          {
            required: true,
            message: '请输入是否执行下次补仓',
            trigger: 'blur',
          },
        ],

        qb_fee: [
          { required: true, message: '请输入对应的手续费', trigger: 'blur' },
        ],

        platform_id: [
          { required: true, message: '请输入所属平台', trigger: 'blur' },
        ],

        lever: [
          {
            required: true,
            message: '请输入合约倍数（针对合约使用）',
            trigger: 'blur',
          },
        ],

        open_type: [
          {
            required: true,
            message: '请输入开仓方向（针对合约使用）2做多 3做空',
            trigger: 'blur',
          },
        ],

        error: [{ required: true, message: '请输入错误', trigger: 'blur' }],

        param: [{ required: true, message: '请输入备注', trigger: 'blur' }],

        setup_id: [
          { required: true, message: '请输入setup_id', trigger: 'blur' },
        ],

        is_auto: [
          { required: true, message: '请输入自动或者手动', trigger: 'blur' },
        ],
      },

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
      // 所属平台
      platformoptions: [
        {
          value: 2,
          label: '欧易',
        },
        {
          value: 3,
          label: '币安',
        },
      ],
      // 0否 1是
      whetheroptions: [
        {
          value: 0,
          label: '否',
        },
        {
          value: 1,
          label: '是',
        },
      ],
      // 是否自动
      options: [
        {
          value: 0,
          label: '手动',
        },
        {
          value: 1,
          label: '自动',
        },
      ],
      // 是否成交
      isoptions: [
        {
          value: 0,
          label: '未成交',
        },
        {
          value: 2,
          label: '已成交',
        },
        {
          value: 3,
          label: '成交失败',
        },
      ],
      // 是否卖出
      saleoptions: [
        {
          value: 0,
          label: '未卖出',
        },
        {
          value: 1,
          label: '已卖出',
        },
        {
          value: 4,
          label: '卖出失败',
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
        .get('/singleloopbuy/info?id=' + row.id)
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
            .post('/singleloopbuy/edit', this.editForm)
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
        this.$confirm('确定要删除选中的交易买入吗?', '提示', {
          type: 'warning',
        })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/singleloopbuy/delete', { id: ids })
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
          .post('/singleloopbuy/delete', { id: row.id })
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
     * 更改是否成交
     */
    setIsDeal(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/singleloopbuy/setIsDeal', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({ type: 'success', message: res.data.msg })
          } else {
            row.is_deal = !row.is_deal ? 2 : 1
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          loading.close()
          this.$message.error(e.message)
        })
    },

    /**
     * 更改是否卖出
     */
    setIsSell(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/singleloopbuy/setIsSell', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({ type: 'success', message: res.data.msg })
          } else {
            row.is_sell = !row.is_sell ? 2 : 1
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
.red {
  color: red;
}
.green {
  color: green;
}
.yellow {
  color: yellow;
}
</style>
