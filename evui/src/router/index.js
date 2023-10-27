/**
 * 路由配置
 */
import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'
import setting from '@/config/setting'
import EleLayout from '@/views/common/layout/Layout'
import NProgress from 'nprogress'

Vue.use(VueRouter)

/* 静态路由配置 */
const routes = [
  {
    path: '/login',
    component: () => import('@/views/login/login'),
    meta: { hide: true, title: '登录' },
  },
  {
    path: '/forget',
    component: () => import('@/views/login/forget'),
    meta: { hide: true, title: '忘记密码' },
  },
]

// 404路由在动态路由后面加
const route404 = {
  path: '',
  component: EleLayout,
  meta: { hide: true },
  children: [
    {
      path: '*',
      component: () => import('@/views/common/exception/404'),
      meta: { hide: true, title: '404' },
    },
  ],
}

const router = new VueRouter({
  routes,
  // mode: 'history'  // 把这个删掉就是hash模式
})

/* 路由守卫 */
router.beforeEach((to, from, next) => {
  NProgress.start()
  document.title = `${to.meta.title || ''}${to.meta.title ? ' - ' : ''}${
    setting.name
  }`
  if (store.state.user.token) {
    // 判断是否登录
    if (!store.state.user.menus) {
      // 判断是否已注册动态路由
      store
        .dispatch('user/getMenuRouters')
        .then((route) => {
          // 获取动态路由
          if (route && route.children) {
            route.component = EleLayout
            // 去除已注册的路由
            for (let i = route.children.length - 1; i >= 0; i--) {
              if (
                router.resolve(route.children[i].path).resolved.matched.length
              ) {
                route.children.splice(i, 1)
              }
            }
            router.addRoutes([route, route404])
          }
          next({ ...to, replace: true })
        })
        .catch(() => {})
    } else {
      next()
    }
  } else if (setting.whiteList.indexOf(to.path) !== -1) {
    // 判断是否在无需登录白名单
    next()
  } else {
    // 未登录跳转登录页面
    next({ path: '/login', query: to.path === '/' ? {} : { from: to.path } })
  }
})

router.afterEach(() => {
  setTimeout(() => {
    NProgress.done()
  }, 150)
})

export default router
