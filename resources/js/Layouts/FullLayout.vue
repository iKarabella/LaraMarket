<script setup>
import SearchBlock from '@/Components/Elements/Search/SearchBlock.vue';
import HeaderBlock from '@/Components/Elements/HeaderBlock.vue';
import CatalogMenuBlock from '@/Components/Elements/CatalogMenu/CatalogMenuBlock.vue';
import Keller from './Partials/Keller.vue';
import hmenu from '/resources/images/hmenu.jpg';
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <HeaderBlock/>
            <SearchBlock/>
            <div class="text-white bold" :style="`background: #f38619 url(${hmenu}) 0 repeat; text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.75);`">
                <CatalogMenuBlock class="max-w-7xl mx-auto flex items-center justify-between text-xl sm:px-6 lg:px-8" :elements="$page.props.catalogMenu"/>
            </div>
            <!-- Содержание страницы -->
            <main>
                <slot />
            </main>
            <footer>
                <Keller :catalog="$page.props.catalogMenu"/>
            </footer>
        </div>
    </div>
</template>