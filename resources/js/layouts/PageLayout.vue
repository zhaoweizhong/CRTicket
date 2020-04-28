<template>
    <el-card class="body-card">
        <transition name="page-toggle">
            <router-view ref="page" />
        </transition>
    </el-card>
</template>

<script>
export default {
    name: 'PageLayout',
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
.body-card {
    width: 1080px;
    height: 75%;
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255,255,255,0.97);
}
</style>