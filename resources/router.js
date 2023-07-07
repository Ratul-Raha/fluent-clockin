import {createWebHashHistory, createRouter} from "vue-router";
import RegistrationForm from './js/components/auth/RegistrationForm.vue';
import LoginForm from './js/components/auth/LoginForm.vue';
import DashBoard from './js/components/auth/DashBoard.vue';


const routes = [
  {
    path: '/',
    component: DashBoard
  },
  {
    path: '/login',
    component: LoginForm
  },
  {
    path: '/register',
    component: RegistrationForm
  },

];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;
