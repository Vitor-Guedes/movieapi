<template>
  
    <div class="relative mx-auto overflow-hidden border-2 w-6/12">
        <!-- Área das imagens -->
        <div class="gap-2 flex transition-transform duration-500" :style="{ transform: `translateX(-${currentIndex * 100}%)` }">

            <img
                v-for="(image, index) in list"
                :key="index"
                :src="image.link"
                class="h-96 object-cover mx-auto"
                alt="Carousel Image"
            />

        </div>

        <!-- Botões de Navegação -->
        <button class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white text-indigo-500 bg-opacity-50 px-2 font-bold text-2xl hover:bg-gray-100/75 rounded-full" @click="previous"> 
            <
        </button>
        
        <button class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white text-indigo-500 bg-opacity-50 px-2 font-bold text-2xl hover:bg-gray-100/75 rounded-full" @click="next"> 
            > 
        </button>

        <!-- Indicadores -->
        <div class="absolute bottom-4 left-6 transform -translate-x-1/2 flex space-x-2 gap-6">

            <span
                v-for="(image, index) in list"
                :key="index"
                class="h-3 w-3 rounded-full cursor-pointer"
                :class="index === currentIndex ? 'bg-indigo-500' : 'bg-gray-100'"
                @click="goTo(index)"
            ></span>

        </div>

    </div>

</template>

<script>
export default {
    props: ['list'],

    data() {
        return {
            currentIndex: 0
        }
    },

    methods: {
        previous () {
            this.currentIndex = (this.currentIndex - 1 + this.list.length) % this.list.length;
        },

        next() {
            this.currentIndex = this.currentIndex = (this.currentIndex + 1) % this.list.length;
        },

        goTo(index) {
            this.currentIndex = index;
        }
    }
}
</script>