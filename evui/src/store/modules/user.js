/**
 * 登录状态管理
 */
import axios from 'axios'
import setting from '@/config/setting'
import util from '@/utils/util'

let loginUser = {};
try {
    loginUser = JSON.parse(localStorage.getItem(setting.userStoreName) || '{}') || {};
} catch (e) {
    console.error(e);
}

export default {
    namespaced: true,
    state: {
        token: localStorage.getItem(setting.tokenStoreName),  // 当前token
        user: loginUser,  // 当前用户信息
        authorities: [], // 当前用户权限
        roles: [],  // 当前用户角色
        menus: null,  // 当前用户的菜单
        permission:[] // 当前登录用户所拥有的权限节点
    },
    mutations: {
        SET: (state, obj) => {
            state[obj.key] = obj.value;
        },
        SET_PERMISSION(state,data){
            state.permission=data
        }
    },
    actions: {
        /* 设置节点权限 */
        setPermission({commit},data){
            commit('SET_PERMISSION',data)
        },
        /* 缓存token */
        setToken({commit}, token) {
            localStorage.removeItem(setting.tokenStoreName);
            if (token) localStorage.setItem(setting.tokenStoreName, token);
            else commit('SET', {key: 'menus', value: null});
            commit('SET', {key: 'token', value: token});
            axios.defaults.headers.common[setting.tokenHeaderName] = token;
        },
        /* 缓存用户 */
        setUser({commit}, user) {
            localStorage.removeItem(setting.userStoreName);
            if (user) localStorage.setItem(setting.userStoreName, JSON.stringify(user));
            commit('SET', {key: 'user', value: user});
        },
        /* 设置用户权限 */
        setAuthorities({commit}, authorities) {
            commit('SET', {key: 'authorities', value: authorities});
        },
        /* 设置用户角色 */
        setRoles({commit}, roles) {
            commit('SET', {key: 'roles', value: roles});
        },
        /* 设置用户菜单 */
        setMenus({commit}, menus) {
            commit('SET', {key: 'menus', value: menus});
        },
        /* 获取用户菜单路由 */
        getMenuRouters({commit, state}) {
            return new Promise((resolve, reject) => {
                if (state.menus) return resolve(menusToRoute(state.menus));
                if (!setting.menuUrl) {
                    commit('SET', {key: 'menus', value: []});
                    return resolve(null);
                }
                axios.get(setting.menuUrl).then(res => {
                    let menus = setting.parseMenu ? setting.parseMenu(res.data) : res.data.data;
                    resolve(menusToRoute(menus));
                    commit('SET', {key: 'menus', value: menus});
                }).catch(e => {
                    reject(e);
                });
            });
        }
    }
}

/** 菜单生成路由 */
function menusToRoute(menus) {
    let route = {path: '/', name: 'main', children: [], redirect: null};
    util.eachTreeData(menus, (item) => {
        if (setting.parseMenuItem) item = setting.parseMenuItem(item);
        item.meta = Object.assign({title: item.title, icon: item.icon, hide: item.hide, uid: item.uid}, item.meta);
        if (item.path && !item.path.startsWith('http://') && !item.path.startsWith('https://')
            && !item.path.startsWith('//')) {
            if (!route.redirect) {
                route.redirect = item.path;
                if (!setting.homeTitle) setting.homeTitle = item.meta.title;
            }
            route.children.push({
                path: item.path, name: item.name || item.path, meta: item.meta,
                component: () => import('@/views' + (item.component || item.path))
            });
        }
    });
    return route;
}
