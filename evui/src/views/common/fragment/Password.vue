<!-- 修改密码弹窗 -->
<template>
  <el-dialog title="修改密码" :visible.sync="showPassword" width="400px" @closed="onClose"
             :append-to-body="true" :lock-scroll="false">
    <el-form :model="form" ref="pswForm" :rules="rules" label-width="82px" @keyup.enter.native="save">
      <el-form-item label="旧密码:" prop="oldPassword">
        <el-input v-model="form.oldPassword" placeholder="请输入旧密码" show-password/>
      </el-form-item>
      <el-form-item label="新密码:" prop="password">
        <el-input v-model="form.password" placeholder="请输入新密码" show-password/>
      </el-form-item>
      <el-form-item label="确认密码:" prop="configmPassword">
        <el-input v-model="form.configmPassword" placeholder="请再次输入新密码" show-password/>
      </el-form-item>
    </el-form>
    <div slot="footer">
      <el-button @click="cancel">取消</el-button>
      <el-button type="primary" @click="save">确定</el-button>
    </div>
  </el-dialog>
</template>

<script>
export default {
  name: "ElePassword",
  data() {
    let password2Rule = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请再次输入新密码'));
      } else if (value !== this.form.password) {
        callback(new Error('两次输入密码不一致'));
      } else {
        callback();
      }
    };
    return {
      form: {},  // 表单数据
      rules: {  // 表单验证
        oldPassword: [
          {required: true, message: '请输入旧密码', trigger: 'blur'}
        ],
        password: [
          {required: true, message: '请输入新密码', trigger: 'blur'}
        ],
        configmPassword: [
          {validator: password2Rule, trigger: 'blur'}
        ]
      },
      loading: false  // 按钮loading
    }
  },
  computed: {
    showPassword: {
      get() {
        return this.$store.state.theme.showPassword;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'showPassword', value: val});
      }
    }
  },
  methods: {
    /* 修改密码 */
    save() {
      this.$refs['pswForm'].validate((valid) => {
        if (valid) {
          this.loading = true;
          this.$http.post('/index/updatePwd', {oldPassword: this.form.oldPassword, newPassword: this.form.password}).then(res => {
            this.loading = false;
            if (res.data.code === 0) {
              this.$message({type: 'success', message: res.data.msg});
              this.cancel();
            } else {
              this.$message.error(res.data.msg);
            }
          }).catch(e => {
            this.loading = false;
            this.$message.error(e.message);
          });
        } else {
          return false;
        }
      });
    },
    /* 取消 */
    cancel() {
      this.$store.dispatch('theme/toggle', 'showPassword');
    },
    /* 关闭回调 */
    onClose() {
      this.form = {};
      this.loading = false;
      this.$refs['pswForm'].resetFields();
    }
  }
}
</script>
