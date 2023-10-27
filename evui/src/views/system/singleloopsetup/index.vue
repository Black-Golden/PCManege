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
            <el-form-item label="是否运行:">
              <el-select
                clearable
                v-model="table.where.is_run"
                placeholder="请选择是否运行"
              >
                <el-option
                  v-for="item in whetheroptions"
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
            <el-form-item label="交易策略:">
              <el-select
                clearable
                v-model="table.where.type_id"
                placeholder="请选择交易策略"
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
          <el-col :md="12" :sm="19">
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
        <el-row>
          <el-col :md="8" :sm="12">
            <el-form-item label="是否补仓:">
              <el-select
                clearable
                v-model="table.where.is_open_down"
                placeholder="请选择是否补仓"
              >
                <el-option
                  v-for="item in whetheroptions"
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
          v-if="permission.includes('sys:singleloopsetup:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:singleloopsetup:dall')"
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
              <div :style="{ color: row.is_run == 0 ? 'red' : 'green' }">
                {{ row.is_run == 0 ? '否' : '是' }}
              </div>
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
              <div :style="{ color: row.is_loop == 0 ? 'red' : 'green' }">
                {{ row.is_loop == 0 ? '否' : '是' }}
              </div>
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
              <div :style="{ color: row.is_open_down == 0 ? 'red' : 'green' }">
                {{ row.is_open_down == 0 ? '否' : '是' }}
              </div>
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
              <div :style="{ color: row.is_singke_cut == 0 ? 'red' : 'green' }">
                {{ row.is_singke_cut == 0 ? '否' : '是' }}
              </div>
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
              <div :style="{ color: row.is_double_cut == 0 ? 'red' : 'green' }">
                {{ row.is_double_cut == 0 ? '否' : '是' }}
              </div>
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
              <div :style="{ color: row.is_other_cut == 0 ? 'red' : 'green' }">
                {{ row.is_other_cut == 0 ? '否' : '是' }}
              </div>
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
                v-if="permission.includes('sys:singleloopsetup:edit')"
                >查看</el-link
              >
              <el-popconfirm
                title="确定要删除此交易设置吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:singleloopsetup:delete')"
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
      :title="editForm.id ? '查看交易设置' : '修改交易设置'"
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
              label="用户昵称"
              prop="user_name"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.user_name"
                placeholder="请输入用户昵称"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="代币类型："
              prop="symbol"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.symbol"
                placeholder="请输入代币类型"
                clearable
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="合约倍数："
              prop="lever"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.lever"
                placeholder="请输入合约倍数"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="购买数量："
              prop="qb_token"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.qb_token"
                placeholder="请输入购买数量"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="每笔仓位："
              prop="min_token"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.min_token"
                placeholder="请输入每一笔的仓位"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
          <el-col :sm="11">
            <el-form-item
              label="层级："
              prop="rounds"
              style="margin-bottom:20px;"
            >
              <el-input
                disabled
                v-model="editForm.rounds"
                placeholder="请输入层级"
                class="ele-fluid ele-text-left"
              /> </el-form-item
          ></el-col>
        </el-row>
        <el-row>
          <el-col :sm="11"
            ><el-form-item
              label="是否运行："
              prop="is_run"
              style="margin-bottom:20px;"
            >
              <el-select
                disabled
                v-model="editForm.is_run"
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
              label="是否循环："
              prop="is_loop"
              style="margin-bottom:20px;"
            >
              <el-select
                disabled
                v-model="editForm.is_loop"
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
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="所属平台："
              prop="platform_id"
              style="margin-bottom:20px;"
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
              label="开仓方向"
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
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="是否补仓："
              prop="is_open_down"
              style="margin-bottom:20px;"
            >
              <el-select
                disabled
                v-model="editForm.is_open_down"
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
              label="是否单向斩仓："
              prop="is_singke_cut"
              style="margin-bottom:20px;"
            >
              <el-select
                disabled
                v-model="editForm.is_singke_cut"
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
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="是否双向斩仓："
              prop="is_double_cut"
              style="margin-bottom:20px;"
            >
              <el-select
                disabled
                v-model="editForm.is_double_cut"
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
              label="交易策略:"
              prop="type_id"
              style="margin-bottom:20px;"
            >
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
  name: 'SysSingleLoopSetup',
  data() {
    return {
      create_time: '',
      table: { url: '/singleloopsetup/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        user_id: [{ required: true, message: '请输入会员', trigger: 'blur' }],

        symbol: [
          { required: true, message: '请输入代币类型', trigger: 'blur' },
        ],

        lever: [
          {
            required: true,
            message: '请输入合约倍数（针对合约使用）',
            trigger: 'blur',
          },
        ],

        qb_token: [
          {
            required: true,
            message: '请输入购买数量首次必须下',
            trigger: 'blur',
          },
        ],

        is_run: [
          { required: true, message: '请选择是否运行', trigger: 'blur' },
        ],

        is_loop: [
          { required: true, message: '请选择是否循环', trigger: 'blur' },
        ],

        platform_id: [
          { required: true, message: '请输入所属平台', trigger: 'blur' },
        ],

        open_type: [
          { required: true, message: '请输入2是多3是空', trigger: 'blur' },
        ],

        is_open_down: [
          { required: true, message: '请选择是否开启补仓', trigger: 'blur' },
        ],

        is_singke_cut: [
          { required: true, message: '请输入是否单向斩仓', trigger: 'blur' },
        ],

        is_double_cut: [
          { required: true, message: '请输入是否双向斩仓', trigger: 'blur' },
        ],

        is_other_cut: [
          { required: true, message: '请输入是否跨币斩仓', trigger: 'blur' },
        ],

        type_id: [
          {
            required: true,
            message: '请输入1高频2激进3保守4超级保守5自定义',
            trigger: 'blur',
          },
        ],

        min_token: [
          { required: true, message: '请输入每一笔的仓位', trigger: 'blur' },
        ],

        rounds: [{ required: true, message: '请输入层级', trigger: 'blur' }],
      },
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
    rq_change(e) {
      this.table.where.start_time = e[0]
      this.table.where.end_time = e[1]
    },
    /**
     * 显示编辑
     */
    edit(row) {
      this.$http
        .get('/singleloopsetup/info?id=' + row.id)
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
            .post('/singleloopsetup/edit', this.editForm)
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
        this.$confirm('确定要删除选中的交易设置吗?', '提示', {
          type: 'warning',
        })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/singleloopsetup/delete', { id: ids })
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
          .post('/singleloopsetup/delete', { id: row.id })
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
     * 更改是否运行
     */
    setIsRun(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/singleloopsetup/setIsRun', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({ type: 'succes1s', message: res.data.msg })
          } else {
            row.is_run = !row.is_run ? 2 : 1
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          loading.close()
          this.$message.error(e.message)
        })
    },

    /**
     * 更改是否循环
     */
    setIsLoop(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/singleloopsetup/setIsLoop', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({ type: 'success', message: res.data.msg })
          } else {
            row.is_loop = !row.is_loop ? 2 : 1
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          loading.close()
          this.$message.error(e.message)
        })
    },

    /**
     * 更改是否开启补仓
     */
    setIsOpenDown(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/singleloopsetup/setIsOpenDown', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({ type: 'success', message: res.data.msg })
          } else {
            row.is_open_down = !row.is_open_down ? 2 : 1
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
</style>
