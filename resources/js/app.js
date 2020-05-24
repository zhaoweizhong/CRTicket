require('./bootstrap');

import Vue from 'vue'
import axios from 'axios';
import App from './App.vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import {getCookie} from 'tiny-cookie'

import router from './router';
import store from "./store";

Vue.use(ElementUI);

Vue.prototype.$axios = axios;
Vue.prototype.router = router
Vue.prototype.$store = store;

axios.interceptors.response.use(
	function (response) {
		return response;
	},
	function (error) {
		const originalRequest = error.config;
		if (error.response.data.message.indexOf('expired')!=-1 && !originalRequest._retry) {
			originalRequest._retry = true;
			axios.defaults.headers.common["Authorization"] = "Bearer " + getCookie("token");
			return axios.put("/api/auth/current").then(res => {
				store.commit('account/refreshToken', res.data.access_token);
				originalRequest.headers.Authorization =
					"Bearer " + res.data.access_token;
				return axios(originalRequest);
			});
		}
		if (error.response.data.message.indexOf('blacklist')!=-1) {
			store.commit('account/logout')
			Message({
				showClose: true,
				message: '登录失效，请重新登录',
				type: 'error'
			});
			return Promise.reject(error);
		}
		if (error.response.status === 401 && (error.response.data.message.indexOf('Token Signature could not be verified')!=-1 || error.response.data.message.indexOf('Wrong number of segments')!=-1)) {
			store.commit('account/logout')
			Message({
				showClose: true,
				message: '登录失效，请重新登录',
				type: 'error'
			});
			return Promise.reject(error);
		}
		return Promise.reject(error);
	}
);


const app = new Vue({
	el: '#app',
	render: h => h(App),
	router,
	store
});