<!-- 状态文字组件 -->
<template>
  <span class="ele-status-dot">
    <span :class="dotClass" :style="dotStyle"><span :class="dotColor"></span></span>
    <span v-if="text" class="ele-dot-text">{{ text }}</span>
  </span>
</template>

<script>
export default {
  name: "EleDot",
  props: {
    type: {
      type: String,
      default: ''
    },
    ripple: {
      type: Boolean,
      default: true
    },
    text: {
      type: String,
      default: ''
    },
    size: {
      type: String,
      default: '6px'
    }
  },
  computed: {
    dotColor() {
      return [
        'ele-bg-primary',
        {'ele-bg-success': this.type === 'success'},
        {'ele-bg-warning': this.type === 'warning'},
        {'ele-bg-danger': this.type === 'danger'},
        {'ele-bg-info': this.type === 'info'}
      ]
    },
    dotClass() {
      return [
        'ele-dot',
        {'ele-dot-ripple': this.ripple}
      ].concat(this.dotColor);
    },
    dotStyle() {
      return {width: this.size, height: this.size};
    }
  }
}
</script>

<style>
.ele-status-dot {
  line-height: 1;
  display: inline-block;
  box-sizing: border-box;
}

.ele-status-dot .ele-dot-text {
  margin-left: 8px;
  display: inline-block;
  vertical-align: middle;
}

.ele-dot {
  border-radius: 50%;
  display: inline-block;
  vertical-align: middle;
  position: relative;
}

.ele-dot > span {
  display: none;
}

.ele-dot.ele-dot-ripple > span {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: block;
  border-radius: 50%;
  box-sizing: border-box;
  animation: eleAnimStatusDot 1.2s ease-in-out infinite;
}

@keyframes eleAnimStatusDot {
  from {
    transform: scale(.8);
    opacity: .6;
  }
  to {
    transform: scale(2.4);
    opacity: 0;
  }
}
</style>