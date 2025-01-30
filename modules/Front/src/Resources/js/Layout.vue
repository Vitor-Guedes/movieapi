<template>
    <header>
        <slot name="header">
            <div class="w-6/12 mx-auto shadow-md">
                <h1 class="text-indigo-500 text-2xl mb-6 px-6 py-2">MovieApp</h1>
            </div>
        </slot>
    </header>

    <main>
        <slot name="content">
            <Showcase />
        </slot>
    </main>

   <footer>
        <slot name="footer">
            <div class="mt-6 mx-auto bg-white shadow-inner w-6/12 z-20 border-t-2 p-6 flex justify-center">
                <span class="block text-indigo-500 text-sm sm:text-center">
                    © 2023 Vitor Guedes Gomes™. All Rights Reserved.
                </span>
            </div>
        </slot>
   </footer>
</template>

<script>
import Showcase from './components/Showcase.vue';

export default {
    name: 'BaseLayout',

    components: {
        Showcase
    },

    created() {
        document.title = "MovieApp"
    },  

    data() {
        return {}
    }
}
</script>