<!-- 标签页 -->
<template>
  <div class="ele-admin-tabs">
    <el-tabs v-model="active" @tab-click="tabChange" @tab-remove="tabRemove">
      <el-tab-pane name="/" :label="($setting&&$setting.homeTitle)||'主页'"/>
      <el-tab-pane v-for="(d,i) in tabs" :key="i+d.path" :name="d.path" :label="d.title" :closable="true"/>
    </el-tabs>
    <el-dropdown class="ele-tabs-drop" @command="onDropClick">
      <i class="el-icon-arrow-down"></i>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item command="other" icon="el-icon-close">关闭其他</el-dropdown-item>
        <el-dropdown-item command="right" icon="el-icon-right">关闭右侧</el-dropdown-item>
        <el-dropdown-item command="left" icon="el-icon-back">关闭左侧</el-dropdown-item>
        <el-dropdown-item command="all" icon="el-icon-circle-close">关闭全部</el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </div>
</template>

<script>
export default {
  name: "EleTabs",
  data() {
    return {
      active: null      // 当前选中页签
    }
  },
  computed: {
    tabs: {
      get() {
        return this.$store.state.theme.tabs;
      },
      set() {
      }
    }
  },
  mounted() {
    this.routeChange();
  },
  methods: {
    /* 处理路由切换 */
    routeChange() {
      let current = this.$route, path = current.fullPath, title = (current.meta && current.meta.title) || '';
      if (path === '/' || this.isHome()) {
        this.active = '/';
      } else {
        this.$store.dispatch('theme/tabAdd', {path: path, title: title});
        this.active = path;
      }
    },
    /* 切换tab */
    tabChange(tab) {
      if (tab.name === '/' && this.isHome()) return;
      if (this.$route.fullPath !== tab.name) this.$router.push(tab.name);
    },
    /* 删除tab */
    tabRemove(name) {
      this.$store.dispatch('theme/tabRemove', name).then(last => {
        if (this.active === name) this.$router.push(last === -1 ? '/' : this.tabs[last].path);
      });
    },
    /* 下拉菜单点击事件 */
    onDropClick(command) {
      if (command === 'other') {  // 关闭其他
        this.$store.dispatch('theme/tabRemoveOther', this.active);
      } else if (command === 'right') {  // 关闭右侧
        if (this.active === '/') this.$store.dispatch('theme/tabRemoveAll');
        else this.$store.dispatch('theme/tabRemoveRight', this.active);
      } else if (command === 'left') {  // 关闭左侧
        this.$store.dispatch('theme/tabRemoveLeft', this.active);
      } else if (command === 'all') {  // 关闭全部
        this.$store.dispatch('theme/tabRemoveAll');
        if (this.active !== '/') this.$router.push('/');
      }
    },
    /* 判断是否是主页 */
    isHome() {
      let current = this.$route, matched = current.matched;
      return matched && matched.length && !matched[0].path && matched[0].redirect === current.path;
    }
  },
  watch: {
    $route() {
      this.routeChange();
    }
  }
}
</script>
