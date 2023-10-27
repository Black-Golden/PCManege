<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">

          <el-col :md="6" :sm="12">
            <el-form-item label="会员:">
              <el-input v-model="table.where.user_name" placeholder="请输入会员" clearable/>
            </el-form-item>
          </el-col>
                                            
            <el-col :md="6" :sm="12">
              <el-form-item label="策略类型:">
                <el-select v-model="table.where.strategy_id" placeholder="请选择策略类型" clearable class="ele-fluid">
                  <el-option label="保守" value="1"/>
                  <el-option label="稳健" value="2"/>
                  <el-option label="激进" value="3"/>

                </el-select>
              </el-form-item>
            </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="所属平台:">
              <el-select v-model="table.where.platform_id" placeholder="请选择所属平台" clearable class="ele-fluid">
                <el-option label="欧易" value="2"/>
                <el-option label="币安" value="3"/>

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
<!--        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantsetup:add')">添加-->
<!--        </el-button>-->
        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantsetup:dall')">批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
                  
          <el-table-column prop="user_name" label="会员" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="symbol" label="代币类型" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="rounds" label="做单次数" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="qb_first" label="首单金额" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
<!--          <el-table-column prop="is_double" label="首单是否加倍" sortable="custom" show-overflow-tooltip min-width="120"/>-->
          <el-table-column prop="is_double" label="首单是否加倍" sortable="custom" :resizable="false" min-width="120">
            <template slot-scope="{row}">
              <span v-if="row.is_double==0" style="color: green">否</span>
              <span v-if="row.is_double==1" style="color: blue">是</span>
            </template>
          </el-table-column>
          <el-table-column prop="down_stop_per" label="止损百分比" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="down_back_per" label="止损回调百分比" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="up_stop_per" label="止盈百分比" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="up_back_per" label="止盈回落百分比" sortable="custom" show-overflow-tooltip min-width="120"/>
                                          
          <el-table-column prop="is_run" label="是否运行" sortable="custom" :resizable="false" min-width="120">
              <template slot-scope="{row}">
                <span v-if="row.is_run==0" style="color: green">否</span>
                <span v-if="row.is_run==1" style="color: blue">是</span>
              </template>
          </el-table-column>
                                          
          <el-table-column prop="is_loop" label="是否循环" sortable="custom" :resizable="false" min-width="120">
              <template slot-scope="{row}">
                <span v-if="row.is_loop==0" style="color: green">否</span>
                <span v-if="row.is_loop==1" style="color: blue">是</span>
              </template>
          </el-table-column>
                                
<!--          <el-table-column prop="platform_id" label="所属平台" sortable="custom" show-overflow-tooltip min-width="120"/>-->
          <el-table-column prop="platform_id" label="所属平台" sortable="custom" :resizable="false" min-width="120">

            <template slot-scope="{row}">

              <span v-if="row.platform_id==2" style="color: green">欧易</span>
              <span v-if="row.platform_id==3" style="color: blue">币安</span>
            </template>
          </el-table-column>
<!--          <el-table-column prop="platform_type" label="1是现货2是合约" sortable="custom" show-overflow-tooltip min-width="120"/>-->
          <el-table-column prop="platform_type" label="类别" sortable="custom" :resizable="false" min-width="120">

            <template slot-scope="{row}">

              <span v-if="row.platform_type==1" style="color: green">现货</span>
              <span v-if="row.platform_type==2" style="color: blue">合约</span>
            </template>
          </el-table-column>
          <el-table-column prop="lever" label="合约倍数（针对合约使用）" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="up_back_scale" label="止损比例（针对合约使用）" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
<!--          <el-table-column prop="open_type" label="开仓方向（针对合约使用）1指标趋势 2做多 3做空" sortable="custom" show-overflow-tooltip min-width="120"/>-->
          <el-table-column prop="open_type" label="开仓方向" sortable="custom" :resizable="false" min-width="120">

            <template slot-scope="{row}">

              <span v-if="row.open_type==1" style="color: green">指标趋势</span>
              <span v-if="row.open_type==2" style="color: blue">做多</span>
              <span v-if="row.open_type==2" style="color: red">做空</span>
            </template>
          </el-table-column>
<!--          <el-table-column prop="strategy_id" label="策略类型" sortable="custom" show-overflow-tooltip min-width="120"/>-->
          <el-table-column prop="strategy_id" label="策略类型" sortable="custom" :resizable="false" min-width="120">

            <template slot-scope="{row}">

              <span v-if="row.strategy_id==1" style="color: green">保守</span>
              <span v-if="row.strategy_id==2" style="color: blue">稳健</span>
              <span v-if="row.strategy_id==2" style="color: red">激进</span>
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="创建时间" sortable="custom" show-overflow-tooltip min-width="120"/>
          <el-table-column prop="update_time" label="更新时间" sortable="custom" show-overflow-tooltip min-width="120"/>
          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">
            <template slot-scope="{row}">
<!--              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantsetup:edit')">修改</el-link>-->
              <el-popconfirm title="确定要删除此量化设置吗？" @confirm="remove(row)" class="ele-action">
                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantsetup:delete')">删除</el-link>
              </el-popconfirm>
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改量化设置':'修改量化设置'" :visible.sync="showEdit" width="450px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
                          
        <el-form-item label="会员：" prop="user_id">
            <el-input-number v-model="editForm.user_id" controls-position="right" :min="0"
                              placeholder="请输入会员" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="代币类型：" prop="symbol">
            <el-input v-model="editForm.symbol" placeholder="请输入代币类型" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="做单次数：" prop="rounds">
            <el-input-number v-model="editForm.rounds" controls-position="right" :min="0"
                              placeholder="请输入做单次数" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="首单金额：" prop="qb_first">
            <el-input-number v-model="editForm.qb_first" controls-position="right" :min="0"
                              placeholder="请输入首单金额" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="首单是否加倍：" prop="is_double">
            <el-input-number v-model="editForm.is_double" controls-position="right" :min="0"
                              placeholder="请输入首单是否加倍" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="止损百分比：" prop="down_stop_per">
            <el-input-number v-model="editForm.down_stop_per" controls-position="right" :min="0"
                              placeholder="请输入止损百分比" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="止损回调百分比：" prop="down_back_per">
            <el-input-number v-model="editForm.down_back_per" controls-position="right" :min="0"
                              placeholder="请输入止损回调百分比" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="止盈百分比：" prop="up_stop_per">
            <el-input-number v-model="editForm.up_stop_per" controls-position="right" :min="0"
                              placeholder="请输入止盈百分比" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="止盈回落百分比：" prop="up_back_per">
            <el-input-number v-model="editForm.up_back_per" controls-position="right" :min="0"
                              placeholder="请输入止盈回落百分比" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                  
        <el-form-item label="是否运行：" prop="is_run">
          <el-switch
                  v-model="editForm.is_run"
                  active-text="是"
                  inactive-text="否">
          </el-switch>
        </el-form-item>
                                              
        <el-form-item label="是否循环：" prop="is_loop">
          <el-switch
                  v-model="editForm.is_loop"
                  active-text="是"
                  inactive-text="否">
          </el-switch>
        </el-form-item>
                                                  
        <el-form-item label="所属平台：" prop="platform_id">
            <el-input-number v-model="editForm.platform_id" controls-position="right" :min="0"
                              placeholder="请输入所属平台" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="1是现货2是合约：" prop="platform_type">
            <el-input-number v-model="editForm.platform_type" controls-position="right" :min="0"
                              placeholder="请输入1是现货2是合约" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="合约倍数（针对合约使用）：" prop="lever">
            <el-input-number v-model="editForm.lever" controls-position="right" :min="0"
                              placeholder="请输入合约倍数（针对合约使用）" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="止损比例（针对合约使用）：" prop="up_back_scale">
            <el-input-number v-model="editForm.up_back_scale" controls-position="right" :min="0"
                              placeholder="请输入止损比例（针对合约使用）" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="开仓方向（针对合约使用）1指标趋势 2做多 3做空：" prop="open_type">
            <el-input-number v-model="editForm.open_type" controls-position="right" :min="0"
                              placeholder="请输入开仓方向（针对合约使用）1指标趋势 2做多 3做空" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                  
        <el-form-item label="策略类型：" prop="strategy_id">
          <el-select v-model="editForm.strategy_id" placeholder="请选择策略类型" class="ele-block" clearable>
            <el-option
                    v-for="item in []"
                    :key="item.value"
                    :label="item.name"
                    :value="item.value">
            </el-option>
          </el-select>
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
import { mapGetters } from "vuex";
export default {
  name: "SysQuantSetup",
  data() {
    return {
      table: {url: '/quantsetup/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
              
        user_id: [
          {required: true, message: '请输入会员', trigger: 'blur'}
        ],
                                    
        symbol: [
          {required: true, message: '请输入代币类型', trigger: 'blur'}
        ],
                          
        rounds: [
          {required: true, message: '请输入做单次数', trigger: 'blur'}
        ],
                          
        qb_first: [
          {required: true, message: '请输入首单金额', trigger: 'blur'}
        ],
                          
        is_double: [
          {required: true, message: '请输入首单是否加倍', trigger: 'blur'}
        ],
                          
        down_stop_per: [
          {required: true, message: '请输入止损百分比', trigger: 'blur'}
        ],
                          
        down_back_per: [
          {required: true, message: '请输入止损回调百分比', trigger: 'blur'}
        ],
                          
        up_stop_per: [
          {required: true, message: '请输入止盈百分比', trigger: 'blur'}
        ],
                          
        up_back_per: [
          {required: true, message: '请输入止盈回落百分比', trigger: 'blur'}
        ],
                          
        is_run: [
          {required: true, message: '请选择是否运行', trigger: 'blur'}
        ],
                          
        is_loop: [
          {required: true, message: '请选择是否循环', trigger: 'blur'}
        ],
                          
        platform_id: [
          {required: true, message: '请输入所属平台', trigger: 'blur'}
        ],
                          
        platform_type: [
          {required: true, message: '请输入1是现货2是合约', trigger: 'blur'}
        ],
                          
        lever: [
          {required: true, message: '请输入合约倍数（针对合约使用）', trigger: 'blur'}
        ],
                          
        up_back_scale: [
          {required: true, message: '请输入止损比例（针对合约使用）', trigger: 'blur'}
        ],
                          
        open_type: [
          {required: true, message: '请输入开仓方向（针对合约使用）1指标趋势 2做多 3做空', trigger: 'blur'}
        ],
                          
        strategy_id: [
          {required: true, message: '请选择策略类型', trigger: 'blur'}
        ],
              
      },
    }
  },
computed: {
    ...mapGetters(["permission"]),
},
  mounted() {
  },
  methods: {
    /**
     * 显示编辑
     */
    edit(row) {
      this.$http.get('/quantsetup/info?id=' + row.id).then(res => {
        if (res.data.code === 0) {
          this.editForm = res.data.data;
          this.showEdit = true;
        } else {
          this.$message.error(res.data.msg);
        }
      }).catch(e => {
        this.$message.error(e.message);
      });
    },
    /**
     * 保存编辑
     */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({lock: true});
          this.$http.post('/quantsetup/edit', this.editForm).then(res => {
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
    /**
     * 刪除(批量刪除)
     */
    remove(row) {
      console.log(row)
      if (!row) {  // 批量删除
        if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
        let ids = this.choose.map(d => d.id);
        this.$confirm('确定要删除选中的量化设置吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/quantsetup/delete', {id: ids}).then(res => {
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
        this.$http.post('/quantsetup/delete', {id:row.id}).then(res => {
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
     * 更改是否运行
     */
    setIsRun(row) {
        const loading = this.$loading({lock: true});
        let params = Object.assign({}, row);
        this.$http.post("/quantsetup/setIsRun", params).then(res => {
            loading.close();
            if (res.data.code === 0) {
                this.$message({type: 'success', message: res.data.msg});
            } else {
                row.is_run = !row.is_run ? 2 : 1;
                this.$message.error(res.data.msg);
            }
        }).catch(e => {
            loading.close();
            this.$message.error(e.message);
        });
    },
      
    /**
     * 更改是否循环
     */
    setIsLoop(row) {
        const loading = this.$loading({lock: true});
        let params = Object.assign({}, row);
        this.$http.post("/quantsetup/setIsLoop", params).then(res => {
            loading.close();
            if (res.data.code === 0) {
                this.$message({type: 'success', message: res.data.msg});
            } else {
                row.is_loop = !row.is_loop ? 2 : 1;
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