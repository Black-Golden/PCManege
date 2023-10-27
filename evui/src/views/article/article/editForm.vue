<template>
  <el-drawer
    class="edit-table"
    title="文章"
    :visible.sync="visible"
    :direction="direction"
    :size="'calc(100vw - 256px)'"
    :before-close="handleClose"
  >
    <el-form :model="editForm" ref="editForm" :rules="editRules" label-width="150px" style="margin:10px 30px;">
      <el-form-item label="文章封面：">
        <uploadImage :limit="1" v-model="editForm.cover"></uploadImage>
      </el-form-item>
      <el-row :gutter="15">
        <el-col :sm="12">
          <el-form-item label="文章标题：" prop="title">
            <el-input v-model="editForm.title" placeholder="请输入文章标题" clearable/>
          </el-form-item>
          <el-form-item label="所属站点：" prop="item_id" :rules="{
                  required: true, message: '所属站点不能为空', trigger: 'change'
                }">
            <el-select filterable clearable v-model="editForm.item_id" size="small" placeholder="-请选择所属站点-" class="ele-block">
              <el-option v-for="item in itemList" :key="item.id" :label="item.name" :value="item.id"/>
            </el-select>
          </el-form-item>
        </el-col>
        <el-col :sm="12">
          <el-form-item label="状态：" prop="status">
            <el-select v-model="editForm.status" placeholder="请选择状态：" class="ele-block" clearable>
              <el-option label="待审核" :value="0"/>
              <el-option label="审核通过" :value="1"/>
              <el-option label="审核拒绝" :value="2"/>
            </el-select>
          </el-form-item>
          <el-form-item label="是否置顶:">
            <el-radio-group v-model="editForm.is_top">
              <el-radio :label="1">置顶</el-radio>
              <el-radio :label="2">不置顶</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-col>
      </el-row>
      <el-form-item label="文章导读:">
        <el-input v-model="editForm.guide" placeholder="请输入文章导读" :rows="4" type="textarea"/>
      </el-form-item>
      <!-- 富文本编辑器 -->
      <el-form-item label="文章内容：" prop="content">
        <tinymce-editor v-model="editForm.content" :init="initEditor"/>
      </el-form-item>
      <el-form-item style="text-align: center;margin-left:-100px;margin-top:10px;">
        <el-button @click="save" type="primary" size="medium">保存 </el-button>
        <el-button @click="$emit('returnBack')" size="medium">返回</el-button>
      </el-form-item>
    </el-form>
  </el-drawer>
</template>

<script>
  import uploadImage from '@/components/uploadImage'
  import TinymceEditor from '@/components/TinymceEditor'
  export default {
    name: "ArticleEdit",
    components: {uploadImage,TinymceEditor},
    data() {
      return {
        direction: 'rtl',
        editForm: {status:1, is_top:2},  // 表单数据
        editRules: {  // 表单验证规则
          title: [
            {required: true, message: '请输入文章标题', trigger: 'blur'}
          ],
          item_id: [
            {required: true, message: '请选择所属站点', trigger: 'blur'}
          ],
          status: [
            {required: true, message: '请选择审核状态', trigger: 'blur'}
          ],
        },
      };
    },
    props: {
      visible: {
        type: Boolean,
        default() {
          return false;
        }
      },
      articleId: {
        type: Number,
        default() {
          return 0;
        }
      },
      // 接收站点
      itemList:{
        type:Array,
        default(){
          return []
        }
      },
    },
    computed: {
      /**
       * 初始化编辑器
       */
      initEditor() {
        return {
          height: 525,
          file_picker_callback: this.file_picker_callback,
          skin_url: this.$store.state.theme.theme === 'dark' ? '/tinymce/skins/ui/oxide-dark' : '/tinymce/skins/ui/oxide',
          content_css: this.$store.state.theme.theme === 'dark' ? '/tinymce/skins/content/dark/content.css' : '/tinymce/skins/content/default/content.css'
        };
      }
    },
    mounted() {
      this.getArticleInfo();
    },
    methods: {
      /**
       * 获取文章详情
       */
      getArticleInfo(){
        if (this.articleId > 0) {
          this.$http.get('/article/info?id=' + this.articleId).then(res => {
            if (res.data.code === 0) {
              this.editForm = res.data.data;
            } else {
              this.$message.error(res.data.msg);
            }
          }).catch(e => {
            this.$message.error(e.message);
          });
        }
      },
      /* 保存编辑 */
      save() {
        this.$refs['editForm'].validate((valid) => {
          if (valid) {
            const loading = this.$loading({lock: true});
            this.$http.post('/article/edit', this.editForm).then(res => {
              loading.close();
              if (res.data.code === 0) {
                this.$message({type: 'success', message: res.data.msg});
                this.$emit('reload')
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
       * 关闭页面
       */
      handleClose(hide) {
        if (hide) {
          hide();
        }
        this.$emit("reload");
      }
    }
  };
</script>

<style scoped>
/* 选项卡 */
.demo-icon-tabs >>> .el-tabs__nav-scroll {
  padding: 0 20px;
}

.demo-icon-tabs >>> .el-tabs__item {
  height: 45px;
  line-height: 45px;
}
</style>
