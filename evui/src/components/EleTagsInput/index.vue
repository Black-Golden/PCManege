<!-- 标签输入框组件 -->
<template>
  <div class="ele-tags-input">
    <el-tag v-for="(text,index) in data" :key="index" :size="size" :effect="effect" :disable-transitions="true"
            :type="type" @close="removeTag(index)" closable>{{ text }}
    </el-tag>
    <el-input ref="eleTagsInput" v-if="edit" v-model="editValue" :size="size"
              @keyup.enter.native="saveEdit" @blur="saveEdit" :style="editStyle"/>
    <el-button v-else :size="size" @click="showEdit" :style="editStyle">{{ placeholder }}</el-button>
  </div>
</template>

<script>
export default {
  name: "EleTagsInput",
  model: {
    prop: 'data',
    event: 'change'
  },
  props: {
    data: Array,
    type: String,
    size: String,
    effect: String,
    placeholder: {
      type: String,
      default: '+ New Tag'
    },
    width: {
      type: String,
      default: '90px'
    },
    unique: {
      type: Boolean,
      default: true
    },
    validator: [Function, Array],
    beforeRemove: Function
  },
  data() {
    return {
      edit: false,
      editValue: ''
    }
  },
  computed: {
    editStyle() {
      return {width: this.width};
    }
  },
  methods: {
    showEdit() {
      this.edit = true;
      this.$nextTick(() => {
        this.$refs.eleTagsInput.$refs.input.focus();
      });
    },
    saveEdit() {
      if (this.editValue) {
        if (this.unique && this.data.indexOf(this.editValue) !== -1) {
          return this.$message.error(this.editValue + " 已经存在");
        }
        if (this.validator) {
          if (typeof this.validator === 'function') {
            if (this.validator(this.editValue) === false) return;
          } else if (!new RegExp(this.validator[0]).test(this.editValue)) {
            return this.$message.error(this.validator[1]);
          }
        }
        this.data.push(this.editValue);
        this.$emit('change', this.data);
      }
      this.edit = false;
      this.editValue = '';
    },
    removeTag(index) {
      if (this.beforeRemove && this.beforeRemove() === false) return;
      this.data.splice(index, 1);
      this.$emit('change', this.data);
    }
  }
}
</script>

<style>
.ele-tags-input .el-tag + .el-tag {
  margin-left: 10px;
}

.ele-tags-input .el-button, .ele-tags-input .el-input {
  overflow: hidden;
  margin-left: 10px;
  vertical-align: middle;
}

.ele-tags-input .el-button--mini {
  padding: 3px 5px;
}

.ele-tags-input .el-input--mini .el-input__inner {
  height: 20px;
  line-height: 20px;
  padding: 0 5px;
}

.ele-tags-input .el-button--small {
  padding: 5px 8px;
}

.ele-tags-input .el-input--small .el-input__inner {
  height: 24px;
  line-height: 24px;
  padding: 0 8px;
  font-size: 12px;
}

.ele-tags-input .el-button--medium {
  padding: 6px 10px;
  font-size: 12px;
}

.ele-tags-input .el-input--medium .el-input__inner {
  height: 28px;
  line-height: 28px;
  padding: 0 10px;
  font-size: 12px;
}

.ele-tags-input .el-button--large {
  padding: 8px 10px;
  font-size: 12px;
}

.ele-tags-input .el-input--large .el-input__inner {
  height: 32px;
  line-height: 32px;
  padding: 0 10px;
  font-size: 12px;
}
</style>