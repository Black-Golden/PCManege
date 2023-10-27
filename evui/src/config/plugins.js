/**
 * 引用框架
 */
import Vue from 'vue'
import './axios'
import setting from './setting'
import util from '@/utils/util'
import permission from '@/utils/permission'  // 角色权限控制
// 第三方组件
import NProgress from 'nprogress'  // 顶部进度条
import 'nprogress/nprogress.css'
import VueClipboard from 'vue-clipboard2'  // 剪切板
// 扩展组件
import EleDataTable from '@/components/EleDataTable'  // 数据表格
import EleIconPicker from '@/components/EleIconPicker'  // 图标选择器
import EleAvatarList from '@/components/EleAvatarList'  // 头像列表
import EleDot from '@/components/EleDot'  // 状态文字
import EleResult from '@/components/EleResult'  // 操作结果
import EleTagsInput from '@/components/EleTagsInput'  // 标签输入框
import EleEmpty from '@/components/EleEmpty'  // 空视图
// UI框架
import ElementUI from 'element-ui'  // ElementUI
import 'element-ui/lib/theme-chalk/display.css'
import '@/styles/eleadmin/icon.scss'  // 图标
import '@/styles/eleadmin/index.scss'  // 样式

Vue.prototype.$util = util;
Vue.prototype.$setting = setting;
Vue.use(ElementUI, {size: 'medium'});
Vue.use(permission);
Vue.use(VueClipboard);
NProgress.configure({showSpinner: false});

/* 全局注册常用组件 */
Vue.component(EleDataTable.name, EleDataTable);
Vue.component(EleIconPicker.name, EleIconPicker);
Vue.component(EleAvatarList.name, EleAvatarList);
Vue.component(EleDot.name, EleDot);
Vue.component(EleResult.name, EleResult);
Vue.component(EleTagsInput.name, EleTagsInput);
Vue.component(EleEmpty.name, EleEmpty);

/** 添加全局过滤器 */
Vue.filter('timeAgo', (value, onlyDate) => {
    return util.timeAgo(value, onlyDate);
});
Vue.filter('digit', (value, length) => {
    return util.digit(value, length);
});
Vue.filter('toDateString', (value, format) => {
    return util.toDateString(value, format);
});
Vue.filter('escape', (value) => {
    return util.escape(value);
});
Vue.filter('htmlToText', (value) => {
    return util.htmlToText(value);
});
