import { createApp } from 'vue'

import App from '../js/components/App.vue'

import router from "../router.js"
import ElementPlus from "element-plus";
import "element-plus/dist/index.css";
import * as ElementPlusIconsVue from "@element-plus/icons-vue";

// createApp(App).mount('#fluent_clockin_app');

// new Vue({
//     router,
//     render: (h) => h(App),
//   }).$mount('#fluent_clockin_app');




const app = createApp(App);
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
  app.component(key, component);
}
app.use(ElementPlus).use(router).mount("#fluent_clockin_app");