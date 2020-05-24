<template>
    <div class="page">
        <el-container class="sider">
            <el-aside width="250px">
                <el-menu :default-active.sync="activeItem" @select="handleNavSelect">
                    <img src="images/logo.png" alt="" class="logo">
                    <div class="user">
                        <p>欢迎，请登录</p>
                        <el-button type="success" size="mini" @click="router.push('/login')">登录</el-button>
                    </div>
                    <el-divider></el-divider>
                    <el-menu-item index="0"><i class="fad fa-subway"></i>列车查询</el-menu-item>
                    <el-menu-item index="1"><i class="fad fa-credit-card-front"></i>车票预订</el-menu-item>
                    <el-menu-item index="2"><i class="fad fa-shopping-bag"></i>订单管理</el-menu-item>
                    <el-submenu index="-1">
                        <template slot="title"><i class="fas fa-user-shield"></i>管理员</template>
                        <el-menu-item index="3"><i class="fad fa-subway"></i>车次管理</el-menu-item>
                        <el-menu-item index="4"><i class="fad fa-credit-card-front"></i>订单管理</el-menu-item>
                        <el-menu-item index="5"><i class="fas fa-user-cog"></i>用户管理</el-menu-item>
                    </el-submenu>
                    <div class="bottom">
                        <a href="https://github.com/zhaoweizhong/CRTicket" target="_blank"><i class="fab fa-github"></i></a>
                        <p style="margin-top: 3px;"><i class="far fa-copyright"></i>2020 Some rights reserved.</p>
                        <p>Licensed under the MIT License.</p>
                    </div>
                </el-menu>
            </el-aside>
        </el-container>
        <el-container class="body">
            <el-card class="body-card" shadow="hover">
                <transition name="page-toggle">
                    <router-view ref="page" />
                </transition>
            </el-card>
        </el-container>
    </div>
</template>

<script>
export default {
    name: 'PageLayout',
    data() {
        return {
            activeItem: '',
        }
    },
    watch: {
        '$route' (to, from) {
            this.changeActiveItem(to.path)
        },
    },
    created() {
        this.changeActiveItem(this.$router.currentRoute.path)
    },
    methods: {
        changeActiveItem(path) {
            if (path == "/") {
                this.activeItem = "0"
            } else if (path.substring(0,5) == "/book") {
                this.activeItem = "1"
            } else if (path.substring(0,7) == "/order") {
                this.activeItem = "2"
            } else if (path.substring(0,12) == "/admin/train") {
                this.activeItem = "3"
            } else if (path.substring(0,13) == "/admin/order") {
                this.activeItem = "4"
            } else if (path.substring(0,12) == "/admin/user") {
                this.activeItem = "5"
            } else {
                this.activeItem = ''
            }
        },
        handleNavSelect(key, keyPath) {
            switch (key) {
                case '0':
                    this.router.push('/')
                    break;
                case '1':
                    this.router.push('/book')
                    break;
                case '2':
                    this.router.push('/order')
                    break;
                case '3':
                    this.router.push('/admin/train')
                    break;
                case '4':
                    this.router.push('/admin/order')
                    break;
                case '5':
                    this.router.push('/admin/user')
                    break;
            }
        }
    }
}
</script>

<style lang="less" scoped>
.page-toggle-enter-active {
    transition: all 0.2s ease-in 0.25s;
}
.page-toggle-leave-active {
    transition: all 0.2s ease-out 0s;
}
.page-toggle-enter,
.page-toggle-leave-to {
    opacity: 0;
    padding: 0;
}
.page {
    height: 100%;
}
.sider {
    position: fixed;
    height: 100%;
    .el-aside {
        overflow: hidden;
    }
    .logo {
        width: 180px;
        margin: 15px 35px 10px 35px;
    }
    .user {
        text-align: center;
        font-size: 14px;
        p {
            margin: 7px 0;
        }
    }
    .el-divider {
        margin: 15px 0 5px 0;
    }
    .el-menu {
        height: 100%;
    }
    .bottom {
        position: absolute;
        bottom: 0;
        text-align: center;
        width: 250px;
        margin-bottom: 10px;
        a {
            color: #636b6f;
            text-decoration: none;
        }
        p {
            i {
                font-size: 12px;
                line-height: 13px;
                margin-right: 3px;
            }
            font-size: 13px;
            color: #636b6f;
            margin: 0;
            font-family: Tahoma, Arial;
        }
    }
}
.body {
    padding-left: 250px;
    height: 100%;
}
.body-card {
    margin: auto;
    height: 75%;
}

.el-menu-item,
.el-submenu__title {
    i {
        margin-right: 5px;
        width: 17.5px;
        text-align: center;
    }
}

@media only screen and (max-width: 768px) {
    .body-card {
        width: 480px;
    }
}
@media only screen and (min-width: 768px) {
    .body-card {
        width: 480px;
    }
}
@media only screen and (min-width: 992px) {
    .body-card {
        width: 600px;
    }
}
@media only screen and (min-width: 1200px) {
    .body-card {
        width: 800px;
    }
}
@media only screen and (min-width: 1600px) {
    .body-card {
        width: 1080px;
    }
}
</style>