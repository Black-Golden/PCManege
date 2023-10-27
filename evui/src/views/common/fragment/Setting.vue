<!-- 主题设置界面 -->
<template>
  <el-drawer title="主题设置" :visible.sync="showSetting" size="300px" :append-to-body="true">
    <div class="ele-setting-wrapper">
      <!-- 侧栏风格设置 -->
      <div class="ele-setting-theme">
        <el-tooltip v-for="(item,index) in sideStyles" :key="index" :content="item.name" placement="top">
          <div :class="[item.style,{'active':$store.state.theme.sideStyle===item.value}]"
               @click="setSideStyle(item.value)"></div>
        </el-tooltip>
      </div>
      <!-- 顶栏风格设置 -->
      <div class="ele-setting-theme">
        <el-tooltip v-for="(item,index) in headStyles" :key="index" :content="item.name" placement="top">
          <div :class="[item.style||'ele-head-light',{'active':$store.state.theme.headStyle===item.value}]"
               @click="setHeadStyle(item.value)"></div>
        </el-tooltip>
      </div>
      <!-- 主题颜色设置 -->
      <div class="ele-setting-colors">
        <el-tooltip v-for="(item,index) in themeColors" :key="index" :content="item.name" placement="top">
          <div :class="['ele-setting-color-item',{'active':$store.state.theme.theme===item.value}]"
               @click="setTheme(item.value)" :style="{'background-color': item.color}"></div>
        </el-tooltip>
      </div>
      <el-divider/>
      <!-- 导航模式设置 -->
      <div class="ele-setting-title ele-text-secondary">导航模式</div>
      <div class="ele-setting-theme">
        <el-tooltip content="左侧菜单布局" placement="top">
          <div :class="['ele-side-dark',{'active':$store.state.theme.layoutStyle===0}]" @click="setLayout(0)"></div>
        </el-tooltip>
        <el-tooltip content="顶部菜单布局" placement="top">
          <div :class="['ele-head-dark',{'active':$store.state.theme.layoutStyle===1}]" @click="setLayout(1)"></div>
        </el-tooltip>
        <el-tooltip content="混合菜单布局" placement="top">
          <div :class="['ele-layout-mix',{'active':$store.state.theme.layoutStyle===2}]" @click="setLayout(2)"></div>
        </el-tooltip>
      </div>
      <div class="ele-setting-item">
        <div class="setting-item-title">内容区域铺满</div>
        <div class="setting-item-control">
          <el-switch v-model="bodyFull"/>
        </div>
      </div>
      <!-- 其它配置 -->
      <el-divider/>
      <div class="ele-setting-title ele-text-secondary">其它配置</div>
      <div class="ele-setting-item">
        <div class="setting-item-title">固定顶栏侧栏</div>
        <div class="setting-item-control">
          <el-switch v-model="fixedLayout"/>
        </div>
      </div>
      <div class="ele-setting-item">
        <div class="setting-item-title">Logo宽度自动</div>
        <div class="setting-item-control">
          <el-switch v-model="logoAutoSize"/>
        </div>
      </div>
      <div class="ele-setting-item">
        <div class="setting-item-title">侧栏彩色图标</div>
        <div class="setting-item-control">
          <el-switch v-model="colorfulIcon"/>
        </div>
      </div>
      <div class="ele-setting-item">
        <div class="setting-item-title">侧栏排他展开</div>
        <div class="setting-item-control">
          <el-switch v-model="sideUniqueOpen"/>
        </div>
      </div>
      <div class="ele-setting-item">
        <div class="setting-item-title">是否开启页签</div>
        <div class="setting-item-control">
          <el-switch v-model="showTabs"/>
        </div>
      </div>
      <div class="ele-setting-item">
        <div class="setting-item-title">页签显示风格</div>
        <div class="setting-item-control">
          <el-select v-model="tabStyle" placeholder="请选择" size="mini">
            <el-option v-for="(item,index) in tabStyles" :key="index" :label="item.name" :value="item.value"/>
          </el-select>
        </div>
      </div>
      <el-divider/>
      <el-alert type="warning" :closable="false" class="ele-alert-border"
                title="该功能可实时预览各种布局效果, 更多完整配置在 setting.js 中设置, 修改后会记住配置, 可用于生产环境中."/>
    </div>
  </el-drawer>
</template>

<script>
export default {
  name: "EleSetting",
  data() {
    return {
      /* 侧边栏风格 */
      sideStyles: [
        {name: '暗色侧边栏', value: 1, style: 'ele-side-dark'},
        {name: '亮色侧边栏', value: 0}
      ],
      /* 顶栏风格 */
      headStyles: [
        {name: '亮色顶栏', value: 0},
        {name: '暗色顶栏', value: 1, style: 'ele-head-dark'},
        {name: '主色顶栏', value: 2, style: 'ele-head-primary'}
      ],
      /* 标签页风格 */
      tabStyles: [
        {name: '默认', value: 0},
        {name: '圆点', value: 1},
        {name: '卡片', value: 2}
      ],
      /* 主题色 */
      themeColors: [
        {name: '拂晓蓝', value: null, color: '#409EFF'},
        {name: '薄暮', value: 'dust', color: '#5F80C7'},
        {name: '日暮', value: 'sunset', color: '#FAAD14'},
        {name: '火山', value: 'volcano', color: '#F5686F'},
        {name: '极客蓝', value: 'geekblue', color: '#32A2D4'},
        {name: '极光绿', value: 'green', color: '#33CC99'},
        {name: '明青', value: 'cyan', color: '#2BCCCE'},
        {name: '酱紫', value: 'purple', color: '#9266F9'},
        {name: '暗黑', value: 'dark', color: '#191919'}
      ]
    }
  },
  mounted() {
    if (this.$store.state.theme.theme) this.setTheme(this.$store.state.theme.theme, true);  // 恢复上次设置主题
  },
  methods: {
    /* 切换侧边栏风格 */
    setSideStyle(style) {
      this.$store.dispatch('theme/set', {key: 'sideStyle', value: style});
    },
    /* 切换顶栏风格 */
    setHeadStyle(style) {
      this.$store.dispatch('theme/set', {key: 'headStyle', value: style});
    },
    /* 切换主题色 */
    setTheme(theme, force) {
      if (!force && theme === this.$store.state.theme.color) return;
      const loading = this.$loading({lock: true, background: 'transparent'});
      this.$store.dispatch('theme/setTheme', theme).then(() => {
        loading.close();
      }).catch(e => {
        console.error(e);
        loading.close();
        this.$message.error('主题加载失败');
      });
    },
    /* 设置导航布局 */
    setLayout(style) {
      this.$store.dispatch('theme/set', {key: 'layoutStyle', value: style});
      if (style === 2) this.$store.dispatch('theme/setNavActive', 0);
      else this.$store.dispatch('theme/setNavActive');
    }
  },
  computed: {
    showSetting: {
      get() {
        return this.$store.state.theme.showSetting;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'showSetting', value: val});
      }
    },
    bodyFull: {
      get() {
        return this.$store.state.theme.bodyFull;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'bodyFull', value: val});
      }
    },
    fixedLayout: {
      get() {
        return this.$store.state.theme.fixedLayout;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'fixedLayout', value: val});
      }
    },
    logoAutoSize: {
      get() {
        return this.$store.state.theme.logoAutoSize;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'logoAutoSize', value: val});
      }
    },
    colorfulIcon: {
      get() {
        return this.$store.state.theme.colorfulIcon;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'colorfulIcon', value: val});
      }
    },
    sideUniqueOpen: {
      get() {
        return this.$store.state.theme.sideUniqueOpen;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'sideUniqueOpen', value: val});
      }
    },
    showTabs: {
      get() {
        return this.$store.state.theme.showTabs;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'showTabs', value: val});
      }
    },
    tabStyle: {
      get() {
        return this.$store.state.theme.tabStyle;
      },
      set(val) {
        this.$store.dispatch('theme/set', {key: 'tabStyle', value: val});
      }
    }
  }
}
</script>

<style lang="scss">
.ele-setting-wrapper {
  padding: 20px;

  .ele-setting-title {
    font-size: 13px;
    margin-bottom: 15px;
  }

  /* 主题风格 */
  .ele-setting-theme > div {
    width: 50px;
    height: 35px;
    border-radius: 3px;
    margin: 0 20px 30px 0;
    background-color: #F5F7FA;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
    display: inline-block;
    vertical-align: top;
    position: relative;
    cursor: pointer;

    &.active:after {
      content: "";
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background-color: #19be6b;
      position: absolute;
      left: 50%;
      bottom: -15px;
      margin-left: -3px;
    }

    &:before {
      content: "";
      width: 15px;
      height: 100%;
      background-color: #fff;
      border-top-left-radius: 3px;
      border-bottom-left-radius: 3px;
      display: inline-block;
      vertical-align: top;
    }

    &.ele-side-dark:before {
      background-color: #2E3549;
    }

    &.ele-head-light:before, &.ele-head-dark:before, &.ele-head-primary:before {
      width: 100%;
      height: 10px;
      background-color: #fff;
      border-bottom-left-radius: 0;
      border-top-right-radius: 3px;
    }

    &.ele-head-dark:before {
      background-color: #2E3549;
    }

    &.ele-head-primary:before {
      background-color: #409EFF;
    }

    &.ele-layout-mix {
      background-color: #2E3549;

      &:before {
        background-color: #f0f2f5;
        width: 35px;
        height: 25px;
        position: absolute;
        bottom: 0;
        right: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 3px;
      }
    }
  }

  /* 主题色选择 */
  .ele-setting-colors {
    margin-bottom: 20px;
  }

  .ele-setting-color-item {
    width: 20px;
    height: 20px;
    margin: 8px 8px 0 0;
    border-radius: 2px;
    display: inline-block;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
    vertical-align: top;
    position: relative;
    cursor: pointer;

    &.active:after {
      content: "\e6da";
      font-family: element-icons !important;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      position: absolute;
      top: 50%;
      left: 50%;
      margin: -7px 0 0 -7px;
      font-size: 14px;
      color: #FFF;
    }
  }

  /* 主题配置项 */
  .ele-setting-item {
    display: flex;
    margin-bottom: 20px;
    line-height: 28px;

    .setting-item-title {
      flex: 1;
    }

    .setting-item-control {
      max-width: 80px;
    }
  }

  .el-divider {
    margin-bottom: 20px;
  }
}
</style>