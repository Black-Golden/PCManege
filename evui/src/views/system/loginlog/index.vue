<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
          <el-col :md="6" :sm="12">
            <el-form-item label="用户账号:">
              <el-input v-model="table.where.username" placeholder="请输入用户账号" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <div class="ele-form-actions">
              <el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search" class="ele-btn-icon">查询
              </el-button>
              <el-button @click="exportData" icon="el-icon-download" type="success" class="ele-btn-icon" v-if="permission.includes('sys:loginlog:export')">导出日志</el-button>
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" height="calc(100vh - 260px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
          <el-table-column prop="title" label="日志标题" align="center" show-overflow-tooltip min-width="100"/>
          <el-table-column prop="username" label="登录账号" align="center" show-overflow-tooltip min-width="100"/>
          <el-table-column prop="method" label="请求方式" align="center" show-overflow-tooltip min-width="100"/>
          <el-table-column prop="module" label="操作模块" align="center" show-overflow-tooltip min-width="100"/>
          <el-table-column prop="action" label="操作方法" align="center" show-overflow-tooltip min-width="150"/>
          <el-table-column prop="url" label="操作URL" align="center" show-overflow-tooltip min-width="200"/>
          <el-table-column prop="param" label="请求参数" align="center" show-overflow-tooltip min-width="200"/>
          <el-table-column prop="ip" label="操作IP" align="center" show-overflow-tooltip min-width="130"/>
          <el-table-column prop="type" label="操作类型" align="center" min-width="110" show-overflow-tooltip>
            <template slot-scope="{row}">
              <ele-dot :type="['', 'success', 'warning'][row.type-1]" :ripple="row.type===0"
                        :text="['登录系统','注销系统', '操作日志'][row.type-1]"/>
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="操作时间" align="center" min-width="160" show-overflow-tooltip>
            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="操作" width="100px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
              <el-popconfirm title="确定要删除此登录日志吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:loginlog:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
  </div>
</template>

<script>
import XLSX from 'xlsx'
import { mapGetters } from "vuex";
export default {
  name: "SysLoginRecord",
  data() {
    return {
      table: {url: '/loginlog/index', where: {}},  // 表格配置
      daterange: '',  // 筛选日期范围
      pickerOptions: {  // 日期时间选择器快捷项
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          }
        }]
      }
    }
  },
  computed: {
    ...mapGetters(["permission"]),
  },
  methods: {
    /* 日期选择改变回调 */
    onDateRangeChoose() {
      if (this.daterange) {
        this.table.where.createTimeStart = this.daterange[0];
        this.table.where.createTimeEnd = this.daterange[1];
      } else {
        this.table.where.createTimeStart = null;
        this.table.where.createTimeEnd = null;
      }
    },
    /* 导出数据 */
    exportData() {
      let array = [['日志标题','登录账号', '请求方式', '操作模块', '操作方法', '操作URL', '请求参数', '操作IP', '操作类型', '登录时间']];
      // 请求查询全部(不分页)的接口
      const loading = this.$loading({lock: true});
      this.$http.get('/loginlog/index?page=1&limit=2000').then(res => {
        loading.close();
        if (res.data.code === 0) {
          res.data.data.forEach(d => {
            array.push([
              d.title, d.username, d.method,
              d.module, d.action, d.url,d.param,d.ip,
              ['登录系统', '注销系统'][d.type],
              this.$util.toDateString(d.create_time)
            ]);
          });
          let sheet = XLSX.utils.aoa_to_sheet(array);
          this.$util.exportSheet(XLSX, sheet, '登录日志');
        } else {
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        loading.close();
        this.$message.error(e.message);
      });
    },
    /* 删除 */
    remove(row) {
      const loading = this.$loading({lock: true});
      this.$http.post('/loginlog/delete', {id:row.id}).then(res => {
        loading.close();
        if (res.data.code === 0) {
          this.$message({type: 'success', message: res.data.msg});
          this.$refs.table.reload();
        } else {
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        loading.close();
        this.$message.error(e.message);
      });
    },
  }
}
</script>

<style scoped>

</style>