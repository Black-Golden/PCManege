<!-- 头像列表组件 -->
<template>
  <div class="ele-avatar-list" v-if="data">
    <template v-for="(item,index) in data">
      <el-tooltip v-if="!max||index<max" :key="index" :content="item[name]" :placement="placement"
                  :effect="effect" :disabled="!tooltip" :offset="offset" :transition="transition">
        <img :src="item[avatar]" class="ele-avatar-list-img" :style="[commonStyle,itemStyle,avatarStyle]" alt=""/>
      </el-tooltip>
    </template>
    <div v-if="max&&data.length>max" class="ele-avatar-list-more" :style="[commonStyle,itemStyle,moreStyle]">
      +{{ data.length - max }}
    </div>
  </div>
</template>

<script>
export default {
  name: "EleAvatarList",
  props: {
    data: {
      type: Array
    },
    max: {
      type: Number,
      default: 0
    },
    name: {
      type: String,
      default: 'name'
    },
    avatar: {
      type: String,
      default: 'avatar'
    },
    size: {
      type: String,
      default: '30px'
    },
    avatarStyle: [String, Object],
    itemStyle: [String, Object],
    moreStyle: [String, Object],
    tooltip: {
      type: Boolean,
      default: true
    },
    placement: {
      type: String,
      default: 'top'
    },
    effect: {
      type: String,
      default: 'dark'
    },
    offset: {
      type: Number,
      default: 0
    },
    transition: {
      type: String,
      default: 'el-fade-in-linear'
    }
  },
  computed: {
    commonStyle() {
      return {width: this.size, height: this.size, lineHeight: this.size};
    }
  }
}
</script>

<style>
.ele-avatar-list {
  display: inline-block;
}

.ele-avatar-list .ele-avatar-list-img {
  cursor: pointer;
}

.ele-avatar-list .ele-avatar-list-more, .ele-avatar-list .ele-avatar-list-img {
  text-align: center;
  margin-left: -12px;
  display: inline-block;
  vertical-align: middle;
  border: 1px solid #FFF;
  border-radius: 50%;
  object-fit: cover;
}

.ele-avatar-list .ele-avatar-list-img:first-child {
  margin-left: 0 !important;
}

.ele-avatar-list .ele-avatar-list-more {
  color: #f56a00;
  background-color: #fde3cf;
}
</style>