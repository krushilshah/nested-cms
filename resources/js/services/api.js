import axios from 'axios';

const apiClient = axios.create({
  baseURL: ' http://127.0.0.1:8000/api', // Laravel API base URL
  headers: {
    'Content-Type': 'application/json',
  },
});

export default {

  getAllPages() {
    return apiClient.get('/pages');
  },

  createPage(data) {
    return apiClient.post('/pages', data);
  },

  getPage(id) {
    return apiClient.get(`/pages/${id}`);
  },

  updatePage(id, data) {
    return apiClient.put(`/pages/${id}`, data);
  },

  deletePage(id) {
    return apiClient.delete(`/pages/${id}`);
  },


  resolvePage(path) {
    return apiClient.get(`/${path}`);
  },
};