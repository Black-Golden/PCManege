<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="90px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
          <el-col :md="6" :sm="12">
            <el-form-item label="所属站点">
              <el-select filterable clearable v-model="table.where.item_id" size="small" placeholder="-请选择所属站点-" class="ele-block">
                <el-option v-for="item in itemList" :key="item.id" :label="item.name" :value="item.id"/>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <div class="ele-form-actions">
              <el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search" class="ele-btn-icon">查询
              </el-button>
              <el-button @click="(table.where={})&&$refs.table.reload()">重置</el-button>
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 操作按钮 -->
      <div class="ele-table-tool ele-table-tool-default">
        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:layout:add')">添加
        </el-button>
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:layout:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
          <el-table-column prop="loc_desc" label="布局描述" show-overflow-tooltip align="center" min-width="200"/>
          <el-table-column prop="loc_id" label="布局位置" show-overflow-tooltip align="center" min-width="120"/>
          <el-table-column prop="item_name" label="所属站点" show-overflow-tooltip align="center" min-width="120"/>
          <el-table-column label="布局图片" min-width="80" align="center">
            <template slot-scope="{row}">
              <el-avatar shape="square" :size="25" :src="row.image"/>
            </template>
          </el-table-column>
          <el-table-column label="布局类型" min-width="80" align="center">
            <template slot-scope="{row}">
              <el-tag :type="['','success'][row.type-1]" size="mini">{{ ['系统文章', '通知公告'][row.type-1] }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="type_name" label="类型描述" show-overflow-tooltip align="center" min-width="200"/>
          <el-table-column prop="sort" label="排序" sortable="custom" show-overflow-tooltip align="center"/>
          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip align="center" min-width="160">
            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip align="center" min-width="160">
            <template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:layout:edit')">修改</el-link>
              <el-popconfirm title="确定要删除此布局描述吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:layout:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>

    <!-- 编辑数据表 -->
    <editForm 
        v-if="showEdit"
        @returnBack="showEdit=false"
        :visible="showEdit"
        :layoutId="layoutId"
        :itemList="itemList"
        @reload="showEdit=false && $refs.table.reload()"
    ></editForm>

  </div>
</template>

<script>
import editForm from './editForm';
import { mapGetters } from "vuex";
export default {
  name: "SysLayout",
  components: {editForm},
  data() {
    return {
      table: {url: '/layout/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      // 站点列表
      itemList:[],
      layoutId:'',
    }
  },
  computed: {
    ...mapGetters(["permission"]),
  },
  mounted() {
    // 查询站点列表
    this.getItemList();
  },
  methods: {
    /* 显示编辑 */
    edit(row) {
      this.layoutId = row.id;
      this.showEdit = true
    },
    /* 删除 */
    remove(row) {
      if (!row) {  // 批量删除
        if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
        let ids = this.choose.map(d => d.id);
        this.$confirm('确定要删除选中的布局描述吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/layout/delete', {id: ids}).then(res => {
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
        }).catch(() => 0);
      } else {  // 单个删除
        const loading = this.$loading({lock: true});
        this.$http.post('/layout/delete', {id:row.id}).then(res => {
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
  }
}
</script>

<style scoped>
.ele-block >>> .el-upload, .ele-block >>> .el-upload-dragger {
  width: 100%;
}
</style>