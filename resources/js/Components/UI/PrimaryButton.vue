<script>
    export default{
        props: {
            disabled:{
                type:Boolean, 
                default:false,
            },
        }
    }
</script>
<template>
    <button
        class="inline-flex justify-center px-4 py-2 border border-transparent 
               rounded-md font-semibold text-xs text-white dark:text-indigo-200 
               uppercase tracking-widest transition ease-in-out duration-150"
        :class="
            {
                'bg-orange-500 dark:bg-orange-600 focus:bg-orange-400 dark:focus:bg-orange-700 active:bg-orange-700 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 hover:bg-orange-600 dark:focus:ring-offset-orange-600 dark:hover:bg-white cursor-pointer':disabled==false, 
                'bg-orange-400 dark:bg-orange-400 cursor-default':disabled==true
            }"
    >
        <slot />
    </button>
</template>
