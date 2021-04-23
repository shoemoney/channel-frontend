<template>
  <div class="container">
    <div
      id="start-of-content"
      class="page-wrapper page--search"
    >
      <SearchPageHeader
        :loading="loading"
        :extra-classes="['heading', 'heading--primary', 'heading--search']"
      >
        <template #summary>
          <AnimatedNumber
            v-show="total"
            :value="total"
            :duration="400"
            :round="1"
          /><span v-show="total"> {{ total > 1 ? 'results' : 'result' }}</span>
          <div
            v-if="searchTerm"
            class="search-page__summary"
          >
            <span class="search-page__summary__span">for&nbsp;</span>
            <a
              class="search-page__summary__link link link--text link--text-and-button"
              @click="resetSearch"
            >
              <strong>{{ searchTerm }}</strong>
              <button
                class="button button--icon search-facet__item-remove"
                :aria-label="`Clear your query and reset results`"
              >
                <BaseIcon
                  width="36"
                  height="36"
                  view-box="0 0 36 36"
                  icon-name="clear-term"
                >
                  <ClosePinkIcon />
                </BaseIcon>
              </button>
            </a>
          </div>
        </template>
        <template #extras>
          <button
            class="button button--action button--search-toggle button--small-devices"
            @click="toggleSearchFilters"
          >
            {{ 'Search and filter' }}
          </button>
          <VToggle
            transition="slide-down"
            :classes="{
              root: 'search-page__sorting',
              label: 'search-page__sorting-control button',
              content: 'search-page__sorting-content'
            }"
          >
            <template #label>
              <span
                class="search-page__sorting-control-label"
              >
                <span>Sort by</span>
                <BaseIcon
                  width="18"
                  height="18"
                  view-box="0 0 36 36"
                  icon-name="sorting-options"
                  title="Sorting options"
                >
                  <DropdownIcon />
                </BaseIcon>
              </span>
            </template>

            <template #default>
              <ul>
                <li>
                  <RouterLink
                    aria-label="Sort by date from newest to oldest"
                    class="link link--text"
                    :to="{
                      name: 'search',
                      query: {
                        ...$route.query,
                        ...{ sort: 'date_recorded', order: 'desc' }
                      }
                    }"
                  >
                    Date (newest-oldest)
                  </RouterLink>
                </li>
                <li>
                  <RouterLink
                    aria-label="Sort by date from oldest to newest"
                    class="link link--text"
                    :to="{
                      name: 'search',
                      query: {
                        ...$route.query,
                        ...{ sort: 'date_recorded', order: 'asc' }
                      }
                    }"
                  >
                    Date (oldest-newest)
                  </RouterLink>
                </li>
              </ul>
            </template>
          </VToggle>
        </template>
        <CurrentSearch />
      </SearchPageHeader>

      <div class="search-page__content">
        <nav class="search-page__sidebar">
          <transition
            name="slide-in"
            @enter="afterFiltersEnter"
          >
            <div
              v-show="showFilters && hasFacets"
              v-hammer:swipe.left="toggleSearchFilters"
              :class="['search__filters-overlay', {'search__filters-overlay--active': showFilters}]"
              @click.self="toggleSearchFilters"
            >
              <div
                ref="searchFilters"
                v-hammer:swipe.left="toggleSearchFilters"
                :class="['search__filters']"
              >
                <button
                  ref="searchFiltersToggle"
                  class="button button--action button--search-toggle button--small-devices"
                  @click.stop="toggleSearchFilters"
                >
                  {{ 'Hide filters' }}
                </button>
                <div class="search-page__form">
                  <div class="form__input-wrapper">
                    <VInput
                      ref="searchInput"
                      v-model="clonedTerm"
                      :classes="{
                        input: ['form__input', 'form__input--search', 'form__input--light'],
                        text: 'visually-hidden'
                      }"
                      type="text"
                      :name="inputId"
                      label="Search"
                      aria-label="Enter search query"
                      :placeholder="placeholder"
                      @keydown.enter.prevent="submitSearch"
                      @focus="placeholder = 'Type something'"
                      @blur="placeholder = 'Search'"
                    />
                    <div class="form__submit-wrapper">
                      <button
                        :class="['form__submit', 'button', 'button--icon']"
                        @click="submitSearch"
                      >
                        <BaseIcon
                          width="24"
                          height="24"
                          view-box="0 0 24 24"
                          icon-name="search-form"
                          title="Submit search"
                        >
                          <SearchIcon />
                        </BaseIcon>
                      </button>
                    </div>
                  </div>
                </div>

                <h2 class="heading heading--secondary">
                  Filter
                </h2>
                <div
                  v-if="facets"
                  class="search-page__facet-controls"
                >
                  <button
                    aria-owns="topics"
                    data-facet-id="topics"
                    :class="['button',
                             'search-facet__label',
                             {'search-facet__label--active': openFacetName === 'topics'}
                    ]"
                    :disabled="!topicsAndTags.length"
                    @click="toggleFacetOverlay('topics')"
                  >
                    <span aria-label="Search filter: Topics & Tags">Topics & Tags</span>
                  </button>
                  <button
                    aria-owns="people"
                    data-facet-id="people"
                    :class="['button',
                             'search-facet__label',
                             {'search-facet__label--active': openFacetName === 'people'}]"
                    :disabled="!facets.speakers.items.length"
                    @click="toggleFacetOverlay('people')"
                  >
                    <span aria-label="Search filter: People">People</span>
                  </button>
                  <button
                    aria-owns="playlists"
                    data-facet-id="playlists"
                    :class="['button',
                             'search-facet__label',
                             {'search-facet__label--active': openFacetName === 'playlists'}]"
                    :disabled="!facets.in_playlists.items.length"
                    @click="toggleFacetOverlay('playlists')"
                  >
                    <span aria-label="Search filter: Playlists">Playlists</span>
                  </button>
                  <button
                    aria-owns="date"
                    data-facet-id="date"
                    :class="['button',
                             'search-facet__label',
                             {'search-facet__label--active': openFacetName === 'date'}]"
                    :disabled="!facets.date_recorded.items.length"
                    @click="toggleFacetOverlay('date')"
                  >
                    <span aria-label="Search filter: Date">Date</span>
                  </button>
                </div>
              </div>
            </div>
          </transition>
        </nav>

        <div class="search-page__main">
          <transition
            name="slide-out"
            mode="out-in"
            @before-enter="setScrollPosition(false)"
            @enter="setElementHeight('.overlay__inner', '.overlay')"
          >
            <Overlay
              v-if="openFacetName === 'topics'"
              id="topics"
              key="topics"
              @close-panel="toggleFacetOverlay(null)"
            >
              <SearchableFacet
                v-if="facets"
                :active-facets="activeFacets"
                :facet-list="topicsAndTags"
                :panel-name="'topics'"
                @close-panel="toggleFacetOverlay(null)"
              />
            </Overlay>

            <Overlay
              v-if="openFacetName === 'people'"
              id="people"
              key="people"
              @close-panel="toggleFacetOverlay(null)"
            >
              <SearchableFacet
                v-if="facets"
                :active-facets="activeFacets"
                :facet-list="[facets.speakers]"
                :panel-name="'people'"
                @close-panel="toggleFacetOverlay(null)"
              />
            </Overlay>

            <Overlay
              v-if="openFacetName === 'playlists'"
              id="playlists"
              key="playlists"
              @close-panel="toggleFacetOverlay(null)"
            >
              <SearchFacet
                v-if="facets"
                :active-facets="activeFacets"
                :facet="facets.in_playlists"
                @close-panel="toggleFacetOverlay(null)"
              />
            </Overlay>

            <Overlay
              v-if="openFacetName === 'date'"
              id="date"
              key="date"
              @close-panel="toggleFacetOverlay(null)"
            >
              <SearchFacet
                v-if="facets"
                :active-facets="activeFacets"
                :facet="facets.date_recorded"
                @close-panel="toggleFacetOverlay(null)"
              />
            </Overlay>
          </transition>

          <span
            ref="results"
            class="visually-hidden"
            tabindex="0"
          >
            Search results
          </span>

          <div
            v-if="noResults"
            class="no-results"
          >
            <NoResults />
          </div>
          <div
            v-else
            class="search__results"
          >
            <UiGrid>
              <UiCard
                v-for="item in hits"
                :key="item.id"
              >
                <RouterLink
                  :to="{name: 'video', params: {id: item.asset_id, slug: item.title_slug }}"
                  tabindex="0"
                >
                  <div class="ui-card__thumbnail">
                    <img
                      :src="`/images/d/medium/${item.thumbnailId}.jpg`"
                      class="ui-card__thumbnail-image"
                      alt=""
                    >
                  </div>
                  <h2 class="ui-card__title">
                    <span v-html="highlight(item)" />
                  </h2>
                  <div class="ui-card__date">
                    {{ new Date(item.date_recorded) | dateFormat('MMM DD, YYYY') }}
                  </div>
                  <SearchSnippets
                    :snippets="item.snippets"
                  />
                  <Duration :duration="item.duration" />
                </RouterLink>
              </UiCard>
            </UiGrid>

            <Pagination
              v-if="!loading"
              :total-pages="totalPages"
              :current-page="currentPage"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import debounce from 'lodash/debounce';
import AnimatedNumber from 'animated-number-vue';
import { VToggle, VInput } from 'vuetensils/src/components';
import NoResults from './NoResults.vue';
import UiCard from './UiCard.vue';
import UiGrid from './UiGrid.vue';
import SearchSnippets from './SearchSnippets.vue';
import getRouteData from '../mixins/getRouteData';
import stringifyQuery from '../mixins/stringifyQuery';
import Pagination from './Pagination.vue';
import CurrentSearch from './CurrentSearch.vue';
import SearchFacet from './SearchFacet.vue';
import SearchableFacet from './SearchableFacet.vue';
import SearchPageHeader from './SearchPageHeader.vue';
import Overlay from './Overlay.vue';
import { store, mutations } from '../store';

export default {
  name: 'Search',
  components: {
    AnimatedNumber,
    CurrentSearch,
    NoResults,
    Pagination,
    SearchableFacet,
    SearchSnippets,
    SearchFacet,
    SearchPageHeader,
    Overlay,
    UiCard,
    UiGrid,
    VInput,
    VToggle,
  },
  mixins: [getRouteData],
  props: {
    facetQuery: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      openFacetName: null,
      clearedSortQuery: null,
      clearPageQuery: null,
      currentQuery: null,
      facets: null,
      loading: false,
      noResults: false,
      pager: null,
      showFilters: false,
      clonedTerm: '',
      title: null,
      totals: null,
      videos: null,
      debouncedResize: null,
      initialWidth: 0,
      placeholder: 'Search',
    };
  },
  computed: {
    activeFacets() {
      const query = this.$route.query;
      const allowed = ['topics', 'tags', 'speakers', 'date_recorded', 'in_playlists'];
      return Object.keys(query)
        .filter((key) => allowed.includes(key))
        .reduce((obj, key) => {
          obj[key] = query[key];
          return obj;
        }, {});
    },
    hasFacets() {
      return !!this.facets;
    },
    hits() {
      return this.videos;
    },
    hitInfo() {
      return this.total;
    },
    inputId() {
      return `input-${Math.random().toString(12).substring(4, 8)}`;
    },
    topicsAndTags() {
      return [this.facets.topics, this.facets.tags];
    },
    total() {
      return this.totals ? this.totals.total : 0;
    },
    currentPage() {
      return this.totals ? this.totals.currentPage : null;
    },
    totalPages() {
      return this.totals ? this.totals.totalPages : null;
    },
    facetOverlayActive() {
      return store.facetOverlayActive;
    },
    searchTerm() {
      return store.searchTerm;
    },
  },
  watch: {
    $route: {
      immediate: true,
      handler(to) {
        if (to.query) {
          this.getPageData(stringifyQuery(to.query));
          let pageTitle = `Search | the channel `;

          if (to.query.term) {
            const term = to.query.term;
            this.setSearchTerm(term);
            this.$announcer.set(`Search results for ${term}. Page loaded with ${this.total} results.`);
            pageTitle = `Search results for ${term} | the channel `;
            this.$gtm.trackEvent({
              event: 'videoSearch',
              searchTerm: term,
              searchResults: this.total,
              searchTopic: this.activeFacets.topics ? [this.activeFacets.topics].join() : null,
              searchTag: this.activeFacets.tags ? [this.activeFacets.tags].join() : null,
              searchPeople: this.activeFacets.speakers ? [this.activeFacets.speakers].join() : null,
              searchDate: this.activeFacets.date_recorded ? [this.activeFacets.date_recorded].join() : null,
              searchPlaylist: this.activeFacets.in_playlists ? [this.activeFacets.in_playlists].join() : null,
            });
          } else {
            this.setSearchTerm('');
            this.$announcer.set(`Search results page loaded`);
          }

          document.title = pageTitle;
          this.$gtm.trackEvent({
            event: 'virtualPageView',
            virtualPageURL: to.fullPath,
            virtualPageTitle: document.title,
          });
        }
      },
    },
    showFilters(active) {
      if (window.innerWidth < 960 && active) {
        document.addEventListener('keydown', this.toggleSearchFilters);
      } else {
        document.removeEventListener('keydown', this.toggleSearchFilters);
      }
    },
    videos(to) {
      this.noResults = to && to.length === 0;
    },
  },
  mounted() {
    this.initialWidth = window.innerWidth;
    this.showFilters = this.initialWidth >= 960;
    this.debouncedResize = debounce(this.handleResize, 200).bind(this);
    window.addEventListener('resize', this.debouncedResize, false);
  },
  beforeDestroy() {
    window.addEventListener('resize', this.debouncedResize, false);
  },
  methods: {
    setSearchTerm: mutations.setSearchTerm,
    toggleFacetOverlayActive: mutations.toggleFacetOverlayActive,
    getPageData(params = '') {
      this.videos = null;
      this.loading = true;
      axios
        .get(`/api/search${params}`)
        .then((response) => {
          this.setVars(response);
          this.loading = false;
        }).catch((err) => {
          this.loading = false;
          console.error(err);
        });
    },
    handleResize() {
      if (this.initialWidth !== window.innerWidth && window.innerWidth >= 960) {
        this.showFilters = true;
        if (this.facetOverlayActive && !this.openFacetName) {
          this.toggleFacetOverlayActive();
        }
      } else if (this.showFilters && !this.facetOverlayActive) {
        this.showFilters = false;
      }

      if (this.openFacetName) {
        this.setElementHeight('.overlay__inner', '.overlay');
      }
    },
    highlight(item) {
      if (this.searchTerm) {
        const div = document.createElement('div');
        div.innerText = this.searchTerm;
        const escapedTerm = div.innerHTML;
        const regex = new RegExp(`\\b(${escapedTerm.replace(/([-/\\^$*+?.()|[\]{}])/ig, '\\$&')})\\b`, 'i');
        return item.title.replace(regex, '<span class="ui-card__highlight">$1</span>');
      }
      return item.title;
    },
    resetSearch() {
      this.$router.push({ name: 'search' }).catch(() => {});
      this.clonedTerm = '';
      this.totals = null;
      this.setSearchTerm('');
    },
    submitSearch(e) {
      if (window.innerWidth < 960) {
        this.toggleSearchFilters(e);
      }
      let searchParams = {};
      if (this.clonedTerm) {
        searchParams = { term: this.clonedTerm };
      }
      this.$router.push({ name: 'search', query: searchParams }).catch(() => {});
      this.$refs.searchInput.$refs.input.blur();
      this.$refs.results.focus();
      this.clonedTerm = '';
    },
    setScrollPosition(useExisting) {
      if (useExisting) {
        document.documentElement.scrollTop = this.scrollTop;
      }
      this.scrollTop = document.documentElement.scrollTop;
    },
    setElementHeight(selector, wrapper) {
      document.documentElement.scrollTop = this.scrollTop;
      const el = document.querySelector(selector);
      const container = document.querySelector(wrapper);
      const offsetTop = el.getBoundingClientRect().top;
      const headerHeight = 72;
      if (window.innerWidth >= 960) {
        el.style.height = `${window.innerHeight - offsetTop}px`;
        if (offsetTop < headerHeight) {
          el.style.height = `${window.innerHeight - headerHeight}px`;
          const top = offsetTop > 0 ? headerHeight - Math.abs(offsetTop) : headerHeight - offsetTop;
          container.style.top = `${top}px`;
        }
      } else {
        el.style.height = '';
      }
    },
    // Set component data when a response is fetched.
    setVars(response) {
      const data = response.data;
      this.totals = data.totals;
      this.title = data.title;
      this.pager = data.totals.pager;
      this.videos = data.videos;
      this.facets = data.facets;
      this.clearPageQuery = data.clearedPageQuery;
      this.clearedSortQuery = data.clearedSortQuery;
      this.currentQuery = data.currentQuery;
    },
    toggleFacetOverlay(name) {
      if (this.openFacetName === null || name === null) {
        if (window.innerWidth >= 960) {
          this.toggleFacetOverlayActive();
        }
      }

      if (this.openFacetName === name) {
        this.openFacetName = null;

        if (window.innerWidth >= 960) {
          this.toggleFacetOverlayActive();
        }
      } else {
        this.openFacetName = name;
      }
    },
    afterFiltersEnter() {
      this.$refs.searchFiltersToggle.focus();
    },
    toggleSearchFilters(event) {
      // Prevents escape key from also closing the main filter panel when
      // a facet list is open on top of it.
      if (this.openFacetName) return;

      if (event.type === 'click'
        || (event.type === 'keydown' && (event.which === 27))
      ) {
        this.showFilters = !this.showFilters;
        this.toggleFacetOverlayActive();
      }

      if (event.type === 'swipeleft') {
        this.showFilters = false;
      }
    },
  },
};
</script>
