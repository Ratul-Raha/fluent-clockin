import {createWebHashHistory, createRouter} from "vue-router";
import DashBoard from './js/components/DashBoard.vue';


const routes = [
  {
    path: '/',
    component: DashBoard
  },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;
