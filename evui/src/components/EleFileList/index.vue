<!-- 文件列表组件 -->
<template>
  <div class="ele-file-list-group">
    <!-- 网格模式 -->
    <div v-if="grid" class="ele-file-list">
      <!-- 头部全选框 -->
      <div v-if="showCheckbox" class="ele-file-list-header">
        <div class="ele-file-list-check-group" @click.stop="allChecked=!allChecked">
          <i :class="['ele-file-list-check el-icon-check ele-bg-primary',{'checked':allChecked}]"></i>
          <span v-if="!checked.length">全选</span>
          <span v-else>已选中{{ checked.length }}个文件/文件夹</span>
        </div>
      </div>
      <!-- item -->
      <div v-for="(item,i) in data" :key="i"
           :class="['ele-file-list-item',{'checked':checked.indexOf(item)!==-1}]"
           @click="onItemClick(item)">
        <!-- 背景和边框 -->
        <div class="ele-file-list-item-bg ele-bg-primary"></div>
        <div class="ele-file-list-item-border ele-border-primary"></div>
        <!-- item body -->
        <div class="ele-file-list-item-body">
          <!-- 图标 -->
          <div class="ele-file-list-item-icon">
            <img :src="getIcon(item)" :class="{'ele-file-list-item-icon-image': item.thumbnail}" alt=""/>
          </div>
          <!-- 标题 -->
          <div class="ele-file-list-item-title ele-text-primary" :title="item.name">{{ item.name }}</div>
        </div>
        <!-- 复选框 -->
        <div v-if="showCheckbox" @click.stop="choose(item)" class="ele-file-list-item-check ele-bg-white">
          <i class="el-icon-check ele-bg-primary"></i>
        </div>
      </div>
    </div>
    <!-- 表格模式 -->
    <div v-else class="ele-file-list-table">
      <!-- 表头 -->
      <div :class="['ele-file-list-table-item ele-file-list-table-item-header',{'checked':allChecked}]">
        <div class="ele-file-list-table-item-body">
          <div v-if="showCheckbox" class="ele-file-list-table-item-check-group">
            <i class="ele-file-list-table-item-check el-icon-check ele-bg-primary"
               @click.stop="allChecked=!allChecked"></i>
          </div>
          <div class="ele-file-list-table-item-name" @click="onClickSort('name')">
            <span v-if="!checked.length">
              <span>文件名</span>
              <i v-if="sort==='name'"
                 :class="['ele-file-list-table-item-sort ele-text-primary',{'el-icon-top':order==='asc'},{'el-icon-bottom':order==='desc'}]"></i>
            </span>
            <span v-else @click.stop="allChecked=!allChecked">已选中{{ checked.length }}个文件/文件夹</span>
          </div>
          <div class="ele-file-list-table-item-size" @click="onClickSort('length')">
            <span>大小</span>
            <i v-if="sort==='length'"
               :class="['ele-file-list-table-item-sort ele-text-primary',{'el-icon-top':order==='asc'},{'el-icon-bottom':order==='desc'}]"></i>
          </div>
          <div class="ele-file-list-table-item-time" @click="onClickSort('updateTime')">
            <span>修改日期</span>
            <i v-if="sort==='updateTime'"
               :class="['ele-file-list-table-item-sort ele-text-primary',{'el-icon-top':order==='asc'},{'el-icon-bottom':order==='desc'}]"></i>
          </div>
        </div>
      </div>
      <!-- 列表item -->
      <div v-for="(item,i) in data" :key="i"
           :class="['ele-file-list-table-item',{'checked':checked.indexOf(item)>=0}]">
        <!-- 背景和边框 -->
        <div class="ele-file-list-table-item-bg ele-bg-primary"></div>
        <div class="ele-file-list-table-item-border ele-border-primary"></div>
        <!-- item body cells -->
        <div class="ele-file-list-table-item-body">
          <!-- 复选框 -->
          <div v-if="showCheckbox" class="ele-file-list-table-item-check-group">
            <i class="ele-file-list-table-item-check el-icon-check ele-bg-primary" @click.stop="choose(item)"></i>
          </div>
          <!-- 图标和标题 -->
          <div class="ele-file-list-table-item-name">
            <div class="ele-file-list-table-item-icon">
              <img :src="getIcon(item)" class="ele-file-list-table-item-icon-image" alt="" @click="onItemClick(item)"/>
              <div class="ele-file-list-table-item-title ele-text-primary" :title="item.name"
                   @click="onItemClick(item)">{{ item.name }}
              </div>
            </div>
            <div class="ele-file-list-table-item-tool-group">
              <slot name="tool" v-bind:item="item"></slot>
            </div>
          </div>
          <!-- 大小 -->
          <div class="ele-file-list-table-item-size">{{ item.length }}</div>
          <!-- 修改时间 -->
          <div class="ele-file-list-table-item-time">{{ item.updateTime }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "EleFileList",
  props: {
    grid: {  // 是否网格展示
      type: Boolean,
      default: true
    },
    data: Array,  // 数据
    checked: Array,  // 选中数据
    sort: String,  // 排序字段
    order: String,  // 排序方式
    icons: {  // 网格模式后缀对应图标
      type: Array,
      default() {
        return [
          {icon: require('@/assets/images/ic_file_misc.png'), type: 'file'},
          {icon: require('@/assets/images//ic_file_folder.png'), type: 'dir'},
          {
            icon: require('@/assets/images//ic_file_code.png'),
            types: ['.java', '.js', '.css', '.vue', '.ts', '.tsx', '.scss', '.less', '.c', '.cpp', '.cs', '.jsp', '.php', '.asp', '.py', '.go', '.kt', '.lua']
          },
          {icon: require('@/assets/images//ic_file_htm.png'), types: ['.html', '.htm']},
          {icon: require('@/assets/images//ic_file_text.png'), types: ['.txt']},
          {icon: require('@/assets/images//ic_file_pdf.png'), types: ['.pdf']},
          {icon: require('@/assets/images//ic_file_word.png'), types: ['.doc', '.docx']},
          {icon: require('@/assets/images//ic_file_excel.png'), types: ['.xls', '.xlsx']},
          {icon: require('@/assets/images//ic_file_ppt.png'), types: ['.ppt', '.pptx']},
          {icon: require('@/assets/images//ic_file_visio.png'), types: ['.vsd']},
          {icon: require('@/assets/images//ic_file_ps.png'), types: ['.psd']},
          {icon: require('@/assets/images//ic_file_cad.png'), types: ['.dwg']},
          {icon: require('@/assets/images//ic_file_flash.png'), types: ['.swf']},
          {icon: require('@/assets/images//ic_file_music.png'), types: ['.mp3', '.wav']},
          {
            icon: require('@/assets/images//ic_file_video.png'),
            types: ['.mp4', '.rmvb', '.flv', '.avi', '.3gp']
          },
          {
            icon: require('@/assets/images//ic_file_picture.png'),
            types: ['.png', '.jpg', '.jpeg', '.gif', '.bmp']
          },
          {icon: require('@/assets/images//ic_file_fonts.png'), types: ['.ttf', '.woff']},
          {icon: require('@/assets/images//ic_file_bt.png'), types: ['.torrent']},
          {icon: require('@/assets/images//ic_file_android.png'), types: ['.apk']},
          {icon: require('@/assets/images//ic_file_exe.png'), types: ['.exe']},
          {icon: require('@/assets/images//ic_file_ipa.png'), types: ['.ipa', '.dmg']},
          {icon: require('@/assets/images//ic_file_zip.png'), types: ['.zip', '.rar', '.7z']},
        ];
      }
    },
    smIcons: {  // 表格模式后缀对应图标
      type: Array,
      default() {
        return [
          {icon: require('@/assets/images//ic_file_misc_sm.png'), type: 'file'},
          {icon: require('@/assets/images//ic_file_folder_sm.png'), type: 'dir'},
          {
            icon: require('@/assets/images//ic_file_code_sm.png'),
            types: ['.java', '.js', '.css', '.vue', '.ts', '.tsx', '.scss', '.less', '.c', '.cpp', '.cs', '.jsp', '.php', '.asp', '.py', '.go', '.kt', '.lua']
          },
          {icon: require('@/assets/images//ic_file_htm_sm.png'), types: ['.html', '.htm']},
          {icon: require('@/assets/images//ic_file_text_sm.png'), types: ['.txt']},
          {icon: require('@/assets/images//ic_file_pdf_sm.png'), types: ['.pdf']},
          {icon: require('@/assets/images//ic_file_word_sm.png'), types: ['.doc', '.docx']},
          {icon: require('@/assets/images//ic_file_excel_sm.png'), types: ['.xls', '.xlsx']},
          {icon: require('@/assets/images//ic_file_ppt_sm.png'), types: ['.ppt', '.pptx']},
          {icon: require('@/assets/images//ic_file_visio_sm.png'), types: ['.vsd']},
          {icon: require('@/assets/images//ic_file_ps_sm.png'), types: ['.psd']},
          {icon: require('@/assets/images//ic_file_cad_sm.png'), types: ['.dwg']},
          {icon: require('@/assets/images//ic_file_flash_sm.png'), types: ['.swf']},
          {icon: require('@/assets/images//ic_file_music_sm.png'), types: ['.mp3', '.wav']},
          {
            icon: require('@/assets/images//ic_file_video_sm.png'),
            types: ['.mp4', '.rmvb', '.flv', '.avi', '.3gp']
          },
          {
            icon: require('@/assets/images//ic_file_picture_sm.png'),
            types: ['.png', '.jpg', '.jpeg', '.gif', '.bmp']
          },
          {icon: require('@/assets/images//ic_file_fonts_sm.png'), types: ['.ttf', '.woff']},
          {icon: require('@/assets/images//ic_file_bt_sm.png'), types: ['.torrent']},
          {icon: require('@/assets/images//ic_file_android_sm.png'), types: ['.apk']},
          {icon: require('@/assets/images//ic_file_exe_sm.png'), types: ['.exe']},
          {icon: require('@/assets/images//ic_file_ipa_sm.png'), types: ['.ipa', '.dmg']},
          {icon: require('@/assets/images//ic_file_zip_sm.png'), types: ['.zip', '.rar', '.7z']},
        ];
      }
    }
  },
  computed: {
    /* 是否显示复选框 */
    showCheckbox() {
      return this.checked && Array.isArray(this.checked);
    },
    /* 是否全选 */
    allChecked: {
      get() {
        return this.checked.length && (this.checked.length === this.data.length);
      },
      set(value) {
        this.$emit('update:checked', value ? [].concat(this.data) : []);
      }
    }
  },
  methods: {
    /* 图标 */
    getIcon(item) {
      if (this.grid && item.thumbnail) return item.thumbnail;
      let icons = this.grid ? this.icons : this.smIcons;
      if (item.isDirectory) return icons.filter(d => d.type === 'dir')[0].icon;
      let icon;
      icons.forEach(d => {
        if (d.types) {
          d.types.forEach(s => {
            if (item.name.endsWith(s)) {
              icon = d.icon;
            }
          });
        }
      });
      if (!icon) icon = icons.filter(d => d.type === 'file')[0].icon;
      return icon;
    },
    /* 选中 */
    choose(item) {
      let index = this.checked.indexOf(item);
      if (index === -1) {
        this.$emit('update:checked', this.checked.concat([item]));
      } else {
        let temp = this.checked.slice(0, index).concat(this.checked.slice(index + 1, this.checked.length));
        this.$emit('update:checked', temp);
      }
    },
    /* item点击事件 */
    onItemClick(item) {
      this.$emit('item-click', item);
    },
    /* 排序点击事件 */
    onClickSort(name) {
      let order = this.sort === name ? {'asc': 'desc', 'desc': 'asc'}[this.order] : 'asc';
      this.$emit('sort-change', {sort: name, order: order});
    }
  }
}
</script>

<style>
/** 网格样式 */
.ele-file-list {
  box-sizing: border-box;
  position: relative;
}

/* header */
.ele-file-list .ele-file-list-header {
  padding: 0 15px;
  margin-bottom: 8px;
  border-bottom: 1px solid hsla(0, 0%, 60%, .15);
  box-sizing: border-box;
  line-height: 36px;
  font-size: 13px;
}

/* 全选框 */
.ele-file-list .ele-file-list-header .ele-file-list-check-group {
  display: inline-block;
  cursor: pointer;
}

.ele-file-list .ele-file-list-header .ele-file-list-check-group .ele-file-list-check {
  color: #FFF;
  width: 15px;
  height: 15px;
  line-height: 15px;
  text-align: center;
  border-radius: 2px;
  margin-right: 15px;
  font-weight: 600;
  font-size: 12px;
}

.ele-file-list .ele-file-list-header .ele-file-list-check-group .ele-file-list-check:not(.checked) {
  width: 13px;
  height: 13px;
  line-height: 13px;
  color: transparent !important;
  background-color: transparent !important;
  border: 1px solid hsla(0, 0%, 60%, .3);
}

/* item */
.ele-file-list .ele-file-list-item {
  display: inline-block;
  box-sizing: border-box;
  padding: 10px 3px 10px 3px;
  margin: 0 0 8px 8px;
  text-align: center;
  position: relative;
  cursor: pointer;
}

/* 图标 */
.ele-file-list .ele-file-list-item .ele-file-list-item-icon {
  position: relative;
}

.ele-file-list .ele-file-list-item .ele-file-list-item-icon > img {
  width: 56px;
  height: 56px;
  margin: 14px 0;
  display: inline-block;
}

.ele-file-list .ele-file-list-item .ele-file-list-item-icon > .ele-file-list-item-icon-image {
  width: 84px;
  height: 84px;
  margin: 0;
  object-fit: cover;
}

/* 标题 */
.ele-file-list .ele-file-list-item .ele-file-list-item-title {
  width: 110px;
  font-size: 12px;
  margin-top: 4px;
  box-sizing: border-box;
  overflow: hidden;
  text-overflow: ellipsis;
  word-break: break-all;
  white-space: nowrap;
}

.ele-file-list .ele-file-list-item .ele-file-list-item-title:not(:hover) {
  color: inherit;
}

/* 复选框 */
.ele-file-list .ele-file-list-item .ele-file-list-item-check {
  position: absolute;
  right: 6px;
  top: 6px;
  border-radius: 50%;
  display: none;
  z-index: 1;
}

.ele-file-list .ele-file-list-item.checked .ele-file-list-item-check,
.ele-file-list .ele-file-list-item:hover .ele-file-list-item-check {
  display: block;
}

.ele-file-list .ele-file-list-item .ele-file-list-item-check .el-icon-check {
  color: #FFF;
  font-size: 12px;
  font-weight: 600;
  border-radius: 50%;
  width: 19px;
  height: 19px;
  line-height: 19px;
  text-align: center;
  display: block;
  opacity: .35;
}

.ele-file-list .ele-file-list-item.checked .ele-file-list-item-check .el-icon-check {
  opacity: 1;
}

/* 背景和边框 */
.ele-file-list .ele-file-list-item .ele-file-list-item-bg,
.ele-file-list .ele-file-list-item .ele-file-list-item-border {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 5px;
  box-sizing: border-box;
  display: none;
}

.ele-file-list .ele-file-list-item:hover .ele-file-list-item-bg,
.ele-file-list .ele-file-list-item.checked .ele-file-list-item-bg {
  opacity: .06;
  display: block;
}

.ele-file-list .ele-file-list-item.checked .ele-file-list-item-border {
  opacity: .3;
  border-width: 1px;
  border-style: solid;
  display: block;
}

/* body */
.ele-file-list .ele-file-list-item .ele-file-list-item-body {
  position: relative;
  z-index: 1;
}

/** 表格样式 */
.ele-file-list-table {
  position: relative;
}

/* item */
.ele-file-list-table .ele-file-list-table-item {
  font-size: 13px;
  line-height: 44px;
  position: relative;
}

.ele-file-list-table .ele-file-list-table-item-header {
  line-height: 36px;
  cursor: pointer;
}

/* body */
.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-body {
  display: flex;
  align-items: center;
  border-top: 1px solid hsla(0, 0%, 60%, .15);
  position: relative;
  z-index: 1;
}

.ele-file-list-table .ele-file-list-table-item:last-child .ele-file-list-table-item-body {
  border-bottom: 1px solid hsla(0, 0%, 60%, .15);
}

.ele-file-list-table .ele-file-list-table-item:first-child .ele-file-list-table-item-body {
  border-top: none;
}

/* item body cells */
.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-check-group {
  width: 45px;
  padding-left: 15px;
  box-sizing: border-box;
  flex-shrink: 0;
}

.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-name {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-size {
  width: 120px;
  flex-shrink: 0;
}

.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-time {
  width: 180px;
  flex-shrink: 0;
}

/* 表格复选框 */
.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-check-group .ele-file-list-table-item-check {
  color: #FFF;
  width: 15px;
  height: 15px;
  line-height: 15px;
  text-align: center;
  border-radius: 2px;
  font-weight: 600;
  font-size: 12px;
  cursor: pointer;
}

.ele-file-list-table .ele-file-list-table-item:not(.checked) .ele-file-list-table-item-check-group .ele-file-list-table-item-check {
  width: 13px;
  height: 13px;
  line-height: 13px;
  color: transparent !important;
  background-color: transparent !important;
  border: 1px solid hsla(0, 0%, 60%, .3);
}

/* 图标和标题 */
.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-icon {
  flex: 1;
  height: 24px;
  overflow: hidden;
}

.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-icon .ele-file-list-table-item-icon-image {
  width: 24px;
  height: 24px;
  object-fit: cover;
  display: inline-block;
  vertical-align: top;
  cursor: pointer;
}

.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-icon .ele-file-list-table-item-title {
  display: inline;
  padding-left: 10px;
  box-sizing: border-box;
  word-break: break-all;
  vertical-align: top;
  line-height: 24px;
  cursor: pointer;
}

.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-icon .ele-file-list-table-item-title:not(:hover) {
  color: inherit;
}

/* 工具按钮 */
.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-tool-group {
  flex-shrink: 0;
  display: none;
  align-items: center;
  box-sizing: border-box;
  padding: 0 15px;
  cursor: default;
}

.ele-file-list-table .ele-file-list-table-item:hover .ele-file-list-table-item-tool-group {
  display: flex;
}

.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-tool-group .ele-file-list-item-tool {
  margin-right: 15px;
  font-size: 18px;
  cursor: pointer;
  line-height: 1;
}

/* 背景和边框 */
.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-bg,
.ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-border {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  display: none;
}

.ele-file-list-table .ele-file-list-table-item:hover .ele-file-list-table-item-bg,
.ele-file-list-table .ele-file-list-table-item.checked .ele-file-list-table-item-bg {
  opacity: .06;
  display: block;
}

.ele-file-list-table .ele-file-list-table-item:hover .ele-file-list-table-item-border,
.ele-file-list-table .ele-file-list-table-item:not(.ele-file-list-table-item-header):hover + .ele-file-list-table-item .ele-file-list-table-item-border,
.ele-file-list-table .ele-file-list-table-item.checked .ele-file-list-table-item-border,
.ele-file-list-table .ele-file-list-table-item.checked + .ele-file-list-table-item .ele-file-list-table-item-border {
  opacity: .3;
  border-top-width: 1px;
  border-top-style: solid;
  display: block;
}

.ele-file-list-table .ele-file-list-table-item:not(.ele-file-list-table-item-header):last-child:hover .ele-file-list-table-item-border,
.ele-file-list-table .ele-file-list-table-item:not(.ele-file-list-table-item-header):last-child.checked .ele-file-list-table-item-border {
  border-bottom-width: 1px;
  border-bottom-style: solid;
}

/* 表头排序 */
.ele-file-list-table-item-sort {
  font-weight: 600;
  margin-left: 5px;
}

/* 小屏幕样式 */
@media screen and (max-width: 768px) {
  .ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-size {
    display: none;
  }

  .ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-time {
    display: none;
  }

  .ele-file-list-table .ele-file-list-table-item .ele-file-list-table-item-tool-group {
    padding-right: 0;
  }
}
</style>