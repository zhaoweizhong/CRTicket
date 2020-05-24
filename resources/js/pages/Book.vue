<template>
    <div></div>
</template>

<script>
export default {
    name: 'Book',
    created() {
        this.$emit('getTitle', '车票预订');
    },
}
</script>

<style>

</style>