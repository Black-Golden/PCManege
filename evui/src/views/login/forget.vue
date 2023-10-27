<template>
  <div class="login-wrapper">
    <el-form ref="regForm" :model="form" :rules="rules" class="login-form ele-bg-white" size="large"
             @keyup.enter.native="doSubmit">
      <h4>忘记密码</h4>
      <el-form-item prop="phone">
        <el-input placeholder="请输入绑定手机号" v-model="form.phone" prefix-icon="el-icon-mobile-phone" clearable/>
      </el-form-item>
      <el-form-item prop="password">
        <el-input placeholder="请输入新的登录密码" v-model="form.password" prefix-icon="el-icon-lock" show-password/>
      </el-form-item>
      <el-form-item prop="password2">
        <el-input placeholder="请再次输入登录密码" v-model="form.password2" prefix-icon="el-icon-key" show-password/>
      </el-form-item>
      <el-form-item prop="code">
        <div class="login-input-group">
          <el-input placeholder="请输入验证码" v-model="form.code" prefix-icon="el-icon-_vercode"/>
          <div @click="showImgCodeCheck" class="login-captcha">发送验证码</div>
        </div>
      </el-form-item>
      <div class="el-form-item">
        <el-link @click="$router.push('/login')" type="primary" class="ele-pull-right" :underline="false">返回登录</el-link>
      </div>
      <div class="el-form-item">
        <el-button @click="doSubmit" :loading="loading" type="primary" class="login-btn" size="large">修改密码</el-button>
      </div>
    </el-form>
    <div class="login-copyright">copyright © 2017~2021 rxthink.cn all rights reserved.</div>
    <!-- 图形验证码弹窗 -->
    <el-dialog title="发送验证码" :visible.sync="showImgCode" width="320px">
      <div class="login-input-group" style="margin-bottom: 15px;">
        <el-input placeholder="输入图形验证码" v-model="imgCode" size="large" @keyup.enter.native="sendCode"/>
        <img :src="captcha" @click="changeImgCode" class="login-captcha" alt=""/>
      </div>
      <el-button @click="sendCode" :loading="codeLoading" type="primary" class="login-btn" size="large">立即发送</el-button>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: "Forget",
  data() {
    let password2Validator = (rule, value, callback) => {
      if (!value) return callback(new Error('请再次输入登录密码'));
      if (value !== this.form.password) return callback(new Error('两次密码输入不一致'));
      callback();
    };
    return {
      loading: false,  // 保存按钮loading
      form: {phone: '1234567890', password: '', password2: '', code: ''},  // 表单数据
      rules: {  // 表单验证规则
        phone: [
          {required: true, message: '请输入绑定手机号', trigger: 'blur'}
        ],
        password: [
          {required: true, message: '请输入新的登录密码', trigger: 'blur'}
        ],
        password2: [
          {validator: password2Validator, trigger: 'blur'}
        ],
        code: [
          {required: true, message: '请输入验证码', trigger: 'blur'}
        ]
      },
      v: new Date().getTime(),
      showImgCode: false,  // 是否显示图形验证码弹窗
      imgCode: '',  // 图形验证码
      codeLoading: false  // 发送验证码按钮loading
    }
  },
  computed: {
    /* 图形验证码地址 */
    captcha() {
      return 'http://shiro.easyweb.vip/assets/captcha?v=' + this.v
    }
  },
  methods: {
    /* 提交 */
    doSubmit() {
      this.$refs['regForm'].validate((valid) => {
        if (!valid) return false;
        this.loading = true;
        setTimeout(() => {
          this.$message({type: 'success', message: '密码修改成功'});
          this.$router.push('/login');
        }, 1000);
      });
    },
    /* 更换图形验证码 */
    changeImgCode() {
      this.v = new Date().getTime();
    },
    /* 显示发送短信验证码弹窗 */
    showImgCodeCheck() {
      if (!this.form.phone) return this.$message.error('请输入手机号码');
      this.imgCode = '';
      this.changeImgCode();
      this.showImgCode = true;
    },
    /* 发送短信验证码 */
    sendCode() {
      if (!this.imgCode) return this.$message.error('请输入图形验证码');
      this.codeLoading = true;
      setTimeout(() => {
        this.$message({type: 'success', message: '短信验证码发送成功, 请注意查收!'});
        this.showImgCode = false;
        this.codeLoading = false;
      }, 1000);
    }
  }
}
</script>

<style scoped>
.login-wrapper {
  padding: 50px 20px;
  position: relative;
  box-sizing: border-box;
  background-image: url("~@/assets/bg-login.png");
  background-repeat: no-repeat;
  background-size: cover;
  min-height: 100vh;
}

.login-wrapper:before {
  content: "";
  background-color: rgba(0, 0, 0, .2);
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.login-form {
  margin: 0 auto;
  width: 360px;
  max-width: 100%;
  padding: 25px 30px;
  position: relative;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
  box-sizing: border-box;
  border-radius: 4px;
  z-index: 2;
}

.login-form h4 {
  text-align: center;
  margin: 0 0 25px 0;
}

.login-form > .el-form-item {
  margin-bottom: 25px;
}

.login-input-group {
  display: flex;
  align-items: center;
}

.login-input-group >>> .el-input {
  flex: 1;
}

.login-captcha {
  height: 38px;
  width: 102px;
  margin-left: 10px;
  border-radius: 4px;
  border: 1px solid #DCDFE6;
  text-align: center;
  cursor: pointer;
}

.login-captcha:hover {
  opacity: .75;
}

.login-btn {
  display: block;
  width: 100%;
}

.login-oauth-icon {
  color: #FFF;
  padding: 5px;
  margin: 0 10px;
  font-size: 20px;
  border-radius: 50%;
  cursor: pointer;
}

.login-copyright {
  color: #eee;
  padding-top: 20px;
  text-align: center;
  position: relative;
  z-index: 1;
}

@media screen and (min-height: 550px) {
  .login-form {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: -250px;
  }

  .login-copyright {
    position: absolute;
    bottom: 20px;
    right: 0;
    left: 0;
  }
}
</style>