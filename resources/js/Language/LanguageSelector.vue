<template>
    <inertia-link :href="route('language', [selectable_locale])" :class="classes">
        <div v-if="selectable_locale == 'fr'">FR</div>
        <div v-else>EN</div>
    </inertia-link>
</template>
 
<script>
 
export default {
 
    computed: {
        selectable_locale() {
            if(this.$page.locale == 'fr') {
                return 'en';
            }
            return 'fr'
        },
        classes() {
            return 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-red-900 hover:text-red-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out'
        }
    },
}
</script>