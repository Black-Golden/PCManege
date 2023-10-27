<!-- 页面布局 -->
<template>
  <div :class="layoutClass">
    <ele-header/><!-- 顶栏 -->
    <el-container class="ele-admin-main">
      <ele-sidebar v-if="showSidebar"/><!-- 侧边栏 -->
      <div class="ele-admin-body">
        <ele-tabs v-if="theme.showTabs"/><!-- 多页签 -->
        <keep-alive :include="$setting.keepAliveList">
          <router-view v-if="!theme.reload"/><!-- 路由出口 -->
        </keep-alive>
      </div>
    </el-container>
    <div class="ele-admin-shade" @click="collapse"></div><!-- 小屏幕下遮罩层 -->
    <ele-setting v-if="$setting.showSetting"/><!-- 主题设置抽屉 -->
    <ele-password/><!-- 修改密码弹窗 -->
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import EleHeader from './Header'
import EleSidebar from './Sidebar'
import EleTabs from './Tabs'
import EleSetting from '../fragment/Setting'
import ElePassword from '../fragment/Password'

export default {
  name: "EleLayout",
  components: {EleHeader, EleSidebar, EleTabs, EleSetting, ElePassword},
  computed: {
    /* 框架布局class */
    layoutClass() {
      return [
        'ele-admin-layout',
        {'ele-layout-collapse': this.theme.collapse},  // 折叠状态
        {'ele-layout-fixed': this.theme.fixedLayout},  // 固定顶栏和侧栏
        {'ele-side-dark': this.theme.sideStyle === 1},  // 暗色侧栏
        {'ele-side-color-icon': this.theme.colorfulIcon},  // 侧栏彩色图标
        {'ele-head-dark': this.theme.headStyle !== 0},  // 暗色顶栏
        {'ele-logo-auto-size': this.theme.logoAutoSize || this.theme.layoutStyle === 1},  // logo宽度自适应
        {'ele-tabs-show': this.theme.showTabs},  // 开启多页签
        ['', 'ele-tab-dot', 'ele-tab-card'][this.theme.tabStyle],  // 页签风格
        {'ele-admin-body-limit': !this.theme.bodyFull}  // 主体内容定宽
      ]
    },
    /* 是否显示侧边栏 */
    showSidebar() {
      return this.theme.layoutStyle !== 1 || this.theme.screenWidth < 768;  // 小屏幕强制左侧菜单模式
    },
    ...mapGetters(['theme']),
  },
  mounted() {
    this.getUserInfo();
    if (this.$setting.watchResize) {
      window.addEventListener('resize', this.onWindowResize);
    }
  },
  methods: {
    /* 获取当前用户信息 */
    getUserInfo() {
      if (this.$setting.userUrl) {
        this.$http.get(this.$setting.userUrl).then(res => {
          if (res.data.code === 0) {
            if (this.$setting.parseUser) {
              res.data = this.$setting.parseUser(res.data);
            }
            let user = res.data.data;
            this.$store.dispatch('user/setUser', user);
            this.$store.dispatch('user/setRoles', user ? user.roles : null);
            this.$store.dispatch('user/setAuthorities', user ? user.authorities : null);
            // 设置节点权限
            this.$store.dispatch('user/setPermission', user ? user.permissionList : null);
          } else {
            this.$message.error(res.data.msg);
          }
        }).catch(e => {
          this.$message.error(e.message);
        });
      }
    },
    /* 折叠/展开侧边栏 */
    collapse() {
      this.$store.dispatch('theme/toggle', 'collapse');
    },
    /* 窗口大小改变监听 */
    onWindowResize() {
      let screenWidth = document.documentElement.clientWidth || document.body.clientWidth;
      if (this.$store.state.theme.screenWidth !== screenWidth) {
        this.$store.dispatch('theme/set', {
          key: 'screenWidth', value: screenWidth
        });
      }
    }
  },
  destroyed() {
    // 销毁屏幕resize监听
    if (this.$setting.watchResize) {
      window.removeEventListener('resize', this.onWindowResize);
    }
  },
  watch: {
    /* 监听路由变化 */
    $route() {
      if (this.theme.screenWidth < 768) {
        this.$store.dispatch('theme/set', {key: 'collapse', value: true});
      }
      if (!this.$setting.watchResize) {
        this.onWindowResize();
      }
    }
  }
}
</script>
