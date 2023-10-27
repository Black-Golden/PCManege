<!-- 顶栏导航部分 -->
<template>
  <el-menu :default-active="active" mode="horizontal" @select="onMenuSelect" @mousewheel.native="onMousewheel"
           :class="['ele-header-nav ele-scrollbar-hide',{'ele-menu-dark':theme.headStyle!==0}]">
    <template v-for="(menu,index) in menus">
      <ele-sidebar-item v-if="!menu.meta||!menu.meta.hide" :item="menu"
                        :path="menu.path||index" :key="menu.path||index"/>
    </template>
  </el-menu>
</template>

<script>
import {mapGetters} from 'vuex'
import EleSidebarItem from './SidebarItem'
import NProgress from "nprogress"

export default {
  name: "EleHeaderNav",
  components: {EleSidebarItem},
  data() {
    return {
      scroll: false,  // 用于鼠标滚轮事件节流
      activeNo: false  // 用于触发active更新
    }
  },
  computed: {
    /* 菜单数据 */
    menus() {
      if (this.theme.layoutStyle === 0 || this.theme.screenWidth < 768) return [];  // 小屏幕强制左侧菜单模式
      let useMenu = this.user.menus && this.user.menus.length;
      let menus = useMenu ? this.user.menus : this.$router.options.routes.filter(d => !d.meta || !d.meta.hide);
      if (this.theme.layoutStyle === 1) return menus;
      else if (this.theme.layoutStyle === 2) return menus.map(d => Object.assign({}, d, {
        /*path: null, */children: null
      }));
      else return [];
    },
    /* 当前选中项 */
    active() {
      if (this.activeNo) return '';
      if (this.theme.layoutStyle === 1) return this.$route.path;
      return String(this.theme.navActive);
    },
    ...mapGetters(['theme', 'user'])
  },
  mounted() {
    if (this.theme.layoutStyle === 2) this.onMenuSelect(0);
  },
  methods: {
    /* 菜单点击事件 */
    onMenuSelect(index) {
      if (isNaN(index)) {
        if (!index) return;
        if (index.startsWith('http://') || index.startsWith('https://') || index.startsWith('//')) {
          window.open(index);
          this.activeNo = true;
          this.$nextTick(() => {
            this.activeNo = false;
          });
        } else if (this.$route.fullPath === index) {
          this.refresh();
        } else {
          this.$router.push(index);
        }
      } else if (this.theme.layoutStyle === 2) {
        this.$store.dispatch('theme/setNavActive', index);
      }
    },
    /* 刷新 */
    refresh() {
      NProgress.start();
      if (this.theme.reload) return;
      this.$store.dispatch('theme/toggle', 'reload');
      this.$nextTick(() => {
        this.$store.dispatch('theme/toggle', 'reload');
        setTimeout(() => {
          NProgress.done();
        }, 150);
      });
    },
    /* 鼠标滚轮事件 */
    onMousewheel(e) {
      let elem = e.currentTarget;
      if (!this.scroll) {  // 加标识节流
        this.scroll = true;
        let delta = e.wheelDelta || e.detail;
        if (delta > 0) elem.scrollLeft -= elem.clientWidth;
        else if (delta < 0) elem.scrollLeft += elem.clientWidth;
        setTimeout(() => {
          this.scroll = false;
        }, 300);
      }
      e.stopPropagation();
      e.preventDefault();
    }
  }
}
</script>
