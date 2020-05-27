<template>
    <div class="error">
        404
    </div>
</template>

<script>
export default {
    name: 'Error',
    created() {
        this.$emit('getTitle', '错误');
    },
}
</script>

<style lang="less" scoped>
.error {
    font-size: 200px;
    text-align: center;
    margin-top: 100px;
    font-family: monospace;
    color: #616161;
}
</style>