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
            <el-form-item label="关键字:">
              <el-input
                v-model="table.where.name"
                placeholder="请输入关键字"
                clearable
              />
            </el-form-item>
          </el-col>

          <el-col :md="6" :sm="12">
            <el-form-item label="标题:">
              <el-input
                v-model="table.where.title"
                placeholder="请输入标题"
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
          v-if="permission.includes('sys:quantconfig:add')"
          >添加
        </el-button>
        <!--        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantconfig:dall')">批量删除-->
        <!--        </el-button>-->
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
            prop="name"
            label="关键字"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="title"
            label="标题"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <el-table-column
            prop="value"
            label="value"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="create_time"
            label="创建时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />
          <el-table-column
            prop="update_time"
            label="更新时间"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          />

          <!--          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">-->
          <!--            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>-->
          <!--          </el-table-column>-->
          <!--          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">-->
          <!--            <template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>-->
          <!--          </el-table-column>-->
          <el-table-column
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
                v-if="permission.includes('sys:quantconfig:edit')"
                >修改</el-link
              >
              <!--              <el-popconfirm title="确定要删除此后台配置吗？" @confirm="remove(row)" class="ele-action">-->
              <!--                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantconfig:delete')">删除</el-link>-->
              <!--              </el-popconfirm>-->
            </template>
          </el-table-column>
        </template>
      </ele-data-table>
    </el-card>
    <!-- 编辑弹窗 -->
    <el-dialog
      @open="openclick(editForm.id)"
      :title="editForm.id ? '修改后台配置' : '新增后台配置'"
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
        <el-form-item label="关键字：" prop="name">
          <el-input
            v-model="editForm.name"
            :disabled="disabled"
            placeholder="请输入关键字"
            clearable
          />
        </el-form-item>

        <el-form-item label="标题：" prop="title">
          <el-input
            v-model="editForm.title"
            placeholder="请输入标题"
            clearable
          />
        </el-form-item>
        <el-form-item v-if="editForm.type == 1" label="value：" prop="value">
          <el-input-number
            v-model="editForm.value"
            controls-position="right"
            placeholder="请输入更新信息"
          />
        </el-form-item>
        <el-form-item v-if="editForm.type == 2" label="value：" prop="value">
          <!-- <el-input
            v-model="editForm.value"
            placeholder="请输入value"
            clearable
          /> -->
          <template>
            <quill-editor
              v-model="editForm.value"
              :options="editorOption"
            ></quill-editor>
          </template>
        </el-form-item>
        <el-form-item v-if="editForm.type == 3" label="value：" prop="value">
          <el-input
            type="textarea"
            v-model="editForm.value"
            placeholder="请输入内容"
            clearable
          />
        </el-form-item>
        <el-form-item v-if="editForm.type == 4" label="value：" prop="value">
          <!-- <el-input
            type="textarea"
            v-model="editForm.value"
            placeholder="请输入内容"
            clearable
          /> -->

          <el-row>
            <el-col :sm="6">
              <el-image
                style="width: 100px; height: 100px"
                :src="editForm.value"
                :fit="['scale-down']"
              ></el-image
            ></el-col>
            <el-col :sm="6">
              <el-upload
                ref="upload"
                :action="action"
                :on-success="uploadSuccess"
                :on-preview="handlePreview"
                :on-remove="handleRemove"
                :file-list="fileList"
                :auto-upload="false"
                :headers="headers"
              >
                <el-button style="margin-top:15px" size="small" type="primary"
                  >上传</el-button
                >
              </el-upload>
              <el-button style="margin-top:5px" @click="upload"
                >点击上传文件</el-button
              ></el-col
            >
          </el-row>
          <!-- <uploadImage :limit="1" v-model="editForm.value"></uploadImage> -->
        </el-form-item>
        <el-form-item v-if="editForm.type == 5" label="value：" prop="value">
          <el-upload
            ref="upload"
            :on-success="uploadSuccess"
            action="https://api.wending.7w1.top/adminapi/upload/uploadFile"
            :on-preview="handlePreview"
            :on-remove="handleRemove"
            :file-list="fileList"
            :auto-upload="false"
            :headers="headers"
          >
            <el-button size="small" type="primary">上传</el-button>
          </el-upload>
          <el-button @click="upload">点击上传文件</el-button>
          <!-- <el-input
            type="textarea"
            v-model="editForm.value"
            placeholder="请输入内容"
            clearable
          /> -->
          <!-- <template>
            <uploadImage :limit="1" v-model="editForm.value"></uploadImage>
          </template> -->
        </el-form-item>
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
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
// import uploadImage from '@/components/uploadImage'
import setting from '@/config/setting'

export default {
  name: 'SysQuantConfig',
  data() {
    return {
      fileList: [],
      action: 'https://api.wending.7w1.top/adminapi/upload/uploadImage',
      modeList: [],
      headers: {
        authorization: localStorage.getItem(setting.tokenStoreName),
      },
      disabled: true, //是否禁用文本框
      table: { url: '/quantconfig/index', where: {} }, // 表格配置
      choose: [], // 表格选中数据
      showEdit: false, // 是否显示表单弹窗
      editForm: {}, // 表单数据
      editRules: {
        // 表单验证规则

        name: [{ required: true, message: '请输入关键字', trigger: 'blur' }],

        title: [{ required: true, message: '请输入标题', trigger: 'blur' }],

        value: [{ required: true, message: '请输入value', trigger: 'blur' }],
      },
      editorOption: {
        // 富文本编辑器 定义功能
        // 占位配置
        placeholder: '',
        modules: {
          // 配置工具栏
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ header: 1 }, { header: 2 }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ indent: '-1' }, { indent: '+1' }],
            ['image'],
          ],
        },
      },
    }
  },
  computed: {
    ...mapGetters(['permission']),
  },
  mounted() {
    console.log(this.headers)
  },
  components: {
    quillEditor,
    // uploadImage,
  },
  methods: {
    uploadSuccess(response) {
      this.editForm.value = response.data
    },
    handleRemove(file, fileList) {
      console.log(file, fileList)
    },
    handlePreview(file) {
      console.log(file)
    },
    upload() {
      this.$refs.upload.submit()
    },
    // 判断是否禁用文本框
    openclick(id) {
      // console.log(typeof id)
      if (id) {
        this.disabled = true
      } else {
        this.disabled = false
      }
    },
    /**
     * 显示编辑
     */
    edit(row) {
      this.$http
        .get('/quantconfig/info?id=' + row.id)
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
            .post('/quantconfig/edit', this.editForm)
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
        this.$confirm('确定要删除选中的后台配置吗?', '提示', {
          type: 'warning',
        })
          .then(() => {
            const loading = this.$loading({ lock: true })
            this.$http
              .post('/quantconfig/delete', { id: ids })
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
          .post('/quantconfig/delete', { id: row.id })
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
  },
}
</script>

<style scoped>
.ele-block >>> .el-upload,
.ele-block >>> .el-upload-dragger {
  width: 100%;
}
</style>
