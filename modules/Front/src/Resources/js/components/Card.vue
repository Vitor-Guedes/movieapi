<template>
    <div class="w-auto border shadow-md rounded-lg">

        <div id="default" class="p-4 grid h-full content-stretch gap-2">

            <div class="flex flex-row justify-between border-b-2 gap-2">
                <h2 class="mb-1 text-xl font-extrabold tracking-tight w-fit" v-text="source.original_title"></h2>
                <h2 class="mb-1 italic text-sm font-extrabold tracking-tight" v-text="releaseData"></h2>
            </div>

            <div class="">
                <p class="my-3 text-sm" v-text="source.overview"></p>
            </div>

            <div class="self-end">
                <a href="#" @click="goTo" class="place-self-end underline inline-flex items-center font-mediumunderline text-sm hover:text-indigo-800 text-indigo-500">
                    Ver <svg class="w-3 h-3 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                </a>
            </div>
            
        </div>

    </div>
</template>

<script>
    export default {
        props: ['source'],

        computed: {
            releaseData() {
                return (new Date(this.source.release_date)).getFullYear();
            },

            goTo() {
                this.$router.push('/movie/' + this.source.id);
            }
        }
    }
</script>