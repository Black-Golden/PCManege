<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="where" label-width="77px" class="ele-form-search" @keyup.enter.native="query"
               @submit.native.prevent>
        <el-row :gutter="15">
          <el-col :md="6" :sm="12">
            <el-form-item label="栏目名称:">
              <el-input v-model="where.name" placeholder="请输入栏目名称" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="9" :sm="12">
            <div class="ele-form-actions">
              <el-button type="primary" @click="query" icon="el-icon-search" class="ele-btn-icon">查询</el-button>
              <el-button @click="add()" type="primary" icon="el-icon-plus" class="ele-btn-icon" v-if="permission.includes('sys:itemcate:add')">添加</el-button>
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 数据表格 -->
      <el-table ref="table" :data="data" v-loading="loading" row-key="id" default-expand-all border
                height="calc(100vh - 215px)" highlight-current-row  lazy
    :load="load"
    :tree-props="{children: 'children', hasChildren: 'hasChildren'}">
        <el-table-column label="编号" type="index" width="60" align="center" fixed="left"/>
        <el-table-column label="栏目名称" show-overflow-tooltip min-width="200">
          <template slot-scope="{row}">{{ row.name }}</template>
        </el-table-column>
        <el-table-column prop="pinyin" label="拼音(全拼)" min-width="150" align="center"/>
        <el-table-column prop="code" label="拼音(简拼)" min-width="150" align="center"/>
        <el-table-column label="栏目封面" min-width="100" align="center">
          <template slot-scope="{row}">
            <el-avatar shape="square" :size="35" :src="row.cover"/>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" sortable min-width="100">
          <template slot-scope="{row}">
            <ele-dot :type="['success', 'danger'][row.status-1]" :ripple="row.status===0"
                      :text="['正常', '禁用'][row.status-1]"/>
          </template>
        </el-table-column>
        <el-table-column prop="sort" label="排序" width="60px" align="center"/>
        <el-table-column prop="note" label="备注" min-width="200" align="center"/>
        <el-table-column label="创建时间" show-overflow-tooltip min-width="160" align="center">
          <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
        </el-table-column>
        <el-table-column label="操作" width="190px" align="center" :resizable="false"  fixed="right">
          <template slot-scope="{row}">
            <el-link @click="add(row)" icon="el-icon-plus" type="primary" :underline="false" v-if="permission.includes('sys:itemcate:addz')">添加</el-link>
            <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:itemcate:edit')">修改</el-link>
            <el-popconfirm title="确定要删除此栏目吗？" @confirm="remove(row)" class="ele-action">
              <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:itemcate:delete')">删除</el-link>
            </el-popconfirm>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="form.id?'修改栏目':'添加栏目'" :visible.sync="showEdit" width="600px"
               @closed="form={}" :destroy-on-close="true" custom-class="ele-dialog-form" :lock-scroll="false">
      <el-form :model="form" ref="editForm" :rules="rules" label-width="82px">
        <el-form-item label="栏目封面:">
          <uploadImage :limit="1" v-model="form.cover"></uploadImage>
        </el-form-item>
        <el-row :gutter="15">
          <el-col :sm="12">
            <el-form-item label="栏目名称:" prop="name">
              <el-input v-model="form.name" placeholder="请输入栏目名称" clearable/>
            </el-form-item>
            <el-form-item label="拼音全拼:" prop="pinyin">
              <el-input v-model="form.pinyin" placeholder="请输入拼音全拼" clearable/>
            </el-form-item>
            <el-form-item label="排序号:" prop="sort">
              <el-input-number v-model="form.sort" controls-position="right" :min="0"
                                placeholder="请输入排序号" class="ele-fluid ele-text-left"/>
            </el-form-item>
          </el-col>
          <el-col :sm="12">
            <el-form-item label="状态:">
              <el-radio-group v-model="form.status">
                <el-radio :label="1">正常</el-radio>
                <el-radio :label="2">禁用</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="拼音简拼:" prop="code">
              <el-input v-model="form.code" placeholder="请输入拼音简拼" clearable/>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="备注:">
          <el-input v-model="form.note" placeholder="请输入备注" :rows="3" type="textarea"/>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="showEdit=false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import uploadImage from '@/components/uploadImage'
import { mapGetters } from "vuex";
export default {
  name: "SysItemCate",
  components: {uploadImage},
  data() {
    return {
      loading: true,  // 加载状态
      data: [],  // 列表数据
      where: {},  // 搜索条件
      showEdit: false,  // 是否显示表单弹窗
      form: {},  // 表单数据
      rules: {  // 表单验证规则
        name: [
          {required: true, message: '请输入栏目名称', trigger: 'blur'}
        ],
        pinyin: [
          {required: true, message: '请输入栏目拼音(全拼)', trigger: 'blur'}
        ],
        code: [
          {required: true, message: '请输入栏目拼音(简拼)', trigger: 'blur'}
        ],
        sort: [
          {required: true, message: '请输入排序号', trigger: 'blur'}
        ]
      }
    }
  },
  computed: {
    ...mapGetters(["permission"]),
  },
  mounted() {
    this.query();
  },
  methods: {
    /* 查询 */
    query() {
      this.loading = true;
      this.$http.get('/itemcate/index', {params: this.where}).then(res => {
        this.loading = false;
        if (res.data.code === 0) {
          this.data = this.$util.toTreeData(res.data.data, 'id', 'pid');
        } else {
          this.$message.error(res.data.msg || '获取数据失败');
        }
      }).catch(e => {
        this.loading = false;
        this.$message.error(e.message);
      });
    },
    /**
     * 异步加载数据
     */
    load(tree, treeNode, resolve) {
      this.where['pid'] = tree.id;
      this.$http.get('/itemcate/index', {params: this.where}).then(res => {
        if (res.data.code === 0) {
          resolve(res.data.data)
        } else {
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        this.$message.error(e.message);
      });
    },
    /* 显示添加 */
    add(row) {
      this.form = {sort: 0, pid: row ? row.id : null};
      this.showEdit = true;
    },
    /* 显示编辑 */
    edit(row) {
      this.form = Object.assign({}, row, {pid: row.pid || null});
      this.showEdit = true;
    },
    /* 保存编辑 */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({lock: true});
          this.$http.post('/itemcate/edit',
              Object.assign({}, this.form, {pid: this.form.pid || 0})
          ).then(res => {
            loading.close();
            if (res.data.code === 0) {
              this.showEdit = false;
              this.$message({type: 'success', message: res.data.msg});
              if (this.form.id) {
                // 更新数据
                this.$util.eachTreeData(this.data, item => {
                  if (item.id === this.form.id) {
                    Object.assign(item, this.form);
                    return false;
                  }
                });
              } else {
                this.query();
              }
            } else {
              this.$message.error(res.data.msg);
            }
          }).catch(e => {
            loading.close();
            this.$message.error(e.message);
          });
        } else {
          return false;
        }
      });
    },
    /* 删除 */
    remove(row) {
      if (row.children && row.children.length > 0) return this.$message.error('请先删除子节点');
      const loading = this.$loading({lock: true});
      this.$http.post('/itemcate/delete', {id:row.id}).then(res => {
        loading.close();
        if (res.data.code === 0) {
          this.$message({type: 'success', message: res.data.msg});
          // 删除数据
          this.$util.eachTreeData(this.data, item => {
            if (item.id === row.pid) {
              item.children.splice(item.children.findIndex(d => d.id === row.id), 1);
              return false;
            }
          });
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