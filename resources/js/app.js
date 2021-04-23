import 'intersection-observer';
import Vue from 'vue';
import VueAnnouncer from '@vue-a11y/announcer';
import VueCheckView from 'vue-check-view';
import VueFilterDateFormat from 'vue-filter-date-format';
import VueGtm from 'vue-gtm';
import VueProgressBar from 'vue-progressbar';
import VueScrollTo from 'vue-scrollto';
import { VueHammer } from 'vue2-hammer';
import { VSkip } from 'vuetensils/src/components';
import router from './router';
import { store } from './store';
import App from './components/App.vue';

// /**
//  * The following block of code may be used to automatically register your
//  * Vue components. It will recursively scan this directory for the Vue
//  * components and automatically register them with their "basename".
//  *
//  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
//  */
const files = require.context('./', true, /\.vue$/i);
files.keys().map((key) => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.use(VueGtm, {
  id: process.env.MIX_GTM_ID ? process.env.MIX_GTM_ID : 'GTM-XXXXXXX',
  defer: false,
  enabled: process.env.MIX_PROD,
  debug: false,
  loadScript: true,
});

Vue.use(VueHammer);
Vue.use(VueFilterDateFormat);
Vue.use(VueAnnouncer, {}, router);
Vue.use(VueCheckView);
Vue.use(VueFilterDateFormat);
Vue.use(VueProgressBar, {
  color: '#ee2a7b',
  failedColor: 'red',
  height: '2px',
});
Vue.use(VueScrollTo);

Vue.component('VSkip', VSkip);

const app = new Vue({ // eslint-disable-line
  router,
  computed: {
    overlayOpen() {
      return store.searchOverlayActive || store.facetOverlayActive || store.footerActive;
    },
  },
  watch: {
    $route(to, from) {
      if (to.path !== from.path) {
        this.$nextTick(() => {
          setTimeout(() => {
            this.setFocus();
          }, 0);
        });
      }
    },
    overlayOpen() {
      document.body.classList.toggle('overlay--open', this.overlayOpen);
      if (this.overlayOpen) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    },
  },
  created() {
    document.addEventListener('keydown', this.onKeyDown, true);
    document.addEventListener('mousedown', this.onPointerDown, true);
    // See https://github.com/hilongjw/vue-progressbar for progress bar docs.
    this.$Progress.start();
    this.$router.beforeEach((to, from, next) => {
      if (from.hash !== to.hash) return;
      this.$Progress.start();
      next();
    });
    this.$router.afterEach(() => {
      this.$Progress.finish();
    });
  },
  destroyed() {
    document.removeEventListener('keydown', this.onKeyDown, true);
    document.removeEventListener('mousedown', this.onPointerDown, true);
  },
  methods: {
    onKeyDown(e) {
      if (e.metaKey || e.altKey || e.ctrlKey) return;
      document.body.dataset.interactionMode = 'keyboard';
    },
    onPointerDown() {
      document.body.dataset.interactionMode = 'pointer';
    },
    setFocus() {
      this.$el.focus();
    },
  },
  render: h => h(App),
}).$mount('#app');
