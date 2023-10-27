<template>
  <div class="ele-body">
    <el-card shadow="never">
      <!-- 搜索表单 -->
      <el-form :model="table.where" label-width="77px" class="ele-form-search"
               @keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
        <el-row :gutter="15">

          <el-col :md="6" :sm="12">
            <el-form-item label="用户名:">
              <el-input v-model="table.where.user_name" placeholder="请输入用户名" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="代币种类:">
              <el-input v-model="table.where.symbol" placeholder="请输入代币种类" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="订单号:">
              <el-input v-model="table.where.serno" placeholder="请输入订单号" clearable/>
            </el-form-item>
          </el-col>
          <el-col :md="6" :sm="12">
            <el-form-item label="平台订单号:">
              <el-input v-model="table.where.order_id" placeholder="请输入平台订单号" clearable/>
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
<!--        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantmarketbuy:add')">添加-->
<!--        </el-button>-->
<!--        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantmarketbuy:dall')">批量删除-->
<!--        </el-button>-->
      </div>
      <!-- 数据表格 -->
      <ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)" highlight-current-row>
        <template slot-scope="{index}">
          <el-table-column type="selection" width="45" align="center" fixed="left"/>
          <el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left" show-overflow-tooltip/>
                  
          <el-table-column prop="user_name" label="管理用户" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="symbol" label="代币种类" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="rounds" label="补仓次数" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="serno" label="订单号" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="order_id" label="平台订单号" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="qb_source" label="USDT数量" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="price" label="购买行情价" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="qb_to" label="购买数量" sortable="custom" show-overflow-tooltip min-width="120"/>
                                          
          <el-table-column prop="is_deal" label="是否成交" sortable="custom" :resizable="false" min-width="120">
              <template slot-scope="{row}">
                  <el-switch v-model="row.is_deal" @change="setIsDeal(row)" :active-value="1" :inactive-value="2"/>
              </template>
          </el-table-column>
                                          
          <el-table-column prop="is_sale" label="是否卖出" sortable="custom" :resizable="false" min-width="120">
              <template slot-scope="{row}">
                  <el-switch v-model="row.is_sale" @change="setIsSale(row)" :active-value="1" :inactive-value="2"/>
              </template>
          </el-table-column>
                                
          <el-table-column prop="qb_fee" label="对应的手续费" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="platform_id" label="所属平台" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="platform_type" label="1是现货2是合约" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="lever" label="合约倍数（针对合约使用）" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="open_type" label="开仓方向（针对合约使用）1指标趋势 2做多 3做空" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="bail" label="保证金（针对合约使用）" sortable="custom" show-overflow-tooltip min-width="120"/>
                                
          <el-table-column prop="add_bail" label="追加保证金（针对合约使用）" sortable="custom" show-overflow-tooltip min-width="120"/>

          <el-table-column prop="create_time" label="创建时间" sortable="custom" show-overflow-tooltip min-width="120"/>
          <el-table-column prop="update_time" label="更新时间" sortable="custom" show-overflow-tooltip min-width="120"/>
<!--          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">-->
<!--            <template slot-scope="{row}">-->
<!--&lt;!&ndash;              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantmarketbuy:edit')">修改</el-link>&ndash;&gt;-->
<!--              <el-popconfirm title="确定要删除此交易买入吗？" @confirm="remove(row)" class="ele-action">-->
<!--                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantmarketbuy:delete')">删除</el-link>-->
<!--              </el-popconfirm>-->
<!--            </template>-->
<!--          </el-table-column>-->
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog :title="editForm.id?'修改交易买入':'修改交易买入'" :visible.sync="showEdit" width="450px"
               @closed="editForm={}" :destroy-on-close="true" :lock-scroll="false">
      <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
                          
        <el-form-item label="管理用户：" prop="user_id">
            <el-input-number v-model="editForm.user_id" controls-position="right" :min="0"
                              placeholder="请输入管理用户" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="代币种类：" prop="symbol">
            <el-input v-model="editForm.symbol" placeholder="请输入代币种类" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="补仓次数：" prop="rounds">
            <el-input-number v-model="editForm.rounds" controls-position="right" :min="0"
                              placeholder="请输入补仓次数" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="订单号：" prop="serno">
            <el-input v-model="editForm.serno" placeholder="请输入订单号" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="平台订单号：" prop="order_id">
            <el-input v-model="editForm.order_id" placeholder="请输入平台订单号" clearable/>
        </el-form-item>
                                                  
        <el-form-item label="USDT数量：" prop="qb_source">
            <el-input-number v-model="editForm.qb_source" controls-position="right" :min="0"
                              placeholder="请输入USDT数量" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="购买行情价：" prop="price">
            <el-input-number v-model="editForm.price" controls-position="right" :min="0"
                              placeholder="请输入购买行情价" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="购买数量：" prop="qb_to">
            <el-input-number v-model="editForm.qb_to" controls-position="right" :min="0"
                              placeholder="请输入购买数量" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                  
        <el-form-item label="是否成交：" prop="is_deal">
          <el-switch
                  v-model="editForm.is_deal"
                  active-text="是"
                  inactive-text="否">
          </el-switch>
        </el-form-item>
                                              
        <el-form-item label="是否卖出：" prop="is_sale">
          <el-switch
                  v-model="editForm.is_sale"
                  active-text="是"
                  inactive-text="否">
          </el-switch>
        </el-form-item>
                                                  
        <el-form-item label="对应的手续费：" prop="qb_fee">
            <el-input-number v-model="editForm.qb_fee" controls-position="right" :min="0"
                              placeholder="请输入对应的手续费" class="ele-fluid ele-text-left"/>
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
                                                      
        <el-form-item label="开仓方向（针对合约使用）1指标趋势 2做多 3做空：" prop="open_type">
            <el-input-number v-model="editForm.open_type" controls-position="right" :min="0"
                              placeholder="请输入开仓方向（针对合约使用）1指标趋势 2做多 3做空" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="保证金（针对合约使用）：" prop="bail">
            <el-input-number v-model="editForm.bail" controls-position="right" :min="0"
                              placeholder="请输入保证金（针对合约使用）" class="ele-fluid ele-text-left"/>
        </el-form-item>
                                                      
        <el-form-item label="追加保证金（针对合约使用）：" prop="add_bail">
            <el-input-number v-model="editForm.add_bail" controls-position="right" :min="0"
                              placeholder="请输入追加保证金（针对合约使用）" class="ele-fluid ele-text-left"/>
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
  name: "SysQuantMarketBuy",
  data() {
    return {
      table: {url: '/quantmarketbuy/index', where: {}},  // 表格配置
      choose: [],  // 表格选中数据
      showEdit: false,  // 是否显示表单弹窗
      editForm: {},  // 表单数据
      editRules: {  // 表单验证规则
              
        user_id: [
          {required: true, message: '请输入管理用户', trigger: 'blur'}
        ],
                                    
        symbol: [
          {required: true, message: '请输入代币种类', trigger: 'blur'}
        ],
                          
        rounds: [
          {required: true, message: '请输入补仓次数', trigger: 'blur'}
        ],
                                    
        serno: [
          {required: true, message: '请输入订单号', trigger: 'blur'}
        ],
                                    
        order_id: [
          {required: true, message: '请输入平台订单号', trigger: 'blur'}
        ],
                          
        qb_source: [
          {required: true, message: '请输入USDT数量', trigger: 'blur'}
        ],
                          
        price: [
          {required: true, message: '请输入购买行情价', trigger: 'blur'}
        ],
                          
        qb_to: [
          {required: true, message: '请输入购买数量', trigger: 'blur'}
        ],
                          
        is_deal: [
          {required: true, message: '请选择是否成交', trigger: 'blur'}
        ],
                          
        is_sale: [
          {required: true, message: '请选择是否卖出', trigger: 'blur'}
        ],
                          
        qb_fee: [
          {required: true, message: '请输入对应的手续费', trigger: 'blur'}
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
                          
        open_type: [
          {required: true, message: '请输入开仓方向（针对合约使用）1指标趋势 2做多 3做空', trigger: 'blur'}
        ],
                          
        bail: [
          {required: true, message: '请输入保证金（针对合约使用）', trigger: 'blur'}
        ],
                          
        add_bail: [
          {required: true, message: '请输入追加保证金（针对合约使用）', trigger: 'blur'}
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
      this.$http.get('/quantmarketbuy/info?id=' + row.id).then(res => {
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
          this.$http.post('/quantmarketbuy/edit', this.editForm).then(res => {
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
        this.$confirm('确定要删除选中的交易买入吗?', '提示', {type: 'warning'}).then(() => {
          const loading = this.$loading({lock: true});
          this.$http.post('/quantmarketbuy/delete', {id: ids}).then(res => {
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
        this.$http.post('/quantmarketbuy/delete', {id:row.id}).then(res => {
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
     * 更改是否成交
     */
    setIsDeal(row) {
        const loading = this.$loading({lock: true});
        let params = Object.assign({}, row);
        this.$http.post("/quantmarketbuy/setIsDeal", params).then(res => {
            loading.close();
            if (res.data.code === 0) {
                this.$message({type: 'success', message: res.data.msg});
            } else {
                row.is_deal = !row.is_deal ? 2 : 1;
                this.$message.error(res.data.msg);
            }
        }).catch(e => {
            loading.close();
            this.$message.error(e.message);
        });
    },
      
    /**
     * 更改是否卖出
     */
    setIsSale(row) {
        const loading = this.$loading({lock: true});
        let params = Object.assign({}, row);
        this.$http.post("/quantmarketbuy/setIsSale", params).then(res => {
            loading.close();
            if (res.data.code === 0) {
                this.$message({type: 'success', message: res.data.msg});
            } else {
                row.is_sale = !row.is_sale ? 2 : 1;
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