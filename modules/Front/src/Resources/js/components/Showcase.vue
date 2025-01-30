<template>
    <div v-if="source" class="w-6/12 mx-auto">
        <!-- Modifiers -->
        <Modify :url="last_url" :limit="source.per_page"/>
        
        <!-- cards -->
        <div class="grid grid-cols-2 gap-2 mt-4">
            <Card v-for="movie in source.data" :source="movie" />
        </div>

        <!-- Paginação -->
        <Pagination 
            :current_page="source.current_page" 
            :prev_page_url="source.prev_page_url" 
            :next_page_url="source.next_page_url" 
        />
    </div>
</template>

<script>
import axios from 'axios';

import Card from './Card.vue';
import Pagination from './Pagination.vue';
import Modify from './Modify.vue';

export default {
    name: 'Showcase',

    components: {
        Card,
        Pagination,
        Modify
    },

    beforeMount() {
        this.fetch('http://dev.backend.com/v1/api/movies');
    },

    data() {
        return {
            source: {},

            last_url: ''
        }
    },

    methods: {
        fetch(url) {

            axios.get(url)
                .then(response => {
                    this.last_url = url;

                    if (response.status === 200) {
                        this.source = this.modifyUrls(response.data, url);
                    }
                })
                .catch(error => console.error(error))
        },

        modifyUrls(source, currentUrl) {
            var url = new URL(currentUrl);
            
            const applyModifys = (_url) => {
                url.searchParams.keys()
                    .forEach(key => {
                        if (! _url.searchParams.has(key)) {
                            _url.searchParams.set(key, url.searchParams.get(key))
                        }
                    });
                return _url.toString();
            }

            if (source.prev_page_url != null) {
                source.prev_page_url = applyModifys(
                    new URL(source.prev_page_url)
                );
            }

            if (source.next_page_url != null) {
                source.next_page_url = applyModifys(
                    new URL(source.next_page_url)
                );
            }

            return source;
        }
    }
}
</script>