<!-- 菜单item(递归渲染子级) -->
<template>
  <el-menu-item v-if="!item.children||!item.children.length" :index="String(path)">
    <i :class="(item.meta&&item.meta.icon)||''"></i>
    <span slot="title">{{ (item.meta && item.meta.title) || '' }}</span>
  </el-menu-item>
  <el-submenu v-else :index="String(path)" :popper-class="popClass" :popper-append-to-body="false">
    <template slot="title">
      <i :class="(item.meta&&item.meta.icon)||''"></i>
      <span slot="title">{{ (item.meta && item.meta.title) || '' }}</span>
    </template>
    <template v-for="(child,index) in item.children">
      <ele-sidebar-item v-if="!child.meta||!child.meta.hide" :item="child" :path="getPath(child.path)||index"
                        :key="path+(child.path||index)" :pop-class="popClass"/>
    </template>
  </el-submenu>
</template>

<script>
export default {
  name: "EleSidebarItem",
  props: {
    item: {
      type: Object,
      required: true
    },
    path: [String, Number],
    popClass: String
  },
  methods: {
    /* 获取路径，嵌套路由前面加父路径 */
    getPath(url) {
      if (url && (url.indexOf('/') === 0 || url.indexOf('http') === 0)) return url;
      if (this.path && typeof this.path === 'string') {
        if (this.path.lastIndexOf('/') === (this.path.length - 1)) return this.path + (url || '');
        else return this.path + '/' + (url || '');
      }
      return url || '';
    }
  }
}
</script>
