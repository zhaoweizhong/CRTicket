<template>
    <div></div>
</template>

<script>
export default {
    name: 'User',
    created() {
        this.$emit('getTitle', '用户管理');
    },
}
</script>

<style>

</style>