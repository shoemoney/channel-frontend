<template>
  <div class="searchable-facet">
    <div class="searchable-facet__search-input">
      <VInput
        ref="search"
        v-model="searchTerm"
        :name="inputId"
        label="Type to filter list..."
        :classes="{ text: 'visually-hidden', input: 'form__input form__input--search' }"
        :placeholder="placeholder"
        autocomplete="new-password"
        @focus="placeholder = 'Type something'"
        @blur="placeholder = 'Search to filter list'"
      />
    </div>
    <div
      v-if="noResults"
      class="no-results"
    >
      No results found
    </div>

    <div
      v-for="facet in filteredItems"
      v-else
      :key="facet.label"
      class="search-facet__list"
    >
      <h4 class="visually-hidden search-facet__list-label">
        {{ facet.label }}
      </h4>
      <div
        v-for="item in facet.items"
        ref="list"
        :key="item.key"
        :class="{
          'search-facet__item': true,
          'search-facet__item--active': isActive(getValue(item, facet.type), facet.id)
        }"
      >
        <a
          :href="`/search?${query(facet.id, getValue(item, facet.type))}`"
          class="search-facet__item-link"
          tabindex="0"
          :aria-label="`Add ${getValue(item, facet.type)} to selection`"
          :data-tracking-gtm="`search filter - ${facet.id}`"
          @click.prevent="handleClick($event)"
        >
          <template v-if="!isActive(getValue(item, facet.type), facet.id)">
            <span
              class="search-facet__item-text"
            >{{ getValue(item, facet.type) }}</span>
          </template>
          <template v-else>
            <span
              class="search-facet__item-text"
            >{{ getValue(item, facet.type) }}</span>
            <button
              class="button button--icon search-facet__item-remove"
              :aria-label="`Remove ${getValue(item, facet.type)} from selection`"
            >
              <BaseIcon
                width="36"
                height="36"
                view-box="0 0 36 36"
                icon-name="close-overlay"
                :classes="['icon--close']"
              >
                <CloseIcon />
              </BaseIcon>
            </button>
          </template>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import { VInput } from 'vuetensils';
import matchSorter from 'match-sorter';
import stringifyQuery from '../mixins/stringifyQuery';

export default {
  components: {
    VInput,
  },
  props: {
    facetList: {
      type: Array,
      required: true,
    },
    panelName: {
      type: String,
      required: true,
    },
    activeFacets: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      searchTerm: '',
      noResults: false,
      placeholder: 'Search to filter list',
    };
  },
  computed: {
    filteredItems() {
      const filteredOptions = [];
      const list = this.facetList;
      const query = this.searchTerm.toLowerCase();
      if (query) {
        for (let i = 0; i < list.length; i += 1) {
          const {
            id, items, label, type,
          } = list[i];
          filteredOptions.push({
            id, label, type, items: matchSorter(items, query, { keys: ['key'] }),
          });
        }
        return filteredOptions;
      }
      return list;
    },
    inputId() {
      return `input-${Math.random().toString(12).substring(4, 8)}`;
    },
  },
  watch: {
    filteredItems(value) {
      for (let i = 0; i < value.length; i += 1) {
        if (value[i].items.length < 1) {
          this.noResults = true;
        } else {
          this.noResults = false;
        }
      }
    },
  },
  methods: {
    handleClick(e) {
      const target = e.currentTarget;
      this.$router.push(`${target.pathname}${target.search}`);
      this.$emit('close-panel');
    },
    query(key, value) {
      const param = `${key}=${encodeURIComponent(value)}`;
      let queryStr = stringifyQuery(this.$route.query);
      const routeQuery = this.$route.query;
      // If the query string contains pagination info, remove it
      if (routeQuery.page) {
        queryStr = queryStr.replace(`page=${routeQuery.page}`, '');
      }
      // If the querystring contains the current facet, generate a new one without it.
      if (routeQuery[key] && (routeQuery[key] === value || routeQuery[key].includes(value))) {
        const processed = queryStr.replace(param, '');
        return processed;
      }
      return queryStr === '' ? `${queryStr}${param}` : `${queryStr}&${param}`;
    },
    isActive(value, key) {
      return this.activeFacets[key]
      && (this.activeFacets[key] === value || this.activeFacets[key].includes(value));
    },
    getValue(item, type) {
      return type === 'date' ? new Date(item.key_as_string).getFullYear() : item.key;
    },
  },
};
</script>
