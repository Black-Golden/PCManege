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
          <el-col :md="5" :sm="10">
            <el-form-item label="用户名:">
              <el-input
                v-model="table.where.username"
                placeholder="请输入用户名"
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
              <!-- <span class="demonstration">搜索时间</span> -->
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
      <!-- <el-button
        @click="showEdit = true"
        type="primary"
        icon="el-icon-plus"
        class="ele-btn-icon"
        v-if="permission.includes('sys:member:add')"
        >添加</el-button
      >
      <el-button
        @click="remove()"
        type="danger"
        icon="el-icon-delete"
        class="ele-btn-icon"
        v-if="permission.includes('sys:member:dall')"
        >批量删除</el-button
      > -->
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
        height="calc(100vh - 265px)"
        highlight-current-row
      >
        <template slot-scope="{ index }">
<!--          <el-table-column type="selection" width="45" fixed="left" />-->
          <el-table-column
            type="index"
            :index="index"
            label="编号"
            width="60"

            show-overflow-tooltip
          />

          <!-- <el-table-column
            prop="username"
            label="会员手机"

            show-overflow-tooltip
            min-width="130"
          /> -->
          <el-table-column label="头像" min-width="60">
            <template slot-scope="{ row }">
              <el-avatar shape="square" :size="25" :src="row.headimg" />
            </template>
          </el-table-column>
          <el-table-column
            prop="username"
            label="用户名"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="recode"
            label="推荐码"
            show-overflow-tooltip
            min-width="130"
          />
          <!--          <el-table-column prop="rank" label="等级"   show-overflow-tooltip min-width="120"/>-->
          <el-table-column label="等级" min-width="105">
            <template slot-scope="{ row }">
              <div v-for="(item, index) in rank_name" :key="index">
                <div v-if="row.rank == rank_name[index].id">
                  {{ item.name }}
                </div>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="status" label="会员状态" sortable="custom" :resizable="false" min-width="120">
            <template slot-scope="{row}">
              <el-switch v-model="row.status" @change="editStatus(row)" :active-value="1" :inactive-value="2"/>
            </template>
          </el-table-column>

          <el-table-column
            prop="qb_usdt"
            label="钱包金额"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="qb_profit_today"
            label="今日盈利"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="qb_profit_all"
            label="历史盈利"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="team_all"
            label="团队人数"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="team_all"
            label="归集"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <el-link
                type="primary"
                icon="el-icon-edit"
                @click="collection(row)"
                >归集</el-link
              >
            </template>
          </el-table-column>
          <el-table-column
            prop="team_all"
            label="欧易交易余额"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <el-link
                type="primary"
                icon="el-icon-view"
                @click="tradeType(2, row)"
                >查看</el-link
              >
            </template>
          </el-table-column>
          <el-table-column
            prop="team_all"
            label="币安交易余额"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">
              <el-link
                type="primary"
                icon="el-icon-view"
                @click="tradeType(3, row)"
                >查看</el-link
              >
            </template>
          </el-table-column>

          <el-table-column
            prop="qb_indirect_today"
            label="团队今日收益"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="qb_team_all"
            label="团队总收入"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="team_one"
            label="直推人数"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="qb_child_all"
            label="直推总收益"
            show-overflow-tooltip
            min-width="120"
          />

          <!--          <el-table-column label="头像" min-width="60"  >-->
          <!--            <template slot-scope="{row}">-->
          <!--              <el-avatar shape="square" :size="35" :src="row.headimg"/>-->
          <!--            </template>-->
          <!--          </el-table-column>-->
          <!--          <el-table-column label="充值地址" min-width="60"  >-->
          <!--            <template slot-scope="{row}">-->
          <!--              <el-avatar shape="square" :size="35" :src="row.addr"/>-->
          <!--            </template>-->
          <!--          </el-table-column>-->

          <el-table-column
            label="注册时间"
            show-overflow-tooltip
            min-width="160"
          >
            <template slot-scope="{ row }">{{
              (row.reg_time * 1000) | toDateString
            }}</template>
          </el-table-column>

          <el-table-column
            label="操作"
            width="100px"
            :resizable="false"
            fixed="right"
          >
            <template slot-scope="{ row }">
              <el-row style="padding-bottom:10px">
                <el-button
                  type="primary"
                  icon="el-icon-view"
                  class="ele-btn-icon"
                  size="mini"
                  @click="seemore(row)"
                  >查看</el-button
                >
              </el-row>
              <el-row style="padding-bottom:10px">
                <el-button
                  v-if="permission.includes('sys:member:edit')"
                  type="primary"
                  icon="el-icon-edit"
                  class="ele-btn-icon"
                  size="mini"
                  @click="edit(row)"
                  >修改</el-button
                ></el-row
              >
              <el-row style="padding-bottom:10px">
                <el-button
                  v-if="permission.includes('sys:member:edit')"
                  type="primary"
                  icon="el-icon-edit"
                  class="ele-btn-icon"
                  size="mini"
                  @click="chongzhi(row)"
                  >充值</el-button
                ></el-row
              >

              <el-row style="padding-bottom:10px">
                <el-button
                  v-if="permission.includes('sys:member:edit')"
                  type="primary"
                  icon="el-icon-edit"
                  class="ele-btn-icon"
                  size="mini"
                  @click="openTRX(row)"
                  >充值TRX
                </el-button></el-row
              >
              <el-row>
                <el-button
                  v-if="permission.includes('sys:member:edit')"
                  type="primary"
                  icon="el-icon-edit"
                  class="ele-btn-icon"
                  size="mini"
                  @click="getbalance(row)"
                  >钱包余额</el-button
                ></el-row
              >
              <el-row>
                <el-button
                  v-if="permission.includes('sys:member:edit')"
                  type="primary"
                  icon="el-icon-edit"
                  class="ele-btn-icon"
                  size="mini"
                  @click="getbalance(row)"
                  >重置密码</el-button
                ></el-row
              >
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 交易所余额 -->
    <el-dialog
      :title="viewType == 2 ? '查看欧易交易余额' : '查看币安交易余额'"
      :visible.sync="exchangeShow"
      width="450px"
      :destroy-on-close="true"
      custom-class="ele-dialog-form"
      :lock-scroll="false"
    >
      <div style="margin-bottom:20px">
        当前{{ viewType == 2 ? '欧易' : '币安' }}交易余额为：{{ allSum }}
      </div>

      <div slot="footer">
        <el-button type="primary" @click="exchangeShow = false">关闭</el-button>
      </div>
    </el-dialog>
    <!-- 归集 -->
    <el-dialog
      title="归集"
      :visible.sync="guijiShow"
      width="40%"
      :destroy-on-close="true"
      :lock-scroll="false"
    >
      <el-form
        :model="trxForm"
        ref="subclass"
        :rules="editRules"
        label-width="100px"
      >
        <el-form-item label="地址:">
          <el-input
            style="width:80%"
            :rows="2"
            placeholder="请填写归集地址"
            v-model="subclass.address"
          ></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="guijiShow = false">取消</el-button>
        <el-button type="primary" @click="guijidizhi">保存</el-button>
      </div>
    </el-dialog>
    <!-- 编辑弹窗 -->
    <el-dialog
      :title="editForm.id ? '修改会员' : '添加会员'"
      :visible.sync="showEdit"
      width="900px"
      @closed="editForm = {}"
      :destroy-on-close="true"
      custom-class="ele-dialog-form"
      :lock-scroll="false"
    >
      <el-form
        :model="editForm"
        ref="editForm"
        :rules="editRules"
        label-width="auto"
      >
        <!-- <el-form-item label="会员头像:">
          <uploadImage :limit="1" v-model="editForm.avatar"></uploadImage>
        </el-form-item> -->
        <el-row>
          <el-col :sm="11"> </el-col>
          <el-col :sm="11"> </el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="用户名:"
              prop="username"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.username"
                placeholder="请输入会员手机"
                clearable
              /> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="当前等级："
              prop="rank"
              style="margin-bottom:20px;margin-left:20px;"
            >
              <el-select
                v-model="editForm.rank"
                style="width: 100%;"
                placeholder="请选择当前等级"
              >
                <el-option
                  v-for="item in rank_name"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer">
        <el-button @click="showEdit = false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </div>
    </el-dialog>
    <el-dialog
      title="查看钱包余额"
      :visible.sync="balanceShow"
      width="450px"
      :destroy-on-close="true"
      custom-class="ele-dialog-form"
      :lock-scroll="false"
    >
      <div style="margin-bottom:20px">剩余USDT：{{ balance.usdt }}</div>
      <div style="margin-bottom:20px">剩余TRX：{{ balance.trx }}</div>
      <div style="margin-bottom:20px">钱包地址：{{ balance.address }}</div>

      <div slot="footer">
        <el-button type="primary" @click="balanceShow = false">关闭</el-button>
      </div>
    </el-dialog>
    <!-- 查看信息 -->
    <el-dialog
      :showClose="false"
      :close-on-click-modal="false"
      top="3%"
      title="查看信息"
      v-if="showsee"
      :visible="true"
      width="70%"
      :destroy-on-close="true"
      :lock-scroll="false"
    >
      <!-- <el-tab-pane label="报警记录" name="third">
            <ele-data-table
              height="400px"
              :data="devicewarningloglist"
              stripe
              style="width: 100%"
            >
              <template slot-scope="{ index }">
                <el-table-column
                  type="index"
                  :index="index"
                  label="编号"

                  fixed="left"
                  show-overflow-tooltip
                />
                <el-table-column

                  label="通知类型"
                  prop="warning_type"
                >
                  <template slot-scope="{ row }">
                    <span v-if="row.warning_type == 1">电池电量预警</span>
                    <span v-if="row.warning_type == 2">温度预警</span>
                    <span v-if="row.warning_type == 3">湿度预警</span>
                    <span v-if="row.warning_type == 4">点位偏移预警</span>
                  </template>
                </el-table-column>
                <el-table-column
                  prop="warning_content"
                  label="触发条件"

                  show-overflow-tooltip
                />
                <el-table-column
                  label="报警时间"

                  show-overflow-tooltip
                >
                  <template slot-scope="{ row }">{{
                    (row.warning_time * 1000) | toDateString
                  }}</template>
                </el-table-column>
                <el-table-column
                  prop="contact_name"
                  label="报警联系人"

                  show-overflow-tooltip
                />

                <el-table-column
                  prop="warning_msg"
                  label="报警方式"

                  show-overflow-tooltip
                />
                <el-table-column
                  label="处理时间"

                  show-overflow-tooltip
                >
                  <template slot-scope="{ row }">{{
                    (row.handle_time * 1000) | toDateString
                  }}</template>
                </el-table-column>
                <el-table-column   label="状态" prop="status">
                  <template slot-scope="{ row }">
                    <span v-if="row.status == 1" style="color: red;"
                      >待处理</span
                    >
                    <span v-if="row.status == 2" style="color: #00FF80;"
                      >已处理</span
                    >
                  </template>
                </el-table-column>
                <el-table-column
                  label="操作"

                  :resizable="false"
                  fixed="right"
                >
                  <template slot-scope="{ row }">
                    <el-button
                      v-if="row.status == 1"
                      @click="chuli(row)"
                      type="success"
                      class="ele-btn-icon"
                      size="small"
                      >处理
                    </el-button>
                    <el-button
                      v-if="row.status == 2"
                      type="info"
                      class="ele-btn-icon"
                      size="small"
                      >处理
                    </el-button>
                  </template>
                </el-table-column>
              </template>
            </ele-data-table>
          </el-tab-pane> -->
      <el-tabs v-model="activeName" @tab-click="handleClick">
        <el-tab-pane label="充值记录" name="first">
          <ele-data-table
            height="400px"
            :data="pay_rank"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
                fixed="left"
                show-overflow-tooltip
              />

              <el-table-column
                prop="user_name"
                label="充值用户"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="transactionHash"
                label="交易哈希"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="from"
                label="来源地址"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="to"
                label="充值地址"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="value"
                label="金额"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="create_time"
                label="创建时间"
                show-overflow-tooltip
                min-width="120"
              />
              <!-- <el-table-column prop="update_time" label="更新时间"   show-overflow-tooltip min-width="120"/> -->
              <!--          <el-table-column label="操作" width="130px"   :resizable="false"  fixed="right">-->
              <!--            <template slot-scope="{row}">-->
              <!--&lt;!&ndash;              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quanttransfer:edit')">修改</el-link>&ndash;&gt;-->
              <!--              <el-popconfirm title="确定要删除此充值吗？" @confirm="remove(row)" class="ele-action">-->
              <!--                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quanttransfer:delete')">删除</el-link>-->
              <!--              </el-popconfirm>-->
              <!--            </template>-->
              <!--          </el-table-column>-->
            </template>
          </ele-data-table>
        </el-tab-pane>
        <el-tab-pane label="提现记录" name="second">
          <ele-data-table
            height="400px"
            :data="Record"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
                fixed="left"
                show-overflow-tooltip
              />

              <el-table-column
                prop="user_name"
                label="提现用户"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="amount"
                label="提现金额"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="fee"
                label="提现手续费"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="chk_time"
                label="审核时间"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="is_check"
                label="审核状态"
                :resizable="false"
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <span v-if="row.is_check == 0" style="color: red"
                    >未审核</span
                  >
                  <span v-if="row.is_check == 1" style="color: green"
                    >已审核</span
                  >
                  <span v-if="row.is_check == 2" style="color: blue"
                    >已拒绝</span
                  >
                </template>
              </el-table-column>

              <el-table-column
                prop="addr_cash"
                label="提现地址"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="txHash"
                label="交易哈希"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="create_time"
                label="创建时间"
                show-overflow-tooltip
                min-width="120"
              />
              <el-table-column
                prop="update_time"
                label="更新时间"
                show-overflow-tooltip
                min-width="120"
              />
            </template>
          </ele-data-table>
        </el-tab-pane>
        <el-tab-pane label="转账记录" name="third">
          <ele-data-table
            height="400px"
            :data="transferRecord"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
                fixed="left"
                show-overflow-tooltip
              />

              <el-table-column
                prop="user_from"
                label="发起人"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="user_to"
                label="接受人"
                show-overflow-tooltip
                min-width="120"
              />

              <!-- <el-table-column
            prop="wallet"
            label="钱包类型"

            show-overflow-tooltip
            min-width="120"
          /> -->

              <el-table-column
                prop="amount"
                label="转入数量"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="fee"
                label="手续费"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="score_begin"
                label="转入前数量"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="score_end"
                label="转入后数量"
                show-overflow-tooltip
                min-width="120"
              />

              <!-- <el-table-column
            prop="oper"
            label="备注"

            show-overflow-tooltip
            min-width="120"
          /> -->

              <el-table-column
                prop="create_time"
                label="创建时间"
                show-overflow-tooltip
                min-width="120"
              />
              <el-table-column
                prop="update_time"
                label="更新时间"
                show-overflow-tooltip
                min-width="120"
              />
            </template>
          </ele-data-table>
        </el-tab-pane>
        <el-tab-pane label="当前策略" name="third1">
          <ele-data-table
            height="400px"
            :data="strategyList"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
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
                label="代币类型"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="lever"
                label="合约倍数"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="qb_token"
                label="购买数量"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="is_run"
                label="是否运行"
                :resizable="false"
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_run"
                @change="setIsRun(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>{{ row.is_run == 0 ? '否' : '是' }}</div>
                </template>
              </el-table-column>

              <el-table-column
                prop="is_loop"
                label="是否循环"
                :resizable="false"
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_loop"
                @change="setIsLoop(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>{{ row.is_loop == 0 ? '否' : '是' }}</div>
                </template>
              </el-table-column>

              <el-table-column
                prop="platform_id"
                label="所属平台"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_loop"
                @change="setIsLoop(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>{{ row.platform_id == 2 ? '欧易' : '币安' }}</div>
                </template>
              </el-table-column>

              <el-table-column
                prop="open_type"
                label="开仓方向"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_loop"
                @change="setIsLoop(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div :style="{ color: row.open_type == 2 ? 'green' : 'red' }">
                    {{ row.open_type == 2 ? '做多' : '做空' }}
                  </div>
                </template>
              </el-table-column>

              <el-table-column
                prop="is_open_down"
                label="是否补仓"
                :resizable="false"
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_open_down"
                @change="setIsOpenDown(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>{{ row.is_open_down == 0 ? '否' : '是' }}</div>
                </template>
              </el-table-column>

              <el-table-column
                prop="is_singke_cut"
                label="是否单向斩仓"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_open_down"
                @change="setIsOpenDown(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>{{ row.is_singke_cut == 0 ? '否' : '是' }}</div>
                </template>
              </el-table-column>

              <el-table-column
                prop="is_double_cut"
                label="是否双向斩仓"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_open_down"
                @change="setIsOpenDown(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>{{ row.is_double_cut == 0 ? '否' : '是' }}</div>
                </template>
              </el-table-column>

              <el-table-column
                prop="is_other_cut"
                label="是否跨币斩仓"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_open_down"
                @change="setIsOpenDown(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>{{ row.is_other_cut == 0 ? '否' : '是' }}</div>
                </template>
              </el-table-column>

              <el-table-column
                prop="type_id"
                label="交易策略"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_open_down"
                @change="setIsOpenDown(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
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

              <!-- <el-table-column
            prop="min_token"
            label="每笔仓位"
            show-overflow-tooltip
            min-width="120"
          /> -->

              <el-table-column
                prop="rounds"
                label="交易层级"
                show-overflow-tooltip
                min-width="120"
              />

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
            </template>
          </ele-data-table>
        </el-tab-pane>
        <el-tab-pane label="钱包流水" name="third2">
          <ele-data-table
            height="400px"
            :data="financialDetails"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
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
                prop="type_name"
                label="交易类型"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="num"
                label="金额"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="scope">
                  <div :style="{ color: scope.row.num > 0 ? 'green' : 'red' }">
                    {{ scope.row.num }}
                  </div>
                </template>
              </el-table-column>
              <el-table-column
                prop="last_num"
                label="余额"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="memo"
                label="备注"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="create_time"
                label="创建时间"
                show-overflow-tooltip
                min-width="120"
              />
              <el-table-column
                prop="update_time"
                label="更新时间"
                show-overflow-tooltip
                min-width="120"
              />
              <!--          <el-table-column label="操作" width="130px"   :resizable="false"  fixed="right">-->
              <!--            <template slot-scope="{row}">-->
              <!--&lt;!&ndash;              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantwallet:edit')">修改</el-link>&ndash;&gt;-->
              <!--              <el-popconfirm title="确定要删除此钱包奖励吗？" @confirm="remove(row)" class="ele-action">-->
              <!--                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantwallet:delete')">删除</el-link>-->
              <!--              </el-popconfirm>-->
              <!--            </template>-->
              <!--          </el-table-column>-->
            </template>
          </ele-data-table>
        </el-tab-pane>

        <el-tab-pane label="交易日志" name="third4">
          <ele-data-table
            height="400px"
            :data="Transaction_Log"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
                fixed="left"
                show-overflow-tooltip
              />

              <el-table-column
                prop="content"
                label="日志内容"
                show-overflow-tooltip
                min-width="300"
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
                prop="platform_id"
                label="交易所类型"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <div>{{ row.platform_id == 2 ? '欧易' : '币安' }}</div>
                </template>
              </el-table-column>

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

              <el-table-column
                prop="type"
                label="买卖类型"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <div>
                    {{ row.type == 1 ? '买' : row.type == 2 ? '卖' : '普通' }}
                  </div>
                </template>
              </el-table-column>

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
            </template>
          </ele-data-table>
        </el-tab-pane>
        <el-tab-pane label="交易买入记录" name="fourth">
          <ele-data-table
            height="400px"
            :data="deal_buy_log"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
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
                          ? '#ffcc40'
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
            </template> </ele-data-table
        ></el-tab-pane>
        <el-tab-pane label="交易卖出记录" name="fourth1">
          <ele-data-table
            height="400px"
            :data="deal_sale_log"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
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
                prop="order_id"
                label="平台订单号"
                show-overflow-tooltip
                min-width="120"
              />

              <!-- <el-table-column
            prop="buy_qb_usdt"
            label="自动USDT数量"
            show-overflow-tooltip
            min-width="120"
          /> -->

              <el-table-column
                prop="buy_qb_token"
                label="买的时候数量"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="qb_token"
                label="卖出数量"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="buy_price"
                label="买入行情价"
                show-overflow-tooltip
                min-width="120"
              />
              <el-table-column
                prop="price"
                label="卖出行情价"
                show-overflow-tooltip
                min-width="120"
              />
              <!-- <el-table-column
            prop="qb_usdt"
            label="USDT数量"
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
                prop="is_bonus"
                label="是否结算"
                :resizable="false"
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_bonus"
                @change="setIsBonus(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>
                    {{
                      row.is_bonus == 0
                        ? '未处理'
                        : row.is_bonus == 1
                        ? '已结算'
                        : '结算失败'
                    }}
                  </div>
                </template>
              </el-table-column>

              <el-table-column
                prop="qb_fee"
                label="对应手续费"
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
                  <!-- <el-switch
                v-model="row.is_bonus"
                @change="setIsBonus(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>
                    {{ row.platform_id == 2 ? '欧易' : '币安' }}
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

              <el-table-column
                prop="open_type"
                label="开仓方向"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_bonus"
                @change="setIsBonus(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div :style="{ color: row.open_type == 2 ? 'green' : 'red' }">
                    {{ row.open_type == 2 ? '做多' : '做空' }}
                  </div>
                </template>
              </el-table-column>

              <!-- <el-table-column
            prop="setup_id"
            label="setup_id"
            show-overflow-tooltip
            min-width="120"
          /> -->

              <!-- <el-table-column
            prop="buy_id"
            label="买入自动id"
            show-overflow-tooltip
            min-width="120"
          /> -->

              <el-table-column
                prop="is_cut"
                label="是否斩仓"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <!-- <el-switch
                v-model="row.is_bonus"
                @change="setIsBonus(row)"
                :active-value="1"
                :inactive-value="2"
              /> -->
                  <div>
                    {{ row.is_cut == 0 ? '否' : '是' }}
                  </div>
                </template>
              </el-table-column>

              <!-- <el-table-column
            prop="cut_type_id"
            label="斩仓类型"
            show-overflow-tooltip
            min-width="120"
          /> -->
              <!--
          <el-table-column
            prop="cut_sell_id"
            label="斩仓ID"
            show-overflow-tooltip
            min-width="120"
          /> -->

              <el-table-column
                prop="profit_usdt"
                label="盈利金额"
                show-overflow-tooltip
                min-width="120"
              />

              <!-- <el-table-column
            prop="is_auto"
            label="自动手动"
            show-overflow-tooltip
            min-width="120"
          >
            <template slot-scope="{ row }">

              <div>
                {{ row.is_auto == 0 ? '手动' : '自动' }}
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
            </template> </ele-data-table
        ></el-tab-pane>
        <el-tab-pane label="下级分属" name="first9">
          <template>
            <el-table
              :data="personnelList"
              style="width: 100%"
              row-key="id"
              border
              lazy
              :load="load"
              :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
            >
              <el-table-column
                prop="username"
                label="用户名"
                show-overflow-tooltip
                min-width="80"
              />
              <el-table-column label="头像" min-width="60">
                <template slot-scope="{ row }">
                  <el-avatar shape="square" :size="25" :src="row.headimg" />
                </template>
              </el-table-column>

              <el-table-column
                prop="recode"
                label="推荐码"
                show-overflow-tooltip
                min-width="130"
              />
              <el-table-column
                label="注册时间"
                show-overflow-tooltip
                min-width="160"
              >
                <template slot-scope="{ row }">{{
                  (row.reg_time * 1000) | toDateString
                }}</template>
              </el-table-column>
            </el-table>
          </template>
          <!-- <ele-data-table
            height="400px"
            :data="personnelList"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column
                type="selection"
                width="45"

                fixed="left"
              />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"

                fixed="left"
                show-overflow-tooltip
              />

              <el-table-column
                prop="user_name"
                label="充值用户"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="transactionHash"
                label="交易哈希"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="from"
                label="来源地址"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="to"
                label="充值地址"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="value"
                label="金额"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="create_time"
                label="创建时间"
                show-overflow-tooltip
                min-width="120"
              />
            </template>
          </ele-data-table> -->
        </el-tab-pane>
        <el-tab-pane label="trx充值数量" name="first39">
          <ele-data-table
            height="400px"
            :data="trxlog"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
                fixed="left"
                show-overflow-tooltip
              />

              <el-table-column
                prop="user_id"
                label="用户名"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="num"
                label="数量"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                label="执行状态"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <div v-if="row.status == 0" style="color:#ffce46">
                    待执行
                  </div>
                  <div v-if="row.status == 1" style="color:green">已转账</div>
                  <div v-if="row.status == 2" style="color:red">失败</div>
                </template>
              </el-table-column>

              <el-table-column
                prop="txHash"
                label="交易哈希"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="address"
                label="充值地址"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                label="创建时间"
                show-overflow-tooltip
                min-width="160"
              >
                <template slot-scope="{ row }"
                  ><div>{{ row.create_time }}</div></template
                >
              </el-table-column>
              <el-table-column
                label="更新时间"
                show-overflow-tooltip
                min-width="160"
              >
                <template slot-scope="{ row }"
                  ><div>
                    {{ row.update_time }}
                  </div></template
                >
              </el-table-column>
            </template>
          </ele-data-table>
        </el-tab-pane>
        <el-tab-pane label="归集usdt" name="first91">
          <ele-data-table
            height="400px"
            :data="usdtlog"
            stripe
            style="width: 100%"
          >
            <template slot-scope="{ index }">
              <el-table-column type="selection" width="45" fixed="left" />
              <el-table-column
                type="index"
                :index="index"
                label="编号"
                width="60"
                fixed="left"
                show-overflow-tooltip
              />

              <el-table-column
                prop="user_id"
                label="用户名"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="num"
                label="数量"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="status"
                label="执行状态"
                show-overflow-tooltip
                min-width="120"
              >
                <template slot-scope="{ row }">
                  <div v-if="row.status == 0" style="color:#ffce46">
                    待执行
                  </div>
                  <div v-if="row.status == 1" style="color:green">已转账</div>
                  <div v-if="row.status == 2" style="color:red">失败</div>
                </template></el-table-column
              >

              <el-table-column
                prop="txHash"
                label="交易哈希"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                prop="address"
                label="归集地址"
                show-overflow-tooltip
                min-width="120"
              />

              <el-table-column
                label="创建时间"
                show-overflow-tooltip
                min-width="160"
              >
                <template slot-scope="{ row }"
                  ><div>{{ row.create_time }}</div></template
                >
              </el-table-column>
              <el-table-column
                label="更新时间"
                show-overflow-tooltip
                min-width="160"
              >
                <template slot-scope="{ row }"
                  ><div>{{ row.update_time }}</div></template
                >
              </el-table-column>
            </template>
          </ele-data-table>
        </el-tab-pane>
      </el-tabs>
      <div slot="footer">
        <el-button @click="showsee = false">取消</el-button>
      </div>
    </el-dialog>
    <el-dialog
      title="充值TRX"
      :visible.sync="TRXshow"
      width="40%"
      @closed="trxForm = {}"
      :destroy-on-close="true"
      :lock-scroll="false"
    >
      <el-form
        :model="trxForm"
        ref="trxForm"
        :rules="editRules"
        label-width="100px"
      >
        <el-form-item label="用户信息:" prop="username">
          <el-input
            disabled
            style="width:80%"
            :rows="2"
            placeholder="请输入用户信息"
            v-model="trxForm.username"
          ></el-input>
        </el-form-item>
        <el-form-item label="充值私钥:" prop="private_key">
          <el-input
            type="text"
            style="width:80%"
            :rows="2"
            placeholder="请填写充值私钥"
            v-model="trxForm.private_key"
          ></el-input>
        </el-form-item>
        <el-form-item label="trx数量:" prop="trx">
          <el-input
            type="text"
            style="width:80%"
            :rows="2"
            placeholder="请输入备注内容"
            v-model="trxForm.trx"
          ></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="TRXshow = false">取消</el-button>
        <el-button type="primary" @click="chongzhiTRX">保存</el-button>
      </div>
    </el-dialog>
    <el-dialog
      title="充值"
      :visible.sync="genjinshowEdit"
      width="40%"
      @closed="fenjieForm = {}"
      :destroy-on-close="true"
      :lock-scroll="false"
    >
      <el-form
        :model="fenjieForm"
        ref="fenjieForm"
        :rules="editRules"
        label-width="100px"
      >
        <el-form-item label="输入金额:" prop="amount">
          <el-input
            style="width:80%"
            :rows="2"
            placeholder="请输入充值金额"
            v-model="fenjieForm.amount"
          ></el-input>
        </el-form-item>
        <el-form-item label="填写备注:" prop="memo">
          <el-input
            type="text"
            style="width:80%"
            :rows="2"
            placeholder="请输入备注内容"
            v-model="fenjieForm.memo"
          ></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="genjinshowEdit = false">取消</el-button>
        <el-button type="primary" @click="zhuangtaisave">保存</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import cityData from '@/utils/cityData'
// import uploadImage from '@/components/uploadImage'
import { mapGetters } from 'vuex'
export default {
  name: 'SysMember',
  data() {
    return {
      allSum: null,
      viewType: '',
      exchangeShow: false,
      transactionList: {},
      subclass: {
        id: null,
        address: '',
      }, //归集地址
      guijiShow: false,
      TRXshow: false, //trx
      trxForm: {},
      tableData1: [
        {
          id: 1,
          date: '2016-05-02',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄',
        },
        {
          id: 2,
          date: '2016-05-04',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1517 弄',
        },
        {
          id: 3,
          date: '2016-05-01',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1519 弄',
          hasChildren: true,
        },
        {
          id: 4,
          date: '2016-05-03',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1516 弄',
        },
      ],
      personnelList: [],
      create_time: '',
      balance: {}, //钱包余额
      balanceShow: false, //控制钱包余额
      pay_rank: [],
      trxlog: [],
      usdtlog: [],
      Record: [], //提现记录
      transferRecord: [], //转账记录
      strategyList: [], //当前策略列表
      financialDetails: [], //资金流水
      Transaction_Log: [], //交易日志
      deal_buy_log: [], //交易买入记录
      deal_sale_log: [], //交易卖出记录
      activeName: 'first',
      showsee: false,
      fenjieForm: {},
      rank_name: '',
      /* citySelect */
      cityData: cityData, // 省市区数据
      genjinshowEdit: false,
      city: [], // 选中的省市区
      provinceCity: [], // 选中的省市
      province: [], // 选中的省
      table: { url: '/member/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则
        realname: [
          { required: true, message: '请输入会员名称', trigger: 'blur' },
        ],
        sort: [{ required: true, message: '请输入排序', trigger: 'blur' }],
        trx: [{ required: true, message: '请输入trx数量', trigger: 'blur' }],
        private_key: [
          { required: true, message: '请输入充值私钥', trigger: 'blur' },
        ],
        address: [
          { required: true, message: '请输入归集地址', trigger: 'blur' },
        ],
      },
      memberLevelList: [], // 会员等级
    }
  },
  computed: {
    ...mapGetters(['permission']),
  },
  // components: { uploadImage },
  mounted() {
    // this.getMemberLevelList(); // 查询职级列表
    this.rank1()
  },
  methods: {
    tradeType(type, row) {
      this.viewType = type
      // this.$http.post('member/surplus',)
      let obj = {
        id: row.id,
        platform_id: type,
      }
      this.$http.post('member/surplus', obj).then((res) => {
        // console.log(res.data.data)
        this.allSum = res.data.data.balance
      })
      this.exchangeShow = true
    },
    // 归集
    guijidizhi() {
      console.log(this.subclass.id)
      // this.subclass
      let obj = {
        id: this.subclass.id,
        address: this.subclass.address,
      }
      this.$refs['subclass'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            lock: true,
          })

          this.$http
            .post('member/pooling', obj)
            .then((res) => {
              loading.close()
              if (res.data.code === 0) {
                this.guijiShow = false
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
    openTRX(row) {
      this.trxForm = row
      this.TRXshow = true
    },
    chongzhiTRX() {
      let obj = {
        id: this.trxForm.id,
        private_key: this.trxForm.private_key,
        trx: this.trxForm.trx,
      }
      console.log(obj)
      this.$refs['trxForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            lock: true,
          })
          this.$http
            .post('member/trx', obj)
            .then((res) => {
              loading.close()
              if (res.data.code === 0) {
                this.TRXshow = false
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
      //   this.$http.post('member/trx?id=' + row.id).then((res) => {
      //   if (res.data.code === 0) {
      //     console.log(res.data.data)
      //   }
      // })
      // this.TRXshow = true
    },
    load(tree, treeNode, resolve) {
      // console.log(tree)
      // console.log(treeNode)
      // console.log(resolve)
      this.$http.post('member/team?id=' + tree.id).then((res) => {
        console.log(res.data.data)
        resolve(res.data.data)
      })
      // setTimeout(() => {
      //   resolve([
      //     {
      //       id: 31,
      //       date: '2016-05-01',
      //       name: '王小虎',
      //       address: '上海市普陀区金沙江路 1519 弄',
      //     },
      //     {
      //       id: 32,
      //       date: '2016-05-01',
      //       name: '王小虎',
      //       address: '上海市普陀区金沙江路 1519 弄',
      //     },
      //   ])
      // }, 1000)
    },
    unfold(row, expanded) {
      console.log(expanded)
      // console.log(row)
      // alert(1)
      this.$http.post('member/team?id=' + row.id).then((res) => {
        // console.log(res.data.data)
        this.personnelList.forEach((item, index) => {
          if (item.id == row.id) {
            this.personnelList[index].child = res.data.data
          }
        })
        // console.log(this.personnelList)
      })
    },
    rq_change(e) {
      this.table.where.start_time = e[0]
      this.table.where.end_time = e[1]
    },
    // 归集
    collection(row) {
      this.subclass = row
      console.log(this.subclass)
      this.guijiShow = true
      // this.$http.get('member/pooling?id=' + row.id).then((res) => {
      //   // console.log(res)
      //   if (res.status === 200) {
      //     this.$message({
      //       type: 'success',
      //       message: '完成',
      //     })
      //   }
      // })
    },
    getbalance(row) {
      this.balance = {}
      //查看钱包余额
      // console.log(row)
      this.$http.post('member/balance?id=' + row.id).then((res) => {
        // console.log(res)
        if (res.data.code == 0) {
          this.balance = res.data.data
        }
      })
      this.balanceShow = true
    },
    checkpass(row) {
      this.balance = {}
      //查看钱包余额
      // console.log(row)
      this.$http.post('member/resetpass?id=' + row.id).then((res) => {
        // console.log(res)
        if (res.data.code == 0) {
          this.balance = res.data.data
        }
      })
      this.balanceShow = true
    },
    handleClick(tab, event) {
      console.log(tab, event)
    },
    // 查看
    seemore(row) {
      this.getPay_rank(row)
      this.getRecord(row)
      this.gettransferRecord(row)
      this.getstrategyList(row)
      this.getfinancialDetails(row)
      this.getTransaction_Log(row)
      this.getdeal_buy_log(row)
      this.getdeal_sale_log(row)
      this.getpersonnelList(row)
      this.gettrxlog(row)
      this.getusdtlog(row)
      this.showsee = true
    },
    gettrxlog(row) {
      this.trxlog = []
      this.$http.get('/trxlog/index?user_id=' + row.id).then((res) => {
        this.trxlog = res.data.data
      })
    },
    getusdtlog(row) {
      this.usdtlog = []
      this.$http.get('/usdtlog/index?user_id=' + row.id).then((res) => {
        this.usdtlog = res.data.data
      })
    },
    // 拿到充值记录
    getPay_rank(row) {
      this.pay_rank = []
      this.$http.get('/quanttransfer/index?user_id=' + row.id).then((res) => {
        this.pay_rank = res.data.data
      })
    }, //拿到提现记录
    getRecord(row) {
      this.Record = []
      this.$http.get('/quantcash/index?user_id=' + row.id).then((res) => {
        this.Record = res.data.data
      })
    },
    // 拿到转账记录
    gettransferRecord(row) {
      this.transferRecord = []
      this.$http.get('/quantmove/index?user_id_from=' + row.id).then((res) => {
        this.transferRecord = res.data.data
      })
    },
    // 拿到当前策略
    getstrategyList(row) {
      this.strategyList = []
      this.$http.get('/singleloopsetup/index?user_id=' + row.id).then((res) => {
        this.strategyList = res.data.data
      })
    }, //钱包流水
    getfinancialDetails(row) {
      this.financialDetails = []
      this.$http.get('/quantwallet/index?user_id=' + row.id).then((res) => {
        this.financialDetails = res.data.data
      })
    }, //交易记录
    getTransaction_Log(row) {
      this.Transaction_Log = []
      this.$http.get('/tradelog/index?user_id=' + row.id).then((res) => {
        this.Transaction_Log = res.data.data
      })
    }, //交易买入记录
    getdeal_buy_log(row) {
      this.deal_buy_log = []
      this.$http.get('/singleloopbuy/index?user_id=' + row.id).then((res) => {
        this.deal_buy_log = res.data.data
      })
    }, //交易卖出记录
    getdeal_sale_log(row) {
      this.deal_sale_log = []
      this.$http.get('/singleloopsell/index?user_id=' + row.id).then((res) => {
        this.deal_sale_log = res.data.data
      })
    },
    getpersonnelList(res) {
      this.personnelList = []
      this.$http.get('member/team?id=' + res.id).then((res) => {
        // console.log(res)
        this.personnelList = res.data.data
        // this.personnelList.forEach((item) => {
        //   item.hasChildren = true
        // })
      })
    },
    /**
     * 信息分类
     */
    rank1() {
      this.$http
        .get('/teamconfig/index')
        .then((res) => {
          if (res.data.code === 0) {
            this.rank_name = res.data.data
            this.rank_name.push({
              id: 0,
              name: '无段位',
              per: '0.01',
              mark: 1,
              usdt_max: 1000,
              usdt_min: 0,
            })
            this.rank_name.forEach((item) => {
              item.name = item.name + '    LV' + item.id
              // console.log(item)
            })
            // console.log(this.rank_name)
          } else {
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          this.$message.error(e.message)
        })
    },
    chongzhi(row) {
      this.genjinid = row.id
      console.log(row)
      this.genjinshowEdit = true
    },
    zhuangtaisave() {
      console.log(this.genjinid)
      let obj = {
        id: this.genjinid,
        amount: this.fenjieForm.amount,
        memo: this.fenjieForm.memo,
      }
      console.log(obj)
      this.$refs['fenjieForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            lock: true,
          })
          this.$http
            .post('/member/recharge', obj)
            .then((res) => {
              loading.close()
              if (res.data.code === 0) {
                this.genjinshowEdit = false
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
    /* 显示编辑 */
    edit(row) {
      this.editForm = Object.assign({}, row)
      this.showEdit = true
      // 取值赋予城市组件
      this.city = row.city
    },
    /* 保存编辑 */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({ lock: true })
          this.editForm = Object.assign({}, this.editForm, {
            city: this.city,
          })
          this.$http
            .post('/member/edit', this.editForm)
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
    /* 删除 */
    remove(row) {
      if (!row) {
        // 批量删除
        if (this.choose.length === 0)
          return this.$message.error('请至少选择一条数据')
        let ids = this.choose.map((d) => d.id)
        this.$confirm('确定要删除选中的会员吗?', '提示', { type: 'warning' })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/member/delete', { id: ids })
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
          .post('/member/delete', { id: row.id })
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
    /* 更改状态 */
    editStatus(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/member/status', params)
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
.red {
  color: red;
}
.green {
  color: green;
}
.yellow {
  color: #ffcc40;
}
.demo-table-expand {
  font-size: 0;
}
.demo-table-expand label {
  width: 90px;
  color: #99a9bf;
}
.demo-table-expand .el-form-item {
  margin-right: 0;
  margin-bottom: 0;
  width: 50%;
}
</style>
