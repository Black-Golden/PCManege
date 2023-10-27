<template>
  <div class="ele-body">
    <el-card shadow="never" v-loading="loading">
      <!-- 头部工具栏 -->
      <div class="ele-table-tool">
        <div class="ele-table-tool-title">文章管理</div>
        <div class="ele-table-tool-right">
          <div class="ele-inline-block">
            <el-radio-group v-model="search.status" @change="query" size="small">
              <el-radio-button :label="0">全部</el-radio-button>
              <el-radio-button :label="1">进行中</el-radio-button>
              <el-radio-button :label="2">已完成</el-radio-button>
            </el-radio-group>
          </div>
          <div class="ele-inline-block adv-list-search-group hidden-xs-only">
            <el-input placeholder="请输入文章标题" v-model="search.title" size="small" clearable @keyup.enter.native="query">
              <el-button @click="query" slot="append" icon="el-icon-search"/>
            </el-input>
          </div>
        </div>
      </div>
      <!-- 操作按钮 -->
      <div class="ele-table-tool ele-table-tool-default">
        <el-button @click="showEdit=true;articleId=0" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small">添加
        </el-button>
      </div>
      <!-- 数据列表 -->
      <div v-for="(item,index) in data" :key="index">
        <el-divider v-if="index!==0"/>
        <div class="basic-list-item">
          <div class="ele-cell">
            <el-avatar shape="square" :size="60" :src="item.cover"/>
            <div class="ele-cell-content">
              <div class="ele-cell-title">{{ item.title }}</div>
              <div class="ele-cell-desc">{{ item.guide }}</div>
            </div>
          </div>
          <div class="basic-list-item-owner">
            <div>发布人</div>
            <div class="ele-text-secondary">{{ item.create_user_name }}</div>
          </div>
          <div class="basic-list-item-time">
            <div>开始时间</div>
            <div class="ele-text-secondary">{{ item.create_time*1000 | toDateString}}</div>
          </div>
          <div class="basic-list-item-progress">
            <el-progress :percentage="item.progress" :status="item.result"/>
          </div>
          <div class="basic-list-item-tool">
            <el-link @click="edit(item)" icon="el-icon-edit" type="primary" :underline="false">编辑</el-link>
            <el-dropdown @command="command=>dropClick(command,item)">
              <el-link type="primary" :underline="false">更多<i class="el-icon-arrow-down"/></el-link>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="detail">分享</el-dropdown-item>
                <el-dropdown-item command="delete">删除</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </div>
        </div>
      </div>
      <el-pagination :current-page="page.page" :page-size="page.limit" :total="count" :background="true"
                     @size-change="d=>(page.limit=d)&&query()" @current-change="d=>(page.page=d)&&query()"
                     layout="total, prev, pager, next, jumper" :pager-count="5" class="ele-pagination-circle"/>
    </el-card>

    <!-- 编辑数据表 -->
    <editForm 
        v-if="showEdit"
        @returnBack="showEdit=false"
        :visible="showEdit"
        :articleId="articleId"
        :itemList="itemList"
        @reload="reload()"
    ></editForm>

  </div>
</template>

<script>
import editForm from './editForm';
export default {
  name: "CMSArticle",
  components: {editForm},
  data() {
    return {
      loading: false,               // 列表加载状态
      data: [],
      search: {status: 0},           // 搜索表单
      page: {page: 1, limit: 5},    // 分页参数
      count: 0,                    // 数据总数
      showEdit: false,              // 是否显示编辑弹窗
      // 站点列表
      itemList:[],
      articleId:'', // 文章ID
    }
  },
  mounted() {
    // 获取文档列表
    this.query();
    // 查询站点列表
    this.getItemList();
  },
  methods: {
    /* 查询数据 */
    query() {
      this.loading = true;
      this.$http.get('/article/index?page='+this.page.page+'&limit='+ this.page.limit+'&status='+this.search.status).then(res => {
        if (res.data.code === 0) {
          this.loading = false;
          this.data = res.data.data.map(d => {
            d.progress = Math.floor(Math.random() * (100 - 0 + 1)) + 0;
            if (d.progress == 100) {
              d.result = 'success';
            } else if (d.progress <= 30) {
              d.result = 'exception';
            }
            return d;
          });
          this.count = res.data.count;
        } else {
          this.loading = false;
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        this.loading = false;
        this.$message.error(e.message);
      });
    },
    /* 显示编辑弹窗 */
    edit(item) {
      this.articleId = item.id;
      this.showEdit = true;
    },
    /* 下拉菜单点击事件 */
    dropClick(command, item) {
      if (command === 'delete') {  // 删除
        this.$confirm('确定删除该文章吗？', '删除文章', {type: 'warning'}).then(() => {
          // this.data.splice(this.data.findIndex(d => d.id === item.id), 1);
          // this.$message({type: 'success', message: '删除成功'});
          const loading = this.$loading({lock: true});
          this.$http.post('/article/delete', {id:item.id}).then(res => {
            loading.close();
            if (res.data.code === 0) {
              this.$message({type: 'success', message: res.data.msg});
              // 刷新列表
              this.query();
            } else {
              this.$message.error(res.data.msg);
            }
          }).catch(e => {
            loading.close();
            this.$message.error(e.message);
          });
        }).catch(() => 0);
      } else if (command === 'detail') {
        this.$message('点击了详情');
      }
    },
    /**
     * 获取站点列表
     */
    getItemList() {
      this.$http.get('/item/getItemList').then(res => {
        if (res.data.code === 0) {
          this.itemList = res.data.data;
        } else {
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        this.$message.error(e.message);
      });
    },
    // 回调并刷新
    reload(){
      this.showEdit = false;
      this.query();
    },
  }
}
</script>

<style scoped>
/** 顶部文字样式 */
.top-text-item {
  padding: 8px 0;
  text-align: center;
}

.top-text-item .top-text-content {
  font-size: 24px;
  margin-top: 8px;
}

/** 列表样式 */
.basic-list-item {
  display: flex;
  align-items: center;
  padding: 15px 10px;
}

.basic-list-item .ele-cell {
  flex: 1;
}

.basic-list-item .basic-list-item-owner {
  width: 60px;
  padding: 0 15px;
}

.basic-list-item .basic-list-item-time {
  width: 150px;
  padding: 0 15px;
}

.basic-list-item .ele-text-secondary {
  margin-top: 8px;
}

.basic-list-item .basic-list-item-progress {
  width: 180px;
}

.basic-list-item .basic-list-item-tool {
  padding: 0 15px;
}

.ele-body .el-pagination {
  margin: 20px 0 5px 0;
}

/** 表头工具栏 */
.ele-table-tool-right .ele-inline-block {
  vertical-align: middle;
}

.ele-table-tool-right .ele-inline-block + .ele-inline-block {
  margin-left: 12px;
}

.ele-inline-block >>> .el-radio-button__inner {
  padding-left: 12px;
  padding-right: 12px;
}

.adv-list-search-group {
  width: 180px;
}

.adv-list-search-group >>> .el-button {
  padding-left: 13px;
  padding-right: 12px;
}

/* 响应式 */
@media screen and (max-width: 992px) {

  .basic-list-item .basic-list-item-owner,
  .basic-list-item .basic-list-item-time,
  .basic-list-item .basic-list-item-progress,
  .basic-list-item .basic-list-item-tool {
    width: auto;
    padding: 0 10px;
  }

  .basic-list-item .basic-list-item-progress {
    width: 100px;
  }
}

@media screen and (max-width: 768px) {
  .basic-list-item {
    display: block;
  }

  .basic-list-item .basic-list-item-owner,
  .basic-list-item .basic-list-item-time,
  .basic-list-item .basic-list-item-progress,
  .basic-list-item .basic-list-item-tool {
    width: auto;
    padding: 8px 0 0 0;
  }

  .basic-list-item .ele-text-secondary {
    margin-top: 0;
    padding-left: 15px;
  }

  .basic-list-item .basic-list-item-owner > div,
  .basic-list-item .basic-list-item-time > div {
    display: inline-block;
  }

  .basic-list-item .basic-list-item-tool {
    text-align: right;
  }
}
</style>
