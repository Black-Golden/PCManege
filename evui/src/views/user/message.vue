<template>
  <div class="ele-body">
    <el-card shadow="never" body-style="padding:0;">
      <div class="ele-cell ele-cell-align-top ele-user-message">
        <el-menu :default-active="active" :mode="mode" @select="query">
          <el-menu-item index="0">
            <span>全部消息</span>
            <el-badge v-if="allUnReadNum" :value="allUnReadNum" class="ele-badge-static"/>
          </el-menu-item>
          <el-menu-item index="1">
            <span>系统通知</span>
            <el-badge v-if="unReadNum1" :value="unReadNum1" class="ele-badge-static"/>
          </el-menu-item>
          <el-menu-item index="2">
            <span>用户私信</span>
            <el-badge v-if="unReadNum2" :value="unReadNum2" class="ele-badge-static"/>
          </el-menu-item>
          <el-menu-item index="3">
            <span>代办事项</span>
            <el-badge v-if="unReadNum3" :value="unReadNum3" class="ele-badge-static"/>
          </el-menu-item>
        </el-menu>
        <div class="ele-cell-content">
          <!-- 操作按钮 -->
          <div class="ele-table-tool">
            <el-button size="small" @click="read" type="primary" plain>标记已读</el-button>
            <el-button size="small" @click="readAll" type="primary" plain>全部已读</el-button>
            <el-button size="small" @click="remove()" type="danger" plain>删除消息</el-button>
          </div>
          <!-- 数据表格 -->
          <ele-data-table ref="table" :data="data" :choose.sync="choose" height="calc(100vh - 265px)" :border="false">
            <template slot-scope="{index}">
              <el-table-column type="selection" width="45" align="center" fixed="left"/>
              <el-table-column type="index" :index="index" width="45" align="center" fixed="left"
                               show-overflow-tooltip/>
              <el-table-column prop="title" label="标题内容" show-overflow-tooltip min-width="110"/>
              <el-table-column prop="time" label="时间" show-overflow-tooltip width="140px" align="center"/>
              <el-table-column label="状态" width="80px" align="center">
                <template slot-scope="{row}">
                  <span :class="['ele-text-danger','ele-text-info'][row.state]">{{ ['未读', '已读'][row.state] }}</span>
                </template>
              </el-table-column>
              <el-table-column label="操作" width="140px" align="center" :resizable="false" fixed="right">
                <template slot-scope="{row}">
                  <el-link @click="view(row)" icon="el-icon-view" type="primary" :underline="false">查看</el-link>
                  <el-popconfirm title="确定要删除此消息吗？" @onConfirm="remove(row)" class="ele-action">
                    <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false">删除</el-link>
                  </el-popconfirm>
                </template>
              </el-table-column>
            </template>
          </ele-data-table>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
export default {
  name: "UserMessage",
  data() {
    return {
      active: '0',  // 导航选中
      data: [],  // 列表显示数据
      choose: [],  // 列表选中数据
      allData: [  // 全部数据
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 0},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 0},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 0},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 0},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 1},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 1},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 1},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 1},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 1},
        {title: '你有两条任务待完成，不要忘了哦~', type: 3, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 0},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 0},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 0},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI给你发来了一条私信', type: 2, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 0},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 0},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 1},
        {title: 'ThinkPhp6+Vue+ElementUI新版本发布，欢迎体验', type: 1, time: '2020-07-24 11:35', state: 1}
      ]
    }
  },
  computed: {
    /* 小屏幕水平导航 */
    mode() {
      return this.$store.state.theme.screenWidth < 768 ? 'horizontal' : 'vertical';
    },
    /* 全部未读数量 */
    allUnReadNum() {
      return this.allData.filter(d => d.state === 0).length;
    },
    /* 通知未读数量 */
    unReadNum1() {
      return this.allData.filter(d => d.state === 0 && d.type === 1).length;
    },
    /* 私信未读数量 */
    unReadNum2() {
      return this.allData.filter(d => d.state === 0 && d.type === 2).length;
    },
    /* 代办未读数量 */
    unReadNum3() {
      return this.allData.filter(d => d.state === 0 && d.type === 3).length;
    }
  },
  mounted() {
    this.query();
  },
  methods: {
    /* 查询数据 */
    query(index) {
      this.active = index || '0';
      if (this.active === '0') this.data = this.allData;
      else this.data = this.allData.filter(d => String(d.type) === this.active);
    },
    /* 查看 */
    view(row) {
      console.log(row);
      this.$message('点击了查看');
    },
    /* 删除 */
    remove(row) {
      if (row) {  // 单个删除
        this.data.splice(this.data.indexOf(row), 1);
        this.$message({type: 'success', message: '删除成功'});
      } else {  // 批量删除
        if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
        this.$confirm('确定要删除选中的消息吗?', '提示', {type: 'warning'}).then(() => {
          this.choose.forEach(d => {
            this.data.splice(this.data.indexOf(d), 1);
          });
          this.$message({type: 'success', message: '删除成功'});
        }).catch(() => 0);
      }
    },
    /* 标记已读 */
    read() {
      if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
      this.choose.forEach(d => {
        this.data[this.data.indexOf(d)].state = 1;
      });
    },
    /* 全部标记已读 */
    readAll() {
      this.data.forEach(d => {
        d.state = 1;
      });
    }
  }
}
</script>

<style scoped>
.ele-user-message >>> .el-menu {
  flex-shrink: 0;
}

.ele-user-message .ele-cell-content {
  padding: 20px 15px;
  box-sizing: border-box;
  overflow: auto;
}

.ele-user-message .ele-badge-static {
  margin-left: 10px;
}

@media screen and (min-width: 768px) {
  .ele-user-message >>> .el-menu-item {
    min-width: 150px;
    text-align: right;
    border-right: 2px solid transparent;
  }

  .ele-user-message >>> .el-menu-item:first-child {
    margin-top: 15px;
  }
}

@media screen and (max-width: 768px) {
  .ele-user-message {
    display: block;
  }

  .ele-user-message >>> .el-menu-item {
    height: 45px;
    line-height: 45px;
    padding: 0 5px;
    display: inline-block;
    float: none;
  }

  .ele-user-message >>> .el-menu {
    text-align: center;
    white-space: nowrap;
    overflow: auto;
  }

  .ele-user-message .ele-badge-static {
    margin-left: 3px;
  }
}
</style>