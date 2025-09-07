<script setup>
    import Dropdown from '@/Components/UI/Dropdown.vue';
    import DropdownLink from '@/Components/UI/DropdownLink.vue';
    import NavLink from '@/Components/UI/NavLink.vue';
    import ResponsiveNavLink from '@/Components/UI/ResponsiveNavLink.vue';
    import { Link } from '@inertiajs/vue3';
</script>
<template>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <!-- Главное навигационное меню -->
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Линейка ссылок -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <NavLink :href="route('home')" :active="route().current('home')">
                            Бонус+
                        </NavLink>
                        <NavLink :href="route('home')" :active="route().current('home')">
                            Доставка
                        </NavLink>
                        <NavLink :href="route('home')" :active="route().current('home')">
                            Контакты
                        </NavLink>
                    </div>
                </div>
                <div class="sm:flex sm:items-center sm:ms-6">

                    <div class="pr-6">
                        <i class="ri-phone-fill"></i>
                        <a href="tel:+7 (3333) 54-56-15" class="ml-2 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                            +7 (3333) 54-56-15
                        </a>
                    </div>

                    <!-- Выпадающее меню -->
                    <div class="ms-3 relative flex" v-if="$page.props.auth.user">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                >
                                    {{ $page.props.auth.user.nickname }}

                                    <svg
                                        class="ms-2 -me-0.5 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                            </template>

                            <template #content>
                        <DropdownLink :href="route('home', userLogin)"> Профиль </DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">
                            Выход
                        </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                    <div class="ms-3 relative" v-else>
                        <Link
                            :href="route('login')"
                            class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >Вход</Link
                        >

                        <Link
                            :href="route('register')"
                            class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >Регистрация</Link
                        >
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
                </div>

                <!-- Адаптивное навигационное меню -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
            <div class="pt-2 pb-3 space-y-1">
                <ResponsiveNavLink href="#">
                    Бонус+
                </ResponsiveNavLink>
                <ResponsiveNavLink href="#">
                    Доставка
                </ResponsiveNavLink>
                <ResponsiveNavLink href="#">
                    Контакты
                </ResponsiveNavLink>
            </div>
                </div>
            </nav>
</template>