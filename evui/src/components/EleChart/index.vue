<!-- echarts图表组件封装 -->
<template>
  <div ref="chart"></div>
</template>

<script>
import echarts from 'echarts'
import EleCharTheme from './EleChartTheme'

export default {
  name: 'EleChart',
  props: {
    option: Object  // echarts配置
  },
  data() {
    return {
      chart: null  // echarts实例
    }
  },
  computed: {
    screenWidth() {
      try {
        return this.$store.state.theme.screenWidth;
      } catch (e) {
        return null;
      }
    },
    collapse() {
      try {
        return this.$store.state.theme.collapse;
      } catch (e) {
        return null;
      }
    }
  },
  watch: {
    option() {
      this.setOption();
    },
    screenWidth() {
      this.resize();
    },
    collapse() {
      setTimeout(() => {
        this.resize();
      }, 360);
    }
  },
  mounted() {
    this.init();
  },
  methods: {
    /* 初始化echarts实例 */
    init() {
      this.chart = echarts.init(this.$refs.chart, EleCharTheme);
      this.setOption();
      this.$emit('done', this.chart);
    },
    /* 绘制图表 */
    setOption(option) {
      if (!this.chart) return;
      this.chart.setOption(option || this.option);
    },
    /* 重置尺寸 */
    resize() {
      if (!this.chart) return;
      this.chart.resize();
    }
  }
}
</script>