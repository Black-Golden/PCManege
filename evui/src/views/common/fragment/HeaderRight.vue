<!-- 顶栏右侧部分 -->
<template>
  <el-menu
    mode="horizontal"
    :class="[
      'ele-header-right',
      { 'ele-menu-dark': $store.state.theme.headStyle !== 0 },
    ]"
  >
    <el-menu-item index="full" @click="fullScreen">
      <i
        :class="
          isFullScreen ? 'el-icon-_screen-restore' : 'el-icon-_screen-full'
        "
      ></i>
    </el-menu-item>
    <!--    <el-menu-item index="notice">-->
    <!--      <ele-notice/>&lt;!&ndash; 消息通知组件 &ndash;&gt;-->
    <!--    </el-menu-item>-->
    <el-submenu index="user" popper-class="ele-menu-pop">
      <template slot="title">
        <el-avatar :src="user.avatar" />
        <span>{{ user.realname || '' }}</span>
      </template>
      <!-- <el-menu-item index="info" @click="goUserInfo"><i class="el-icon-user"></i><span>个人中心</span></el-menu-item> -->
      <el-menu-item index="password" @click="showPassword"
        ><i class="el-icon-key"></i><span>修改密码</span></el-menu-item
      >
      <el-divider />
      <el-menu-item index="logout" @click="logout"
        ><i class="el-icon-switch-button"></i
        ><span>退出登录</span></el-menu-item
      >
    </el-submenu>
    <el-menu-item index="more" v-if="$setting.showSetting" @click="showSetting">
      <i class="el-icon-_more"></i>
    </el-menu-item>
  </el-menu>
</template>

<script>
import EleNotice from './Notice'

export default {
  name: 'EleHeaderRight',
  components: { EleNotice },
  data() {
    return {
      isFullScreen: false, // 是否全屏
    }
  },
  computed: {
    user() {
      // 当前登录用户信息
      return this.$store.state.user.user || {}
    },
  },
  methods: {
    /* 退出登录 */
    logout() {
      this.$confirm('确定要退出登录吗?', '提示', { type: 'warning' })
        .then(() => {
          this.$http.get('/login/logout').then((res) => {
            if (res.data.code === 0) {
              this.$message({ type: 'success', message: '注销成功' })
              // 清除缓存的token
              this.$store.dispatch('user/setToken').then(() => {
                // this.$router.push({path: "/login"});
                window.location.replace('/')
              })
            } else {
              this.$message.error(res.data.msg)
            }
          })
        })
        .catch(() => 0)
    },
    /* 跳转到个人中心 */
    // goUserInfo() {
    //   if (this.$route.fullPath !== '/user/info') this.$router.push('/user/info');
    // },
    /* 打开修改密码弹窗 */
    showPassword() {
      this.$store.dispatch('theme/toggle', 'showPassword')
    },
    /* 打开主题设置 */
    showSetting() {
      this.$store.dispatch('theme/toggle', 'showSetting')
    },
    /* 全屏 */
    fullScreen() {
      this.isFullScreen = !this.$util.fullScreen()
      if (!this.$setting.watchResize)
        this.$store.dispatch('theme/set', {
          key: 'screenWidth',
          value:
            document.documentElement.clientWidth || document.body.clientWidth,
        })
    },
  },
}
</script>
