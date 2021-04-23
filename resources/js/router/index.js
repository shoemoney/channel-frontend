import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from '../components/HomeComponent.vue';
import Search from '../components/SearchPage.vue';
import NotFound from '../components/NotFoundComponent.vue';
import Video from '../components/video/Video.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    name: 'app',
    component: Home,
    meta: {
      announcer: {
        message: 'Home page',
      },
    },
  },
  {
    path: '/search/:params?',
    name: 'search',
    component: Search,
    props: (route) => ({ facetQuery: route.query.facets }),
    meta: {
      announcer: {
        skip: true,
      },
    },
  },
  {
    path: '/video/:id/:slug',
    name: 'video',
    component: Video,
    props: (route) => ({ id: String(route.params.id) }),
    meta: {
      announcer: {
        skip: true,
      },
    },
  },
  {
    path: '*',
    component: NotFound,
    meta: {
      announcer: {
        message: '404 Page not found',
      },
    },
  },
];

export default new VueRouter({
  mode: 'history',
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (to.name === from.name) {
      return false;
    }
    if (savedPosition) {
      return new Promise((resolve, reject) => {
        setTimeout(() => {
          resolve(savedPosition);
        }, 200);
      });
    }
    return { x: 0, y: 0 };
  },
});
