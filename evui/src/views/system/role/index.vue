<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">
          <el-col :md="6" :sm="12">
            <el-form-item label="角色名称:">
              <el-input v-model="table.where.name" placeholder="请输入角色名称" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="9" :sm="12">
            <div class="ele-form-actions">
              <el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search" class="ele-btn-icon">查询
              </el-button>
              <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" v-if="permission.includes('sys:role:add')">添加</el-button>
              <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" v-if="permission.includes('sys:role:dall')">批量删除</el-button>
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 260px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
          <el-table-column prop="name" label="角色名称" sortable="custom" show-overflow-tooltip min-width="120" align="center"/>
          <el-table-column prop="code" label="角色编码" show-overflow-tooltip min-width="100" align="center"/>
          <el-table-column prop="note" label="备注" show-overflow-tooltip min-width="150" align="center"/>
          <el-table-column prop="create_time" label="创建时间" show-overflow-tooltip min-width="110" align="center">
            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
          </el-table-column>
          <el-table-column label="操作" width="230px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:role:edit')">修改</el-link>
              <el-link @click="auth(row)" icon="el-icon-finished" type="primary" :underline="false" v-if="permission.includes('sys:role:permission')">分配权限</el-link>
              <el-popconfirm title="确定要删除此角色吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:role:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改角色':'添加角色'" :visible.sync="showEdit" width="400px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="82px">
        <el-form-item label="角色名称:" prop="name">
          <el-input v-model="editForm.name" placeholder="请输入角色名称" clearable/>
        </el-form-item>
        <el-form-item label="角色标识:" prop="code">
          <el-input v-model="editForm.code" placeholder="请输入角色标识" clearable/>
        </el-form-item>
        <el-form-item label="备注:">
          <el-input v-model="editForm.note" placeholder="请输入备注" :rows="4" type="textarea"/>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="showEdit=false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </div>
    </el-dialog>
    <!-- 权限分配弹窗 -->
    <el-dialog title="分配权限" :visible.sync="showAuth" width="400px" @closed="editForm={}" :destroy-on-close="true"
               :lock-scroll="false">
      <el-scrollbar style="height:50vh;" wrapStyle="overflow-x: hidden;">
        <el-tree ref="authTree" :data="authData" :props="{label:'title'}" node-key="id"
                 :default-expand-all="true" :default-checked-keys="authChecked" show-checkbox/>
      </el-scrollbar>
      <div slot="footer">
        <el-button @click="showAuth=false">取消</el-button>
        <el-button type="primary" @click="saveAuth">保存</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "SysRole",
  data() {
    return {
      table: {url: '/role/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
        name: [
          {required: true, message: '请输入角色名称', trigger: 'blur'}
        ],
        code: [
          {required: true, message: '请输入角色编码', trigger: 'blur'}
        ]
      },
      showAuth: false,  // 显示权限分配弹窗
      authData: []  // 权限分配数据
    }
  },
  computed: {
    ...mapGetters(["permission"]),
    
    /* 权限树选中数据 */
    authChecked() {
      let checked = [];
      this.$util.eachTreeData(this.authData, d => {
        if (d.checked && (!d.children || !d.children.length)) checked.push(d.id);
      });
      return checked;
    }
  },
  methods: {
    /* 显示编辑 */
    edit(row) {
      this.editForm = Object.assign({}, row);
      this.showEdit = true;
    },
    /* 保存编辑 */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({lock: true});
          this.$http.post('/role/edit', this.editForm).then(res => {
            loading.close();
            if (res.data.code === 0) {
              this.showEdit = false;
              this.$message({type: 'success', message: res.data.msg});
              this.$refs.table.reload();
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
      if (!row) {  // 批量删除
        if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
        let ids = this.choose.map(d => d.id);
        this.$confirm('确定要删除选中的角色吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/role/delete', {id: ids}).then(res => {
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
        this.$http.post('/role/delete', {id:row.id}).then(res => {
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
    /* 显示分配权限弹窗 */
    auth(row) {
      this.editForm = Object.assign({}, row);
      this.authData = [];
      const loading = this.$loading({background: 'transparent'});
      this.$http.get('/role/getPermissionList?role_id=' + row.id).then(res => {
        loading.close();
        this.showAuth = true;
        if (res.data.code === 0) {
          this.authData = this.$util.toTreeData(res.data.data, 'id', 'pid');
        } else {
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        loading.close();
        this.$message.error(e.message);
      });
    },
    /* 保存权限分配 */
    saveAuth() {
      const loading = this.$loading({lock: true});
      let ids = this.$refs.authTree.getCheckedKeys().concat(this.$refs.authTree.getHalfCheckedKeys());
      this.$http.post('/role/savePermission?role_id=' + this.editForm.id, ids).then(res => {
        loading.close();
        if (res.data.code === 0) {
          this.showAuth = false;
          this.$message({type: 'success', message: res.data.msg});
        } else {
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        loading.close();
        this.$message.error(e.message);
      });
    }
  }
}
</script>

<style scoped>

</style>