<!-- 面包屑导航 -->
<template>
  <el-breadcrumb separator="/" class="ele-header-breadcrumb ele-scrollbar-hide">
    <el-breadcrumb-item v-for="(item,index) in levels" :key="index"
                        :to="(index===0&&levels.length>1&&item.path)?{path: item.path}:null">{{ item.meta.title }}
    </el-breadcrumb-item>
  </el-breadcrumb>
</template>

<script>
export default {
  name: "EleBreadcrumb",
  data() {
    return {
      levels: []  // 面包屑数据
    }
  },
  mounted() {
    this.routeChange();
  },
  methods: {
    /* 处理路由切换 */
    routeChange() {
      let current = this.$route, path = current.path, matched;
      if (this.$store.state.user.menus && this.$store.state.user.menus.length) matched = this.getBreadcrumb(path);
      if (!matched || !matched.length) matched = current.matched;  // 从路由中获取面包屑
      matched = matched.filter(item => item.meta && item.meta.title && item.meta.breadcrumb !== false);
      if (path !== '/' && !this.isHome()) matched.unshift({path: '/', meta: {title: '首页'}});
      this.levels = matched;
    },
    /* 从菜单中获取面包屑导航 */
    getBreadcrumb(path, data) {
      if (!data) data = this.$store.state.user.menus;
      for (let i = 0; i < data.length; i++) {
        if (data[i].path === path) {
          return [{path: path, meta: data[i].meta}];
        } else if (data[i].children && data[i].children.length) {
          let temp = this.getBreadcrumb(path, data[i].children);
          if (temp && temp.length) return [{path: data[i].path, meta: data[i].meta}].concat(temp);
        }
      }
      return [];
    },
    /* 判断是否是主页 */
    isHome() {
      let current = this.$route, matched = current.matched;
      return matched && matched.length && !matched[0].path && matched[0].redirect === current.path;
    }
  },
  watch: {
    /* 监听路由切换 */
    $route() {
      this.routeChange();
    }
  }
}
</script>
