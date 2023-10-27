/**
 * 项目统一配置
 */
export default {
    version: '1.0',
    name: '后台管理系统',  // 项目名称
    baseURL: process.env.VUE_APP_BASE_URL,  // 接口地址
    whiteList: ['/login', '/forget'],  // 路由白名单(不需要登录的)
    keepAliveList: [],  // 需要缓存的组件名称
    menuUrl: '/index/getMenuList',  // 菜单数据接口
    parseMenu: null,  // 自定义解析接口菜单数据
    parseMenuItem: null,  // 自定义解析接口菜单每一个数据格式
    userUrl: '/index/getUserInfo',  // 用户信息接口
    parseUser: (res) => {  // 自定义解析接口用户信息
        res.data.roles = res.data.roles.map(d => d.roleCode);
        res.data.authorities = res.data.authorities.map(d => d.authority);
        return res;
    },
    tokenHeaderName: 'Authorization',  // token传递的header名称
    tokenStoreName: 'access_token',  // token存储的名称
    userStoreName: 'user',  // 用户信息存储的名称
    themeStoreName: 'theme',  // 主题配置存储的名称
    homeTitle: '主页',  // 首页Tab显示标题,null会根据菜单自动获取
    watchResize: true,  // 是否监听屏幕尺寸改变
    showSetting: true,  // 是否显示主题设置抽屉
    /* 主题默认配置 */
    sideStyle: 1,  // 侧边栏风格,0默认,1暗色
    headStyle: 0,  // 顶栏风格,0默认,1暗色,2主色
    tabStyle: 1,  // 标签页风格,0默认(下划线),1圆点,2卡片
    bodyFull: true,  // 内容区域宽度铺满
    layoutStyle: 0,  // 布局风格,0默认,1顶部菜单风格,2混合菜单风格
    theme: null,  // 默认主题
    showTabs: true,  // 是否开启多标签
    logoAutoSize: false,  // logo是否自适应宽度
    fixedLayout: true,  // 是否固定顶栏和侧栏
    colorfulIcon: false,  // 侧栏是否多彩图标
    sideUniqueOpen: true  // 侧边栏是否只保持一个子菜单展开
}
