<template>
    <div id="app">
        <transition name="page-toggle">
            <router-view/>
        </transition>
    </div>
</template>
<script>
export default {
	name: 'app',
};
</script>
<style lang="less">
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
body {
    margin: 0;
    font-family: Helvetica Neue,Helvetica,PingFang SC,Hiragino Sans GB,Microsoft YaHei,SimSun,sans-serif;
    ::selection {
        background-color: #cce2ff;
        color: rgba(0,0,0,.87);
    }
}
html,
body,
#app {
    height: 100%;
}
</style>