<script>
    export default{
        props: {
            disabled:{ 
                type:Boolean, 
                default:false,
            }
        }
    }
</script>
<template>
    <button
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
        :class="{'bg-red-600  dark:bg-gray-200 dark:hover:bg-white hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800':disabled==false, 'cursor-default bg-gray-600 dark:bg-gray-400':disabled==true}"
    >
        <slot />
    </button>
</template>
