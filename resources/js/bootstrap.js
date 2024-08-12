import axios from 'axios';
import 'chart.js/auto';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
