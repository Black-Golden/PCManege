<template>
  <div class="ele-body">
    <el-row :gutter="15">
      <el-col :md="6" :sm="8">
        <!-- <el-card shadow="never" body-style="padding: 25px;">
          <div class="user-info-card">
            <div class="user-info-avatar-group" @click="showCropper = true">
              <img class="user-info-avatar" :src="form.avatar" alt="" />
              <i class="el-icon-upload2"></i>
            </div>
            <h2 class="user-info-name">相约在冬季</h2>
            <div class="user-info-desc">为梦想而创作，海纳百川，有容乃大!</div>
          </div>
          <div class="user-info-list">
            <div class="user-info-item">
              <i class="el-icon-user"></i><span>资深PHP架构师</span>
            </div>
            <div class="user-info-item">
              <i class="el-icon-office-building"></i
              ><span>RXThinkCMF研发中心 - 架构设计部 - 资深架构师</span>
            </div>
            <div class="user-info-item">
              <i class="el-icon-location-information"></i
              ><span>中国 • 江苏省 • 南京市</span>
            </div>
            <div class="user-info-item">
              <i class="el-icon-_school"></i
              ><span>ThinkPhp6、JWT、Vue、ElementUI</span>
            </div>
          </div>
          <div style="margin: 30px 0 20px 0;">
            <el-divider class="ele-divider-dashed ele-divider-base" />
          </div>
          <h6 class="ele-text" style="margin-bottom: 8px;">标签</h6>
          <div class="user-info-tags">
            <el-tag size="mini" type="info">中国硅谷</el-tag>
            <el-tag size="mini" type="info">创业者</el-tag>
            <el-tag size="mini" type="info">厚德载物</el-tag>
            <el-tag size="mini" type="info">坚持</el-tag>
            <el-tag size="mini" type="info">毅力</el-tag>
            <el-tag size="mini" type="info">王者归来</el-tag>
          </div>
        </el-card> -->
      </el-col>
      <el-col>
        <el-card shadow="never" body-style="padding-top: 5px;">
          <el-tabs v-model="active" class="user-info-tabs">
            <el-tab-pane label="基本信息" name="info">
              <el-form
                ref="infoForm"
                :model="form"
                :rules="rules"
                label-width="90px"
                style="max-width: 450px;padding-top: 40px;"
                @keyup.enter.native="save"
                @submit.native.prevent
              >
                <el-form-item label="真实姓名:" prop="realname">
                  <el-input
                    v-model="form.realname"
                    placeholder="请输入真实姓名"
                    clearable
                  />
                </el-form-item>
                <el-form-item label="昵称:" prop="nickname">
                  <el-input
                    v-model="form.nickname"
                    placeholder="请输入昵称"
                    clearable
                  />
                </el-form-item>
                <el-form-item label="性别:" prop="gender">
                  <el-select
                    v-model="form.gender"
                    placeholder="请选择性别"
                    class="ele-fluid"
                    clearable
                  >
                    <el-option label="男" :value="1" />
                    <el-option label="女" :value="2" />
                    <el-option label="保密" :value="3" />
                  </el-select>
                </el-form-item>
                <el-form-item label="联系方式:" prop="mobile">
                  <el-input
                    v-model="form.mobile"
                    placeholder="请输入联系方式"
                    clearable
                  />
                </el-form-item>
                <el-form-item label="电子邮箱:" prop="email">
                  <el-input
                    v-model="form.email"
                    placeholder="请输入电子邮箱"
                    clearable
                  />
                </el-form-item>
                <el-form-item label="个人简介:">
                  <el-input
                    v-model="form.intro"
                    placeholder="请输入个人简介"
                    :rows="4"
                    type="textarea"
                  />
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="save" :loading="loading"
                    >保存更改</el-button
                  >
                </el-form-item>
              </el-form>
            </el-tab-pane>
            <!-- <el-tab-pane label="账号绑定" name="account">
              <div class="user-account-list">
                <div class="user-account-item ele-cell">
                  <div class="ele-cell-content">
                    <div>密保手机</div>
                    <div class="ele-text-secondary">
                      已绑定手机：152****4151
                    </div>
                  </div>
                  <el-link type="primary" :underline="false">去修改</el-link>
                </div>
                <el-divider />
                <div class="user-account-item ele-cell">
                  <div class="ele-cell-content">
                    <div>密保邮箱</div>
                    <div class="ele-text-secondary">
                      已绑定邮箱：1175401194@qq.com
                    </div>
                  </div>
                  <el-link type="primary" :underline="false">去修改</el-link>
                </div>
                <el-divider />
                <div class="user-account-item ele-cell">
                  <div class="ele-cell-content">
                    <div>密保问题</div>
                    <div class="ele-text-secondary">未设置密保问题</div>
                  </div>
                  <el-link type="primary" :underline="false">去设置</el-link>
                </div>
                <el-divider />
                <div class="user-account-item ele-cell">
                  <i class="user-account-icon el-icon-_qq"></i>
                  <div class="ele-cell-content">
                    <div>绑定QQ</div>
                    <div class="ele-text-secondary">当前未绑定QQ账号</div>
                  </div>
                  <el-link type="primary" :underline="false">去绑定</el-link>
                </div>
                <el-divider />
                <div class="user-account-item ele-cell">
                  <i class="user-account-icon el-icon-_wechat"></i>
                  <div class="ele-cell-content">
                    <div>绑定微信</div>
                    <div class="ele-text-secondary">当前未绑定绑定微信账号</div>
                  </div>
                  <el-link type="primary" :underline="false">去绑定</el-link>
                </div>
                <el-divider />
                <div class="user-account-item ele-cell">
                  <i class="user-account-icon el-icon-_alipay"></i>
                  <div class="ele-cell-content">
                    <div>绑定支付宝</div>
                    <div class="ele-text-secondary">
                      当前未绑定绑定支付宝账号
                    </div>
                  </div>
                  <el-link type="primary" :underline="false">去绑定</el-link>
                </div>
              </div>
            </el-tab-pane> -->
          </el-tabs>
        </el-card>
      </el-col>
    </el-row>
    <!-- 头像裁剪弹窗 -->
    <ele-cropper-dialog
      :show.sync="showCropper"
      @crop="onCrop"
      :src="form.avatar"
      :lock-scroll="false"
    />
  </div>
</template>

<script>
import EleCropperDialog from '@/components/EleCropperDialog' // 图片裁剪

export default {
  name: 'UserInfo',
  components: { EleCropperDialog },
  data() {
    return {
      active: 'info', // tab页选中
      form: {}, // 表单数据
      rules: {
        // 表单验证规则
        realname: [
          { required: true, message: '请输入真实姓名', trigger: 'blur' },
        ],
        nickname: [{ required: true, message: '请输入昵称', trigger: 'blur' }],
        gender: [{ required: true, message: '请选择性别', trigger: 'blur' }],
        mobile: [
          { required: true, message: '请输入联系方式', trigger: 'blur' },
        ],
        email: [{ required: true, message: '请输入电子邮箱', trigger: 'blur' }],
      },
      loading: false, // 保存按钮loading
      showCropper: false, // 是否显示裁剪弹窗
    }
  },
  mounted() {
    this.getUserInfo()
  },
  methods: {
    /**
     * 获取用户信息
     */
    getUserInfo() {
      if (this.$setting.userUrl) {
        this.$http
          .get(this.$setting.userUrl)
          .then((res) => {
            if (res.data.code === 0) {
              if (this.$setting.parseUser) {
                res.data = this.$setting.parseUser(res.data)
              }
              this.form = JSON.parse(JSON.stringify(res.data.data))
              let user = res.data.data
              this.$store.dispatch('user/setUser', user)
              this.$store.dispatch('user/setRoles', user ? user.roles : null)
              this.$store.dispatch(
                'user/setAuthorities',
                user ? user.authorities : null
              )
            } else {
              this.$message.error(res.data.msg)
            }
          })
          .catch((e) => {
            this.$message.error(e.message)
          })
      }
    },
    /* 保存更改 */
    save() {
      this.$refs['infoForm'].validate((valid) => {
        if (valid) {
          this.loading = true
          this.$http
            .post('/index/updateUserInfo', this.form)
            .then((res) => {
              this.loading = false
              if (res.data.code === 0) {
                this.$message({ type: 'success', message: '保存成功' })
              } else {
                this.$message.error(res.data.msg)
              }
            })
            .catch((e) => {
              this.loading = false
              this.$message.error(e.message)
            })
        } else {
          return false
        }
      })
    },
    /* 头像裁剪完成回调 */
    onCrop(res) {
      this.form.avatar = res
      this.showCropper = false
    },
  },
}
</script>

<style scoped>
.ele-body {
  padding-bottom: 0;
}

.el-card {
  margin-bottom: 15px;
}

/* 用户资料卡片 */
.user-info-card {
  padding-top: 8px;
  text-align: center;
}

.user-info-card .user-info-avatar-group {
  position: relative;
  cursor: pointer;
  margin: 0 auto;
  width: 110px;
  height: 110px;
  border-radius: 50%;
  overflow: hidden;
}

.user-info-card .user-info-avatar {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  object-fit: cover;
}

.user-info-card .user-info-avatar-group > i {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #fff;
  font-size: 30px;
  display: none;
  z-index: 2;
}

.user-info-card .user-info-avatar-group:hover > i {
  display: block;
}

.user-info-card .user-info-avatar-group:hover:after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.3);
}

.user-info-card .user-info-name {
  font-size: 24px;
  margin-top: 20px;
}

.user-info-card .user-info-desc {
  margin-top: 8px;
}

/* 用户信息列表 */
.user-info-list {
  margin-top: 30px;
}

.user-info-list .user-info-item {
  margin-bottom: 10px;
  display: flex;
  align-items: baseline;
}

.user-info-item > i {
  margin-right: 10px;
  font-size: 16px;
}

.user-info-item > span {
  flex: 1;
  display: block;
}

/* 用户标签 */
.user-info-tags .el-tag {
  margin: 10px 10px 0 0;
}

/* 用户账号绑定列表 */
.user-account-list {
  margin-top: 10px;
}

.user-account-list .user-account-item {
  padding: 15px;
}

.user-account-list .user-account-item .ele-text-secondary {
  margin-top: 6px;
}

.user-account-list .user-account-item .user-account-icon {
  width: 42px;
  height: 42px;
  line-height: 42px;
  text-align: center;
  color: #fff;
  font-size: 26px;
  border-radius: 50%;
  background-color: #3492ed;
  box-sizing: border-box;
}

.user-account-list .user-account-item .user-account-icon.el-icon-_wechat {
  background-color: #4daf29;
  font-size: 28px;
}

.user-account-list .user-account-item .user-account-icon.el-icon-_alipay {
  background-color: #1476fe;
  padding-left: 5px;
  font-size: 32px;
}

/* tab页签 */
.user-info-tabs >>> .el-tabs__nav-wrap {
  padding-left: 20px;
}
</style>
