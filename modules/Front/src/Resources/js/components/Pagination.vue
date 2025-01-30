<template>
    <div class="mt-6 p-1 w-full">
        <nav>
            <ul class="flex flex-row justify-between gap-2 text-sm">
                <li>
                    <button v-if="current_page > 1" type="button" @click="previous" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-indigo-500 border border-e-0 border-gray-300 rounded-lg hover:text-indigo-800 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700">
                        Previous
                    </button>
                </li>
                <li>
                    <button type="button" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-indigo-500 border border-e-0 border-gray-300 rounded-lg hover:text-indigo-800 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700">
                        {{ current_page }}
                    </button>
                </li>
                <li>
                    <button type="button" @click="next" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-indigo-500 border border-e-0 border-gray-300 rounded-lg hover:text-indigo-800 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700">
                        Next
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
export default {
    name: 'Pagination',
    
    props: ['current_page', 'prev_page_url', 'next_page_url'],

    methods: {
        previous() {
            this.$parent.fetch(this.prev_page_url);
        },

        next() {
            this.$parent.fetch(this.next_page_url);
        }
    }
}
</script>