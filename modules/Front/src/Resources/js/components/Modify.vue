<template>
    <div>
        <!-- Limit -->
        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" @click="showOptions" class="p-2 text-indigo-500 border hover:bg-blue-800 focus:ring-4 font-sm rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
            Limite Por Página ({{ limit }})
            <svg class="ml-3 w-3 h-3 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdown" ref="limits" class="hidden z-10 border bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
            <ul class="py-2 text-sm text-indigo-800 dark:text-indigo-500" aria-labelledby="dropdownDefaultButton">
                <li v-for="count in allowed" class="py-2">
                    <button href="#" @click="changeQuantity(count)" class="block px-2 py-1 text-indigo-300 hover:text-indigo-800" :class="count == limit ? 'text-indigo-800' : ''" v-text="count"></button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Modify',

    props: [
        'url',
        'limit'
    ],

    data() {
        return {
            allowed: [
                6,
                12,
                24,
                36
            ]
        }
    },

    methods: {
        showOptions() {
            this.$refs.limits.classList.toggle('hidden');
        },

        changeQuantity(qty) {
            this.showOptions();

            var url = new URL(this.url);
            url.searchParams.set('limit', qty);
            this.$parent.fetch(url.toString());
        }
    }
}
</script>