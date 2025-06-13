<script>
    export default {
        props: {
            icon: {
                type: String,
                requred: true,
            },
            title: {
                type: String,
                requred: true,
            },
            action: {
                type: Function,
                requred: true,
            },
            isActive: {
                type: Function,
                default: null,
            },
        }
    }
</script>
<template>
    <button 
        class="menu-item" 
        :class="{'is-active': isActive?isActive():null}"
        @click="action"
        :title="title">
        <i :class="`ri-${icon}`"></i>
    </button>
</template>
<style scoped>
.menu-item{
        background:transparent;
        border:none;
        border-radius:0.4rem;
        color:#fff;
        cursor:pointer;
        width:1.75rem;
        height:1.75rem;
        padding:0.25rem;
        margin-right:0.25rem;
        line-height: 1.5em;
    }
.is-active, .menu-item:hover{
        background-color: #3d3c3c;
    }
button {
        background: none;
        border: none;
        border-radius: 0.4rem;
        color: #5c5c5c;
        font: inherit;
        font-size: 12px;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
} 
</style>