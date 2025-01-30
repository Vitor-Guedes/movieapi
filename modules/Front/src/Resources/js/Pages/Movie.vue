<template>
    <Layout>
        <template #content>
            
            <div class="w-6/12 mx-auto">
                
                <div class="my-4">
                    <button class="text-sm text-indigo-500 hover:text-indigo-800 underline" @click="$router.back()">
                        Voltar
                    </button>
                </div>

                <div id="header" class="flex flex-row justify-between">
                    <div class="flex flex-col">
                        <h1 class="text-2xl text-bold" v-text="source.original_title"></h1>
                        <h3 class="text-sm italic text-gray-500" v-text="source.title"></h3>
                    </div>
                    <div class="flex flex-col p-2">
                        <span class="text-sm italic" v-text="release_date"></span>
                        <span class="text-sm subpixel-antialiased" v-text="source.original_language"></span>
                    </div>
                </div>

                <div id="divider" class="border shadow-sm my-2"></div>

                <blockquote>
                    <p v-text="source.overview"></p>
                </blockquote>

                <br>

                <blockquote>
                    <p class="text-sm text-gray-500 text-center italic" v-text="source.tagline"></p>
                </blockquote>

                <div id="divider" class="border shadow-sm my-2"></div>

                <table class="table w-full mx-auto px-1 mt-6">
                    <thead class="text-sm text-indigo-500">
                        <tr>
                            <th scope="col">Popularidade</th>
                            <th scope="col">Votos</th>
                            <th scope="col">Duração</th>
                            <th scope="col">Média</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-sm text-gray-500 text-center">
                            <td v-text="source.popularity"></td>
                            <td v-text="source.vote_count"></td>
                            <td v-text="source.runtime + ' minutos'"></td>
                            <td v-text="source.vote_average"></td>
                        </tr>
                    </tbody>
                </table>

                <div id="divider" class="border shadow-sm my-2"></div>

                <BudgeList :source="source.genres">
                    <template #head>
                        Categorias
                    </template>

                    <template v-slot:body="{ item }">
                        {{ item.name }}
                    </template>
                </BudgeList>

                <div id="divider" class="border shadow-sm my-2"></div>

                <BudgeList :source="source.keywords">
                    <template #head>
                        Palavras Chaves
                    </template>

                    <template v-slot:body="{ item }">
                        {{ item.name }}
                    </template>
                </BudgeList>

                <div id="divider" class="border shadow-sm my-2"></div>

                <BudgeList :source="source.spoken_languages">
                    <template #head>
                        Linguagens
                    </template>

                    <template v-slot:body="{ item }">
                        {{ item.name }}
                        {{ item.name }} ({{ iso_639_1(item) }})
                    </template>
                </BudgeList>

                <div class="w-full">
                    <h2 class="text-sm font-medium text-indigo-500 mt-4 mb-2">Produção</h2>

                    <div class="flex flex-row mb-4 gap-2 pb-2 text-xs">
                        <div>
                            <span class="underline text-sm text-gray-500 italic">Empresas</span>
                            <ul>
                                <li v-for="company in source.production_companies">
                                    {{ company.name }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex flex-row mb-4 gap-2 pb-2 text-xs">
                        <div>
                            <span class="underline text-sm text-gray-500 italic">Paises</span>
                            <ul>
                                <li v-for="country in source.production_countries">
                                    {{ country.name }} - <span class="italic">{{ country.iso_3166_1 }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
               
            </div>

        </template>
    </Layout>
</template>

<script>
import Layout from '../Layout.vue';
import BudgeList from '../components/BadgeList.vue';

export default {
    name: 'Movie',

    components: {
        Layout,
        BudgeList
    },

    async beforeCreate() {
        const relations = ['genres', 'spoken_languages', 'production_countries', 'keywords', 'production_companies'];
        this.source = await this.$movieService.findWith(this.$route.params.id, relations);
    },

    data() {
        return {
            source: {}
        }
    },

    computed: {
        release_date() {
            return this.$movieService.dateFormat(this.source.release_date);
        },

        iso_639_1() {
            return (iso) => iso.iso_639_1.toUpperCase()
        }
    }
}

</script>