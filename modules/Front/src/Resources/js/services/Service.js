import axios from "axios";

class MovieService {

    last_url = ''

    async find(movieId) {
        try {
            const movie = await axios.get('/v1/api/movies/' + movieId + '?with=genres');
            return await movie.status === 200 ? movie.data : {};
        } catch (e) {
            console.error(e);
            return {};
        }
    }

    async findWith(movieId, relations = []) {
        try {
            var searchParams = new URLSearchParams();
            searchParams.set('with', relations.join(','))
            const movie = await axios.get('/v1/api/movies/' + movieId + '?' + searchParams.toString());
            return await movie.status === 200 ? movie.data : {};
        } catch (e) {
            console.error(e);
            return {};
        }
    }

    dateFormat(date) {
        try {
            const _date = new Date(date);
            return [
                _date.getDay(),
                _date.getMonth(),
                _date.getFullYear()
            ].join('/');
        } catch (e) {
            console.error(e);
            return date;
        }
    }

    releaseDateYear(date) {
        return (new Date(date)).getFullYear();
    }

    async images(term) {
        try {
            const response = await axios.get('/v1/api/movies/images?term=' + term);
            return await response.status === 200 ? response.data : [];
        } catch (e) {
            console.error(e);
            return [];
        }
    }
}

class Services {
    install (app, options) {
        app.config.globalProperties.$movieService = new MovieService();
    }
}

export default new Services;