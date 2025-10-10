<script setup>
import { ref } from 'vue';
import SearchBlock from '@/Components/Elements/Search/SearchBlock.vue';
import HeaderBlock from '@/Components/Elements/HeaderBlock.vue';
import CatalogMenuBlock from '@/Components/Elements/CatalogMenu/CatalogMenuBlock.vue';

</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <HeaderBlock/>
            <SearchBlock/>
            <div class="max-w-7xl mx-auto">
                <CatalogMenuBlock class="flex items-center justify-center text-xl" :elements="$page.props.catalogMenu"/>
            </div>
            <!-- Содержание страницы -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>