import Vue from 'vue';
import VueRouter from 'vue-router';

import PageLayout from './layouts/PageLayout';
import Home from './pages/Home'
import Login from './pages/Login'

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
                component: Login
            },
            {
                path: '/book',
                component: Login
            },
            {
                path: '/order',
                component: Login
            },
            {
                path: '/admin/train',
                component: Login
            },
            {
                path: '/admin/order',
                component: Login
            },
            {
                path: '/admin/user',
                component: Login
            }
            
        ]
    },
]


const router = new VueRouter({
    mode: 'history',
    routes
})



export default router