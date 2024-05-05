import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/'
    // overit ci som dal spravnu url
});

api.defaults.headers.post['Content-Type'] = 'application/json';


export default api;
