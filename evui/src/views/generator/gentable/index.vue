<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
          <el-col :md="6" :sm="12">
            <el-form-item label="表名称:">
              <el-input v-model="table.where.name" placeholder="请输入表名称" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
              <el-form-item label="表描述:">
                <el-input v-model="table.where.comment" placeholder="请输入表描述" clearable/>
              </el-form-item>
            </el-col>
          <el-col :md="9" :sm="12">
            <div class="ele-form-actions">
              <el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search" class="ele-btn-icon">查询
              </el-button>
              <el-button @click="(table.where={})&&$refs.table.reload()">重置</el-button>
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 260px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
          <el-table-column prop="name" label="业务表名称" show-overflow-tooltip align="center" min-width="150"/>
          <el-table-column prop="comment" label="表描述" show-overflow-tooltip align="center" min-width="120"  />
          <el-table-column prop="engine" label="表引擎" show-overflow-tooltip align="center" min-width="150" />
          <el-table-column prop="version" label="版本号" show-overflow-tooltip align="center" min-width="120"  />
          <el-table-column prop="collation" label="编码" show-overflow-tooltip align="center" min-width="150"  />
          <el-table-column prop="rows" label="记录总数" show-overflow-tooltip align="center" min-width="200"  />
          <el-table-column prop="data_length" label="数据大小" sortable="custom" show-overflow-tooltip align="center" min-width="120"  />
          <el-table-column prop="auto_increment" label="自增索引" show-overflow-tooltip align="center" min-width="120"  />
          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">
            <template slot-scope="{row}">{{ row.create_time | toDateString }}</template>
          </el-table-column>
          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">
            <template slot-scope="{row}">{{ row.update_time | toDateString }}</template>
          </el-table-column>
          <el-table-column label="操作" width="150px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
              <el-popconfirm title="确定要一键生成当前表的模块吗？" @confirm="generate(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-copy-document" type="success" :underline="false">一键生成模块</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>

  </div>
</template>

<script>
export default {
  name: "Generate",
  data() {
    return {
      table: {url: '/generate/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      tableId:'',
    }
  },
  mounted() {
  },
  computed: {
  },
  methods: {
    
    /**
     * 一键生成模块
     */
    generate(row) {
      /**
       * 单模块生成
       */
      const loading = this.$loading({lock: true});
      this.$http.post('/generate/generate', {name:row.name,comment:row.comment}).then(res => {
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
.ele-block >>> .el-upload, .ele-block >>> .el-upload-dragger {
  width: 100%;
}
</style>