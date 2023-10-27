<!-- 图片裁剪弹窗 -->
<template>
  <el-dialog :visible="show" :title="title" :width="width" :custom-class="customClass" :show-close="showClose"
             :close-on-click-modal="closeOnClickModal" :close-on-press-escape="closeOnPressEscape"
             :destroy-on-close="destroyOnClose" @update:visible="d => $emit('update:show', d)"
             :lock-scroll="lockScroll" :append-to-body="appendToBody" :modal-append-to-body="modalAppendToBody"
             :modal="modal" :top="top" @open="onOpen" @closed="onClosed">
    <template slot="title">
      <slot name="title"/>
    </template>
    <ele-cropper v-if="showCropper" :src="src" :aspectRatio="aspectRatio" :viewMode="viewMode" :accept="accept"
                 :drag-mode="dragMode" :initial-aspect-ratio="initialAspectRatio"
                 :min-container-width="minContainerWidth" :min-container-height="minContainerHeight"
                 :min-canvas-width="minCanvasWidth" :min-canvas-height="minCanvasHeight"
                 :min-crop-box-width="minCropBoxWidth" :min-crop-box-height="minCropBoxHeight"
                 :cropped-width="croppedWidth" :cropped-height="croppedHeight"
                 :cropped-min-width="croppedMinWidth" :cropped-min-height="croppedMinHeight"
                 :cropped-max-width="croppedMaxWidth" :cropped-max-height="croppedMaxHeight"
                 :cropped-fill-color="croppedFillColor" :to-blob="toBlob"
                 :cropped-image-smoothing-enabled="croppedImageSmoothingEnabled"
                 :cropped-image-smoothing-quality="croppedImageSmoothingQuality"
                 :tools="tools" :showPreview="showPreview" @crop="data=>$emit('crop',data)"/>
    <template slot="footer">
      <slot/>
    </template>
  </el-dialog>
</template>

<script>
import EleCropper from './EleCropper';

export default {
  name: "EleCropperDialog",
  components: {EleCropper},
  props: {
    /* cropper参数 */
    src: String,  // 裁剪的图片地址
    aspectRatio: {  // 裁剪比例
      type: Number,
      default: 1
    },
    viewMode: Number,  // 裁剪组件模式
    dragMode: String,
    initialAspectRatio: Number,
    minContainerWidth: Number,
    minContainerHeight: Number,
    minCanvasWidth: Number,
    minCanvasHeight: Number,
    minCropBoxWidth: Number,
    minCropBoxHeight: Number,
    croppedWidth: Number,
    croppedHeight: Number,
    croppedMinWidth: Number,
    croppedMinHeight: Number,
    croppedMaxWidth: Number,
    croppedMaxHeight: Number,
    croppedFillColor: Number,
    croppedImageSmoothingEnabled: Boolean,
    croppedImageSmoothingQuality: String,
    toBlob: Boolean,  // 是否返回blob数据
    accept: {  // 允许上传的图片类型
      type: String,
      default: 'image/*'
    },
    tools: {  // 操作按钮布局
      type: [Boolean, String],
      default: true
    },
    showPreview: {  // 是否显示预览组件
      type: Boolean,
      default: true
    },
    /* 弹窗参数 */
    title: {  // 弹窗的标题
      type: String,
      default: '裁剪图片'
    },
    width: {  // 弹窗的宽度
      type: String,
      default: '680px'
    },
    customClass: String,  // Dialog 的自定义类名
    closeOnClickModal: {  // 是否可以通过点击 modal 关闭 Dialog
      type: Boolean,
      default: false
    },
    closeOnPressEscape: {  // 是否可以通过按下 ESC 关闭 Dialog
      type: Boolean,
      default: true
    },
    showClose: {  // 是否显示关闭按钮
      type: Boolean,
      default: true
    },
    lockScroll: {  // 是否在 Dialog 出现时将 body 滚动锁定
      type: Boolean,
      default: true
    },
    appendToBody: Boolean,  // Dialog 自身是否插入至 body 元素上
    modalAppendToBody: {  // 遮罩层是否插入至 body 元素上
      type: Boolean,
      default: true
    },
    modal: {  // 是否需要遮罩层
      type: Boolean,
      default: true
    },
    top: String,  // Dialog CSS 中的 margin-top 值
    destroyOnClose: {  // 关闭时销毁 Dialog 中的元素
      type: Boolean,
      default: false
    },
    show: Boolean  // 是否显示
  },
  data() {
    return {
      showCropper: false
    }
  },
  methods: {
    onOpen() {
      this.showCropper = true;
      this.$emit('open');
    },
    onClosed() {
      this.$emit('closed');
      if (this.destroyOnClose) this.showCropper = false;
    }
  }
}
</script>
