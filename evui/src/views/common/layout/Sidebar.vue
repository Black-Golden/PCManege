<!-- 侧边栏 -->
<template>
  <el-aside class="ele-admin-sidebar ele-scrollbar-hide">
    <el-menu :collapse="collapse" :default-active="active" @select="onMenuSelect" :collapse-transition="false"
             :class="{'ele-menu-dark':theme.sideStyle===1}" :unique-opened="theme.sideUniqueOpen">
      <template v-for="(menu,index) in menus">
        <ele-sidebar-item v-if="!menu.meta||!menu.meta.hide" :item="menu" :path="getPath(menu.path)||index"
                          :key="menu.path||index" :pop-class="theme.sideStyle===1?'ele-menu-dark':''"/>
      </template>
    </el-menu>
  </el-aside>
</template>

<script>
import {mapGetters} from 'vuex'
import EleSidebarItem from './SidebarItem'
import NProgress from 'nprogress'

export default {
  name: "EleSidebar",
  components: {EleSidebarItem},
  data() {
    return {
      activeNo: false  // 用于触发active更新
    };
  },
  computed: {
    /* 菜单数据 */
    menus() {
      let useMenu = this.user.menus && this.user.menus.length;
      let menus = useMenu ? this.user.menus : this.$router.options.routes.filter(d => !d.meta || !d.meta.hide);
      if (this.theme.layoutStyle === 2 && this.theme.screenWidth >= 768) {  // 小屏幕强制左侧菜单模式
        let data = menus[this.theme.navActive || 0];
        return data ? (data.children || []) : [];
      }
      return menus;
    },
    /* 是否折叠menu */
    collapse() {
      return this.theme.screenWidth < 768 ? false : this.theme.collapse;  // 小屏幕强制不折叠
    },
    /* 侧边栏选中 */
    active() {
      if (this.$route.meta && this.$route.meta.uid) return this.$route.meta.uid;
      if (this.activeNo) return '';
      return this.$route.path;
    },
    ...mapGetters(['theme', 'user'])
  },
  methods: {
    /* 菜单点击事件 */
    onMenuSelect(index) {
      if (!index) return;
      if (index.startsWith('http://') || index.startsWith('https://') || index.startsWith('//')) {
        window.open(index);
        this.activeNo = true;
        this.$nextTick(() => {
          this.activeNo = false;
        });
        return;
      }
      let path = this.$route.fullPath;
      let parent = this.$route.matched[this.$route.matched.length - 2];
      if ((parent && parent.redirect === path && parent.path === index) || path === index) this.refresh();
      else this.$router.push(index);
    },
    /* 刷新 */
    refresh() {
      if (this.theme.reload) return;
      NProgress.start();
      this.$store.dispatch('theme/toggle', 'reload');
      this.$nextTick(() => {
        this.$store.dispatch('theme/toggle', 'reload');
        setTimeout(() => {
          NProgress.done();
        }, 150);
      });
    },
    /* 获取路径, 多系统模式嵌套路由加父路径 */
    getPath(url) {
      if (!(this.theme.layoutStyle === 2 && this.theme.screenWidth >= 768)) return url || '';
      if (url && (url.indexOf('/') === 0 || url.indexOf('http') === 0)) return url;
      let useMenu = this.user.menus && this.user.menus.length;
      let menus = useMenu ? this.user.menus : this.$router.options.routes.filter(d => !d.meta || !d.meta.hide);
      let path = menus[this.theme.navActive || 0].path;
      if (path && typeof path === 'string') {
        if (path.lastIndexOf('/') === (path.length - 1)) return path + (url || '');
        else return path + '/' + (url || '');
      }
      return url || '';
    }
  }
}
</script>
