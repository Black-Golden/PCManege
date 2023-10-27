/**
 * 主题状态管理
 */
import setting from '@/config/setting'

// 获取屏幕宽度
const screenWidth = (document.documentElement.clientWidth || document.body.clientWidth);
// 获取本地缓存配置
let cache = {};
try {
    cache = JSON.parse(localStorage.getItem(setting.themeStoreName) || '{}');
} catch (e) {
    console.error(e);
}
// 获取缓存的主题配置和缓存的主题css
let cacheTheme = getCache([
    'sideStyle', 'headStyle', 'tabStyle', 'bodyFull', 'layoutStyle', 'theme',
    'showTabs', 'logoAutoSize', 'fixedLayout', 'colorfulIcon', 'sideUniqueOpen'
], setting), cacheStyle = {}, cacheStoreName = setting.themeStoreName + '-cache';
if (cacheTheme.theme) {
    try {
        cacheStyle = JSON.parse(localStorage.getItem(cacheStoreName) || '{}');
        if (!cacheStyle || setting.version !== cacheStyle.version) cacheStyle = {};
    } catch (e) {
        console.error(e);
    }
}

export default {
    namespaced: true,
    state: {
        sideStyle: cacheTheme.sideStyle,  // 侧边栏风格,0默认,1暗色
        headStyle: cacheTheme.headStyle,  // 顶栏风格,0默认,1暗色,2主色
        tabStyle: cacheTheme.tabStyle,  // 标签页风格,0默认(下划线),1圆点,2卡片
        bodyFull: cacheTheme.bodyFull,  // 内容区域宽度铺满
        layoutStyle: cacheTheme.layoutStyle,  // 布局风格,0默认,1顶部菜单风格,2混合菜单风格
        theme: cacheTheme.theme,  // 主题色
        showTabs: cacheTheme.showTabs,  // 是否开启多标签
        logoAutoSize: cacheTheme.logoAutoSize,  // logo是否自适应宽度
        fixedLayout: cacheTheme.fixedLayout,  // 是否固定顶栏和侧栏
        colorfulIcon: cacheTheme.colorfulIcon,  // 侧栏是否多彩图标
        sideUniqueOpen: cacheTheme.sideUniqueOpen,  // 侧边栏是否只保持一个子菜单展开
        collapse: screenWidth < 992,  // 是否折叠侧边栏
        screenWidth: screenWidth,  // 当前屏幕宽度
        showSetting: false,  // 是否显示主题设置抽屉
        showPassword: false,  // 是否显示修改密码弹窗
        themeCache: cacheStyle.cache || {},  // 主题css缓存
        reload: false,  // 用于刷新
        tabs: [],  // 当前打开的选项卡
        navActive: 0  // 混合导航模式顶部选中
    },
    mutations: {
        SET: (state, obj) => {
            state[obj.key] = obj.value;
            if (['collapse', 'reload', 'showSetting'].indexOf(obj.key) !== -1) {  // 重新获取屏幕宽度
                let sw = document.documentElement.clientWidth || document.body.clientWidth;
                if (state.screenWidth !== sw) state.screenWidth = sw;
            }
            if (state.showSetting && obj.key !== 'showSetting') {
                let temp = JSON.parse(localStorage.getItem(setting.themeStoreName) || '{}')
                temp[obj.key] = obj.value;
                localStorage.setItem(setting.themeStoreName, JSON.stringify(temp));
            }
        },
        TAB_PUSH(state, obj) {
            if (!state.tabs.some(r => r.path === obj.path)) state.tabs.push(obj);
        }
    },
    actions: {
        /* 修改配置 */
        set({commit}, obj) {
            commit('SET', obj);
        },
        /* 切换配置 */
        toggle({commit, state}, key) {
            commit('SET', {key: key, value: !state[key]});
        },
        /* 混合菜单模式切换侧边栏显示 */
        setNavActive({commit}, index) {
            commit('SET', {key: 'navActive', value: index});
        },
        /* 添加新tab */
        tabAdd({commit}, obj) {
            commit('TAB_PUSH', obj);
        },
        /* 关闭指定tab */
        tabRemove({commit, state}, path) {
            return new Promise((resolve) => {
                let last = -1;
                for (let i = 0; i < state.tabs.length; i++) {
                    if (state.tabs[i].path === path) {
                        break;
                    }
                    last = i;
                }
                commit('SET', {key: 'tabs', value: state.tabs.filter(d => d.path !== path)});
                resolve(last);
            });
        },
        /* 关闭所有tab */
        tabRemoveAll({commit}) {
            commit('SET', {key: 'tabs', value: []});
        },
        /* 关闭左侧tab */
        tabRemoveLeft({commit, state}, path) {
            for (let i = 0; i < state.tabs.length; i++) {
                if (state.tabs[i].path === path) {
                    commit('SET', {key: 'tabs', value: state.tabs.slice(i)});
                    break;
                }
            }
        },
        /* 关闭右侧tab */
        tabRemoveRight({commit, state}, path) {
            for (let i = 0; i < state.tabs.length; i++) {
                if (state.tabs[i].path === path) {
                    commit('SET', {key: 'tabs', value: state.tabs.slice(0, i + 1)});
                    break;
                }
            }
        },
        /* 关闭其他tab */
        tabRemoveOther({commit, state}, path) {
            commit('SET', {key: 'tabs', value: state.tabs.filter(d => d.path === path)});
        },
        /* 切换主题 */
        setTheme({commit, state}, theme) {
            return new Promise((resolve, reject) => {
                if (!theme) {  // 恢复默认主题
                    document.head.querySelectorAll('style[id^="ele-theme-"],link[id^="ele-theme-"]').forEach((node) => {
                        node.parentNode.removeChild(node);
                    });
                    commit('SET', {key: 'theme', value: theme});
                    return resolve();
                } else if (state.themeCache[theme]) {  // 主题css已经缓存过
                    document.head.querySelectorAll('style[id^="ele-theme-"],link[id^="ele-theme-"]').forEach((node) => {
                        node.parentNode.removeChild(node);
                    });
                    let elem = document.createElement('style');
                    elem.id = `ele-theme-${theme}`;
                    elem.setAttribute('type', 'text/css');
                    elem.innerHTML = state.themeCache[theme];
                    document.head.appendChild(elem);
                    commit('SET', {key: 'theme', value: theme});
                    return resolve();
                } else {  // 动态引入主题css
                    // 检查主题css模块是否已被加载过
                    let oldElem = document.head.querySelector(`#ele-theme-${theme}-js`);
                    if (oldElem) {  // 已被加载过创建css的link标签
                        document.head.querySelectorAll('style[id^="ele-theme-"],link[id^="ele-theme-"]').forEach((node) => {
                            node.parentNode.removeChild(node);
                        });
                        let elem = document.createElement('link');
                        elem.id = `ele-theme-${theme}`;
                        elem.setAttribute('type', 'text/css');
                        elem.setAttribute('rel', 'stylesheet');
                        elem.setAttribute('href', oldElem.getAttribute('ele-css'));
                        document.head.appendChild(elem);
                        commit('SET', {key: 'theme', value: theme});
                        return resolve();
                    }
                    // 把head下面相关元素标记为非主题元素
                    document.head.querySelectorAll('style:not([id^="ele-theme-"]),link:not([id^="ele-theme-"]),script:not([id^="ele-theme-"])').forEach((node) => {
                        if (!node.getAttribute('ele-theme')) node.setAttribute('ele-theme', 'no');
                    });
                    // 加载主题css模块
                    import(`@/styles/theme/${theme}.scss`).then(() => {
                        // 移除上次的主题
                        document.head.querySelectorAll('style[id^="ele-theme-"],link[id^="ele-theme-"]').forEach((node) => {
                            node.parentNode.removeChild(node);
                        });
                        // 获取import之后的主题标签
                        let elem = document.head.querySelectorAll('style:not([ele-theme="no"])');
                        elem = elem.length ? elem[elem.length - 1] : null;
                        if (!elem) {  // 可能是style标签也可能是link标签
                            elem = document.head.querySelectorAll('link:not([ele-theme="no"])');
                            elem = elem.length ? elem[elem.length - 1] : null;
                            if (!elem) return reject(new Error('theme element not found.'));
                            // 再找到对应的主题js模块的标签
                            let href = elem.getAttribute('href');
                            let uuid = href.substring(href.indexOf('chunk-'), href.indexOf('.'));
                            let node = document.head.querySelectorAll(`script[src^="/js/${uuid}"]:not([ele-theme="no"])`);
                            if (node.length) {
                                node[node.length - 1].id = `ele-theme-${theme}-js`;
                                node[node.length - 1].setAttribute('ele-css', href);  // 记录css的link地址
                            }
                        }
                        elem.id = `ele-theme-${theme}`;
                        commit('SET', {key: 'theme', value: theme});
                        // 缓存主题css
                        if (elem.innerHTML) {
                            state.themeCache[theme] = elem.innerHTML;
                            let cache = {};
                            cache[state.theme] = state.themeCache[theme];
                            try {
                                localStorage.setItem(cacheStoreName, JSON.stringify({
                                    version: setting.version, cache: cache
                                }));
                            } catch (e) {
                                console.error(e);
                            }
                        }
                        return resolve();
                    }).catch(e => {
                        reject(e);
                    });
                }
            });
        }
    }
}

/**
 * 获取缓存配置项
 * @param k 配置项
 * @param v 默认值
 */
function getCache(k, v) {
    if (Array.isArray(k)) {
        let obj = {};
        k.forEach(t => {
            obj[t] = (cache[t] === null || cache[t] === undefined) ? v[t] : cache[t];
        });
        return obj;
    } else {
        if (cache[k] === null || cache[k] === undefined) return v;
        return cache[k];
    }
}
