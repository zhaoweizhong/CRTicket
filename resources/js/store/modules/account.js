import Vue from 'vue';
import {getCookie, setCookie, removeCookie} from 'tiny-cookie'
import axios from 'axios'

export default {
    namespaced: true,
    state: {
        token: '',
        user () {
            if (localStorage.getItem('user')) {
                return JSON.parse(localStorage.getItem('user'))
            } else {
                return {
                    id: '',
                    username: '',
                    type: ''
                }
            }
        },
        isLogin: false
    },
    mutations: {
        setUserInfo(user) {
            state.user = user;
            localStorage.setItem('user', user);
        },
        login(state, token) {
            state.token = token
            state.isLogin = true
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
            setCookie('token', token, { expires: '2592000s', domain: 'www.crticket.com' })
            axios.get('/api/user')
            .then(function (response) {
                state.user = response.data
                localStorage.setItem('user', JSON.stringify(response.data))
            });
        },
        logout(state) {
            if (getCookie('token') != null) {
                axios.delete('/api/auth/current');
            }
            state.token = ''
            state.isLogin = false
            localStorage.removeItem('user')
            removeCookie('token', { domain: 'www.crticket.com' })
        },
        // 用户刷新 token 成功，使用新的 token 替换掉本地的token
        refreshToken(state, token) {
            state.token = token
            state.isLogin = true
            setCookie('token', token, { expires: '2592000s', domain: 'www.crticket.com' })
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
            axios.get('/api/user')
            .then(function (response) {
                state.user = response.data
                localStorage.setItem('user', JSON.stringify(response.data))
            });
        },
    }
}