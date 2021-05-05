<template>
  <div
    :class="[
      'container',
      'container--fixed',
      'container--top',
      'header-container',
      { 'overlay--active': overlayActive }]"
  >
    <VSkip
      ref="skip"
      to="#start-of-content"
      class="link link--text link--skip-to-content"
    >
      Skip to content
    </VSkip>
    <header class="header">
      <div class="header__content">
        <div class="header__title">
          <RouterLink
            v-slot="{ href, isExactActive, navigate }"
            class="link link--with-image"
            aria-label="Homepage"
            :to="{name: 'app'}"
          >
            <a
              :href="href"
              :aria-current="isExactActive ? 'page' : false"
              @click="navigate"
            >
              <h1 class="visually-hidden">
                The Channel
              </h1>
              <BaseIcon
                width="24"
                height="24"
                view-box="0 0 2000 196.21"
                icon-name="main-logo"
                title="The Channel logo"
              >
                <ChannelIcon />
              </BaseIcon>
            </a>
          </RouterLink>
          <span class="header__subtitle">
            A project of the<br>
            <a
              class="link--text"
              href="https://example.org"
              target="_blank"
            >
              Hammer Museum
            </a>
          </span>
        </div>
        <nav
          aria-label="Main navigation"
          class="header__actions"
        >
          <button
            class="button button--action button--light overlay-toggle--footer"
            :aria-pressed="overlay.footer"
            :aria-expanded="overlay.footer"
            aria-label="Show information about the archive"
            aria-controls="about-overlay"
            @click="overlay.footer = !overlay.footer"
          >
            <span class="button__text">About</span>
            <BaseIcon
              width="16"
              height="4"
              view-box="0 0 4 16"
              icon-name="menu"
              title="Show information about the archive"
            >
              <MenuIcon />
            </BaseIcon>
          </button>
          <VDrawer
            id="about-overlay"
            v-model="overlay.footer"
            transition="slide-down"
            bg-transition="fade"
            no-scroll
            :classes="{
              bg: 'drawer__container drawer__container--footer',
              content: ['drawer__content', 'drawer__content--footer'] }"
          >
            <template #toggle />
            <button
              :class="['footer__close-button', 'button', 'button--icon']"
              @click="handleFooterClose"
            >
              <BaseIcon
                width="36"
                height="36"
                view-box="0 0 36 36"
                icon-name="close-footer"
                title="Close the description overlay"
              >
                <ClosePinkIcon />
              </BaseIcon>
            </button>
            <TheFooter
              v-hammer:swipe.up="handleFooterClose"
              @close="overlay.footer = false"
            />
          </VDrawer>

          <button
            class="button button--light overlay-toggle--search"
            :aria-pressed="overlay.search"
            :aria-expanded="overlay.search"
            aria-label="Open search form"
            aria-controls="search-overlay"
            @click="overlay.search = !overlay.search"
          >
            <BaseIcon
              width="24"
              height="24"
              view-box="0 0 24 24"
              icon-name="search-header"
              :title="overlay.search ? 'Close the search panel' : 'Open the search panel'"
            >
              <SearchIcon />
            </BaseIcon>
          </button>
          <VDrawer
            id="search-overlay"
            v-model="overlay.search"
            transition="slide-down"
            bg-transition="fade"
            no-scroll
            :classes="{
              bg: 'drawer__container',
              content: [
                'drawer__content',
                'drawer__content--search',
                'background--grate'
              ]
            }"
          >
            <template #toggle />
            <button
              :class="['footer__close-button', 'button', 'button--icon']"
              @click="handleSearchClose"
            >
              <BaseIcon
                width="36"
                height="36"
                view-box="0 0 36 36"
                icon-name="close-footer"
                title="Close the description overlay"
              >
                <ClosePinkIcon />
              </BaseIcon>
            </button>
            <SearchBar
              v-hammer:swipe.up="handleSearchClose"
              :classes="['search-bar--overlay']"
              focus
              :tags="tags"
              :show-tags="true"
              @close="overlay.search = false"
            />
          </VDrawer>
        </nav>
      </div>
    </header>
  </div>
</template>

<script>
import axios from 'axios';
import { VDrawer } from 'vuetensils/src/components';
import SearchBar from './SearchBar.vue';
import TheFooter from './TheFooter.vue';

export default {
  name: 'TheHeader',
  components: {
    SearchBar,
    TheFooter,
    VDrawer,
  },
  data() {
    return {
      tags: [],
      title: 'The Channel',
      overlay: {
        search: false,
        footer: false,
      },
    };
  },
  computed: {
    overlayActive() {
      return this.overlay.search || this.overlay.footer;
    },
  },
  mounted() {
    this.getTags();
  },
  methods: {
    getTags() {
      axios
        .get('/api/suggestions')
        .then((response) => {
          this.tags = response.data;
        }).catch((err) => {
          this.tags = [];
          console.error(err);
        });
    },
    handleSearchClose() {
      this.overlay.search = false;
    },
    handleFooterClose() {
      this.overlay.footer = false;
    },
    scrollFix(hashbang) {
      window.location.hash = hashbang;
    },
  },
};
</script>

<style>
.vts-drawer__content {
  height: auto;
}
</style>
