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
            <el-form-item label="交易对:">
              <el-input
                v-model="table.where.title"
                placeholder="请输入交易对"
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
              <el-button @click=";(table.where = {}) && $refs.table.reload()"
                >重置</el-button
              >
            </div>
          </el-col>
        </el-row>
      </el-form>
      <!-- 操作按钮 -->
      <div class="ele-table-tool ele-table-tool-default">
        <el-button
          @click="showEdit = true"
          type="primary"
          icon="el-icon-plus"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:quantsymbol:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:quantsymbol:dall')"
          >批量删除
        </el-button>
      </div>
      <!-- 数据表格 -->
      <ele-data-table
        ref="table"
        :config="table"
        :choose.sync="choose"
        height="calc(100vh - 315px)"
        highlight-current-row
      >
        <template slot-scope="{ index }">
          <el-table-column
            type="selection"
            width="45"
            align="center"
            fixed="left"
          />
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
            label="交易对"
            show-overflow-tooltip
            min-width="120"
          />

          <!-- <el-table-column
            prop="token"
            label="token"
             
            show-overflow-tooltip
            min-width="120"
          /> -->

          <!-- <el-table-column
            prop="oprice"
            label="欧易价格"
             
            show-overflow-tooltip
            min-width="120"
          /> -->

          <el-table-column
            prop="ohprice"
            label="欧易U本位当前价"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="bhprice"
            label="币安U本位当前价"
            show-overflow-tooltip
            min-width="120"
          />

          <!-- <el-table-column
            prop="bprice"
            label="币安价格"
             
            show-overflow-tooltip
            min-width="120"
          /> -->

          <!-- <el-table-column
            prop="decimal"
            label="精准类型"
            show-overflow-tooltip
            min-width="120"
          /> -->

          <!--          <el-table-column prop="order" label="排序"   show-overflow-tooltip min-width="120"/>-->
          <!--                                          -->
          <!--          <el-table-column prop="is_online" label="是否在线"   :resizable="false" min-width="120">-->
          <!--              <template slot-scope="{row}">-->
          <!--                  <el-switch v-model="row.is_online" @change="setIsOnline(row)" :active-value="1" :inactive-value="2"/>-->
          <!--              </template>-->
          <!--          </el-table-column>-->
          <!-- <el-table-column
            prop="is_online"
            label="是否在线"
            :resizable="false"
            min-width="120"
          >
            <template slot-scope="{ row }">
              <span v-if="row.is_online == 0" style="color: red">否</span>
              <span v-if="row.is_online == 1" style="color: green">是</span>
            </template>
          </el-table-column> -->

          <!-- <el-table-column
            prop="ct_val"
            label="合约面值"
            show-overflow-tooltip
            min-width="120"
          /> -->

          <!-- <el-table-column
            prop="create_time"
            label="创建时间"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="update_time"
            label="更新时间"
            show-overflow-tooltip
            min-width="120"
          /> -->
          <!-- <el-table-column
            label="操作"
            width="130px"
            align="center"
            :resizable="false"
            fixed="right"
          >
            <template slot-scope="{ row }">
              <el-link
                @click="edit(row)"
                icon="el-icon-edit"
                type="primary"
                :underline="false"
                v-if="permission.includes('sys:quantsymbol:edit')"
                >修改</el-link
              >
              <el-popconfirm
                title="确定要删除此代币种类吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:quantsymbol:delete')"
                  >删除</el-link
                >
              </el-popconfirm>
            </template>
          </el-table-column> -->
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog
      title="添加代币种类"
      :visible.sync="showEdit"
      width="900px"
      @closed="editForm = {}"
      :destroy-on-close="true"
      :lock-scroll="false"
    >
      <el-form
        :model="editForm"
        ref="editForm"
        :rules="editRules"
        label-width="auto"
      >
        <el-row>
          <el-col :sm="11"
            ><el-form-item
              label="交易对："
              prop="title"
              style="margin-bottom:20px"
            >
              <el-input
                v-model="editForm.title"
                placeholder="请输入标识"
                clearable
              /> </el-form-item
          ></el-col>
          <el-col :sm="11"
            ><el-form-item
              label="token："
              prop="token"
              style="margin-bottom:20px"
            >
              <el-input
                v-model="editForm.token"
                placeholder="请输入token"
                clearable
              /> </el-form-item
          ></el-col>
        </el-row>
        <!-- <el-row>
          <el-col :sm="11">
            <el-form-item
              label="欧易价格："
              prop="oprice"
              style="margin-bottom:20px"
            >
              <el-input-number
                disabled
                v-model="editForm.oprice"
                controls-position="right"
                :min="0"
                placeholder="请输入欧易价格"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="11">
            <el-form-item
              label="欧易U本位价格："
              prop="ohprice"
              style="margin-bottom:20px"
            >
              <el-input-number
                disabled
                v-model="editForm.ohprice"
                controls-position="right"
                :min="0"
                placeholder="请输入欧易U本位价格"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :sm="11">
            <el-form-item
              label="币安U本位价格："
              prop="bhprice"
              style="margin-bottom:20px"
            >
              <el-input-number
                disabled
                v-model="editForm.bhprice"
                controls-position="right"
                :min="0"
                placeholder="请输入币安U本位价格"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="11">
            <el-form-item
              label="币安价格："
              prop="bprice"
              style="margin-bottom:20px"
            >
              <el-input-number
                disabled
                v-model="editForm.bprice"
                controls-position="right"
                :min="0"
                placeholder="请输入币安价格"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
        </el-row> -->
        <!-- <el-row>
          <el-col :sm="11"
            ><el-form-item
              label="精准类型："
              prop="decimal"
              style="margin-bottom:20px"
            >
              <el-input-number
                disabled
                v-model="editForm.decimal"
                controls-position="right"
                :min="0"
                placeholder="请输入精准类型"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="11">
            <el-form-item
              label="排序："
              prop="order"
              style="margin-bottom:20px"
            >
              <el-input-number
                v-model="editForm.order"
                controls-position="right"
                :min="0"
                placeholder="请输入排序"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :sm="11"
            ><el-form-item
              label="合约面值："
              prop="ct_val"
              style="margin-bottom:20px"
            >
              <el-input-number
                disabled
                v-model="editForm.ct_val"
                controls-position="right"
                :min="0"
                placeholder="请输入合约面值"
                class="ele-fluid ele-text-left"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="11"
            ><el-form-item
              label="是否在线："
              prop="is_online"
              style="padding: 12px 0;margin-bottom:0"
            >
              <el-radio v-model="editForm.is_online" label="1">是</el-radio>
              <el-radio v-model="editForm.is_online" label="0">否</el-radio>
            </el-form-item>
          </el-col>
        </el-row> -->
      </el-form>
      <div slot="footer">
        <el-button @click="showEdit = false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  name: 'SysQuantSymbol',
  data() {
    return {
      table: { url: '/quantsymbol/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        title: [{ required: true, message: '请输入标识', trigger: 'blur' }],

        token: [{ required: true, message: '请输入token', trigger: 'blur' }],

        oprice: [
          { required: true, message: '请输入欧易价格', trigger: 'blur' },
        ],

        ohprice: [
          { required: true, message: '请输入欧易U本位价格', trigger: 'blur' },
        ],

        bhprice: [
          { required: true, message: '请输入币安U本位价格', trigger: 'blur' },
        ],

        bprice: [
          { required: true, message: '请输入币安价格', trigger: 'blur' },
        ],

        decimal: [
          { required: true, message: '请输入精准类型', trigger: 'blur' },
        ],

        order: [{ required: true, message: '请输入排序', trigger: 'blur' }],

        is_online: [
          { required: true, message: '请选择是否在线', trigger: 'blur' },
        ],

        ct_val: [
          { required: true, message: '请输入合约面值', trigger: 'blur' },
        ],
      },
    }
  },
  computed: {
    ...mapGetters(['permission']),
  },
  mounted() {},
  methods: {
    /**
     * 显示编辑
     */
    edit(row) {
      this.$http
        .get('/quantsymbol/info?id=' + row.id)
        .then((res) => {
          if (res.data.code === 0) {
            this.editForm = res.data.data
            this.showEdit = true
          } else {
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          this.$message.error(e.message)
        })
    },
    /**
     * 保存编辑
     */
    save() {
      this.$refs['editForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({ lock: true })
          this.$http
            .post('/quantsymbol/edit', this.editForm)
            .then((res) => {
              loading.close()
              if (res.data.code === 0) {
                this.showEdit = false
                this.$message({ type: 'success', message: res.data.msg })
                this.$refs.table.reload()
              } else {
                this.$message.error(res.data.msg)
              }
            })
            .catch((e) => {
              loading.close()
              this.$message.error(e.message)
            })
        } else {
          return false
        }
      })
    },
    /**
     * 刪除(批量刪除)
     */
    remove(row) {
      console.log(row)
      if (!row) {
        // 批量删除
        if (this.choose.length === 0)
          return this.$message.error('请至少选择一条数据')
        let ids = this.choose.map((d) => d.id)
        this.$confirm('确定要删除选中的代币种类吗?', '提示', {
          type: 'warning',
        })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/quantsymbol/delete', { id: ids })
              .then((res) => {
                loading.close()
                if (res.data.code === 0) {
                  this.$message({ type: 'success', message: res.data.msg })
                  this.$refs.table.reload()
                } else {
                  this.$message.error(res.data.msg)
                }
              })
              .catch((e) => {
                loading.close()
                this.$message.error(e.message)
              })
          })
          .catch(() => 0)
      } else {
        // 单个删除
        const loading = this.$loading({ lock: true })
        this.$http
          .post('/quantsymbol/delete', { id: row.id })
          .then((res) => {
            loading.close()
            if (res.data.code === 0) {
              this.$message({ type: 'success', message: res.data.msg })
              this.$refs.table.reload()
            } else {
              this.$message.error(res.data.msg)
            }
          })
          .catch((e) => {
            loading.close()
            this.$message.error(e.message)
          })
      }
    },

    /**
     * 更改是否在线
     */
    setIsOnline(row) {
      const loading = this.$loading({ lock: true })
      let params = Object.assign({}, row)
      this.$http
        .post('/quantsymbol/setIsOnline', params)
        .then((res) => {
          loading.close()
          if (res.data.code === 0) {
            this.$message({ type: 'success', message: res.data.msg })
          } else {
            row.is_online = !row.is_online ? 2 : 1
            this.$message.error(res.data.msg)
          }
        })
        .catch((e) => {
          loading.close()
          this.$message.error(e.message)
        })
    },
  },
}
</script>

<style scoped>
.ele-block >>> .el-upload,
.ele-block >>> .el-upload-dragger {
  width: 100%;
}
</style>
