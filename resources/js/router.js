import Vue from 'vue';
import VueRouter from 'vue-router';
import {getCookie} from 'tiny-cookie'
import store from "./store";

import PageLayout from './layouts/PageLayout';
import Home from './pages/Home'
import Login from './pages/Login'
import Passenger from './pages/Passenger'
import Book from './pages/Book'
import Order from './pages/Order'
import AdminTrain from './pages/admin/Train'
import AdminOrder from './pages/admin/Order'
import AdminUser from './pages/admin/User'
import ErrorPage from './pages/Error'

Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        component: PageLayout,
        children: [
            {
                path: '/',
                component: Home
            },
            {
                path: '/login',
                component: Login,
                meta: {
                    requiresNoAuth: true
                },
            },
            {
                path: '/passenger',
                component: Passenger,
                meta: {
                    requiresAuth: true
                },
            },
            {
                path: '/book',
                component: Book,
                meta: {
                    requiresAuth: true
                },
            },
            {
                path: '/order',
                component: Order,
                meta: {
                    requiresAuth: true
                },
            },
            {
                path: '/admin/train',
                component: AdminTrain,
                meta: {
                    requiresAuth: true,
                    requiresAdmin: true
                },
            },
            {
                path: '/admin/order',
                component: AdminOrder,
                meta: {
                    requiresAuth: true,
                    requiresAdmin: true
                },
            },
            {
                path: '/admin/user',
                component: AdminUser,
                meta: {
                    requiresAuth: true,
                    requiresAdmin: true
                },
            },
            {
                path: '/error',
                component: ErrorPage
            }
            ,
            {
                path: '/*',
                component: ErrorPage
            }
        ]
    },
]


const router = new VueRouter({
    mode: 'history',
    routes
})

router.beforeEach((to, from, next) => {
    if ((store.state.account.isLogin || store.state.account.user) && getCookie('token') == null) {
        store.commit('account/logout')
    } else if ((!store.state.account.isLogin || !store.state.account.user) && getCookie('token')) {
        store.commit('account/login', getCookie('token'))
    }
    if (to.meta.requiresAuth) {
        //需要认证
        if (getCookie('token') == null) {
            store.commit('account/logout')
            next('/login');
        } else if ((!store.state.account.isLogin || !store.state.account.user) && getCookie('token')) {
            store.commit('account/login', getCookie('token'))
            if (to.meta.requiresAdmin && store.state.account.user.type != 'admin') {
                next('/error');
            } else {
                next();
            }
        } else {
            next();
        }
    } else if (to.meta.requiresNoAuth) {
        //需要不认证
        if (!store.state.account.isLogin) {
            next();
        } else {
            next('/');
        }
    } else {
        next();
    }
  })

export default router