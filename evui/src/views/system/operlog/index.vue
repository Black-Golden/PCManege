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
            <el-form-item label="用户账号:">
              <el-input
                v-model="table.where.username"
                placeholder="请输入用户账号"
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
              <el-button @click="reload">重置</el-button>
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 数据表格 -->
      <ele-data-table
        ref="table"
        :config="table"
        height="calc(100vh - 260px)"
        highlight-current-row
      >
        <template slot-scope="{ index }">
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
            label="日志标题"
            align="center"
            show-overflow-tooltip
            min-width="100"
          />
          <el-table-column
            prop="username"
            label="登录账号"
            align="center"
            show-overflow-tooltip
            min-width="100"
          />
          <el-table-column
            prop="method"
            label="请求方式"
            align="center"
            show-overflow-tooltip
            min-width="100"
          />
          <el-table-column
            prop="module"
            label="操作模块"
            align="center"
            show-overflow-tooltip
            min-width="100"
          />
          <el-table-column
            prop="action"
            label="操作方法"
            align="center"
            show-overflow-tooltip
            min-width="150"
          />
          <el-table-column
            prop="url"
            label="操作URL"
            align="center"
            show-overflow-tooltip
            min-width="200"
          />
          <el-table-column
            prop="param"
            label="请求参数"
            align="center"
            show-overflow-tooltip
            min-width="200"
          />
          <el-table-column
            prop="ip"
            label="操作IP"
            align="center"
            show-overflow-tooltip
            min-width="130"
          />
          <el-table-column
            prop="type"
            label="操作类型"
            align="center"
            min-width="110"
            show-overflow-tooltip
          >
            <template slot-scope="{ row }">
              <ele-dot
                :type="['', 'success', 'warning'][row.type - 1]"
                :ripple="row.type === 0"
                :text="['登录系统', '注销系统', '操作日志'][row.type - 1]"
              />
            </template>
          </el-table-column>
          <el-table-column
            prop="create_time"
            label="操作时间"
            align="center"
            min-width="160"
            show-overflow-tooltip
          >
            <template slot-scope="{ row }">{{
              (row.create_time * 1000) | toDateString
            }}</template>
          </el-table-column>
          <el-table-column
            label="操作"
            width="90px"
            align="center"
            :resizable="false"
            fixed="right"
          >
            <template slot-scope="{ row }">
              <el-link
                @click="view(row)"
                icon="el-icon-view"
                type="primary"
                :underline="false"
                >详情</el-link
              >
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 详情弹窗 -->
    <el-dialog
      title="详情"
      :visible.sync="showView"
      width="600px"
      :destroy-on-close="true"
      :lock-scroll="false"
    >
      <el-form label-width="82px" size="mini" class="ele-form-detail">
        <el-row :gutter="15">
          <el-col :sm="12">
            <el-form-item label="操作账号:" prop="username">
              <div class="ele-text-secondary">{{ viewForm.username }}</div>
            </el-form-item>
            <el-form-item label="请求方式:" prop="method">
              <div class="ele-text-secondary">{{ viewForm.method }}</div>
            </el-form-item>
            <el-form-item label="操作方法:" prop="action">
              <div class="ele-text-secondary">{{ viewForm.action }}</div>
            </el-form-item>
            <el-form-item label="操作类型:" prop="type">
              <el-tag :type="['', 'success'][viewForm.type]" size="mini">
                {{ ['登录系统', '注销系统'][viewForm.type] }}
              </el-tag>
            </el-form-item>
          </el-col>
          <el-col :sm="12">
            <el-form-item label="日志标题:" prop="title">
              <div class="ele-text-secondary">{{ viewForm.title }}</div>
            </el-form-item>
            <el-form-item label="操作模块:" prop="module">
              <div class="ele-text-secondary">{{ viewForm.module }}</div>
            </el-form-item>
            <el-form-item label="操作IP:" prop="ip">
              <div class="ele-text-secondary">{{ viewForm.ip }}</div>
            </el-form-item>
            <el-form-item label="操作时间:" prop="create_time">
              <div class="ele-text-secondary">
                {{ viewForm.create_time | toDateString }}
              </div>
            </el-form-item>
          </el-col>
        </el-row>
        <div style="margin: 12px 0;">
          <el-divider />
        </div>
        <el-form-item label="操作URL:" prop="url">
          <div class="ele-text-secondary">{{ viewForm.url }}</div>
        </el-form-item>
        <el-form-item label="请求参数:" prop="operParam">
          <div class="ele-text-secondary">{{ viewForm.param }}</div>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="showView = false">关闭</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'SysOperRecord',
  data() {
    return {
      table: { url: '/actionlog/index', where: {} }, // 表格配置
      showView: false, // 是否显示查看弹窗
      viewForm: {}, // 表单数据
      daterange: '', // 筛选日期范围
      pickerOptions: {
        // 日期时间选择器快捷项
        shortcuts: [
          {
            text: '最近一周',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
              picker.$emit('pick', [start, end])
            },
          },
          {
            text: '最近一个月',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
              picker.$emit('pick', [start, end])
            },
          },
          {
            text: '最近三个月',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
              picker.$emit('pick', [start, end])
            },
          },
        ],
      },
    }
  },
  methods: {
    /* 详情 */
    view(row) {
      this.viewForm = Object.assign({}, row)
      this.showView = true
    },
    /* 日期选择改变回调 */
    onDateRangeChoose() {
      if (this.daterange) {
        this.table.where.createTimeStart = this.daterange[0]
        this.table.where.createTimeEnd = this.daterange[1]
      } else {
        this.table.where.createTimeStart = null
        this.table.where.createTimeEnd = null
      }
    },
    /* 刷新 */
    reload() {
      this.daterange = null
      this.table.where = {}
      this.$refs.table.reload()
    },
  },
}
</script>

<style scoped></style>
