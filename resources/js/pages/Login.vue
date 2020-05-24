<template>
    <div class="page">
        <el-form class="login-form" ref="loginForm" :model="loginForm">
            <img src="images/logo.png" alt="" class="logo">
            <el-form-item>
                <el-input v-model="loginForm.username" placeholder="用户名">
                    <i slot="suffix" class="far fa-user"></i>
                </el-input>
            </el-form-item>
            <el-form-item>
                <el-input type="password" v-model="loginForm.password" placeholder="密码">
                    <i slot="suffix" class="far fa-key"></i>
                </el-input>
            </el-form-item>
            <el-form-item>
                <el-button class="login-button" type="primary" @click="handleLogin" :loading="logging">登录</el-button>
            </el-form-item>
        </el-form>
        <el-divider class="login-divider"></el-divider>
        <div class="auto-fillin">
            <span class="title"><i class="fas fa-key"></i>快速填充</span>
            <div class="button">
                <el-button type="success" @click="userLogin">用户</el-button>
                <el-button type="danger" @click="adminLogin">管理员</el-button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Login',
    data() {
        return {
            loginForm: {
                username: '',
                password: '',
            },
            logging: false,
        }
    },
    created() {
        this.$emit('getTitle', '登录');
    },
    methods: {
        handleLogin() {
            this.$refs['loginForm'].validate((err) => {
                if (err) {
                    this.logging = true
                    this.$axios.post('/api/auth', {
                        username: this.loginForm.username,
                        password: this.loginForm.password,
                    }).then((res) => {
                        const result = res.data
                        if (res.status == 201) {
                            this.logging = false
                            this.$store.commit('account/login', result.access_token)
                            this.$message({
                                showClose: true,
                                message: '登录成功，欢迎回来',
                                type: 'success'
                            });
                            this.router.push('/')
                        } else {
                            this.$message({
                                showClose: true,
                                message: '登录失败，请稍后重试',
                                type: 'error'
                            });
                        }
                    }).catch((error) => {
                        this.$message({
                            showClose: true,
                            message: error.response.data.message,
                            type: 'error'
                        });
                        if (this.loginType == 'password') {
                            this.passwordError = error.response.data.message
                        } else {
                            this.smsError = error.response.data.message
                        }
                        this.logging = false
                    })
                }
            })
        },
        userLogin() {
            this.loginForm.username = 'user'
            this.loginForm.password = 'password'
        },
        adminLogin() {
            this.loginForm.username = 'admin'
            this.loginForm.password = 'password'
        }
    },
}
</script>

<style lang="less" scoped>
.page {
    padding-top: 40px;
}
.login-form {
    width: 300px;
    text-align: center;
    margin: auto;
    .logo {
        width: 200px;
        margin: 15px 0 30px 0;
    }
    .login-button {
        margin: 10px 0;
    }
}
.login-divider {
    width: 350px;
    margin: 24px auto;
}
.auto-fillin {
    text-align: center;
    .title {
        i {
            margin-right: 4px;
            font-size: 15px;
            line-height: 16px;
        }
    }
    .button {
        margin-top: 10px;
    }
}
</style>
<style lang="less">
.login-form {
    .el-input__suffix {
        margin-right: 5px;
    }
}
</style>