<template>
	<transition name="page-toggle">
		<router-view/>
	</transition>
</template>
<script>
export default {
	name: 'app',
};
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
</style>