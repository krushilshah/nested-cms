import { createRouter, createWebHistory } from 'vue-router';
import PageView from '../views/PageView.vue';

const routes = [
  {
    path: '/:pathMatch(.*)*',
    name: 'PageView',
    component: PageView,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;