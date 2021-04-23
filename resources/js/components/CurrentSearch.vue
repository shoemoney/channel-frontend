<template>
  <div
    class="search-page__current-search"
  >
    <div
      v-for="item in currentFacets"
      :key="item.value"
      :class="{'search-facet__item': true, 'search-facet__item--active': true}"
    >
      <a
        :href="`/search?${query(item.key, item.value)}`"
        class="link link--text link--text-and-button link--text-and-button-underline"
        :aria-label="`Remove ${item.value} filter from selection`"
        @click.prevent="handleClick($event)"
      >
        <strong>{{ item.value }}</strong>
        <span
          class="search-facet__item-remove"
        >
          <BaseIcon
            width="36"
            height="36"
            view-box="0 0 36 36"
            icon-name="remove-item"
          >
            <ClosePinkIcon />
          </BaseIcon>
        </span>
      </a>
    </div>
  </div>
</template>

<script>
import stringifyQuery from '../mixins/stringifyQuery';

export default {
  name: 'CurrentSearch',
  computed: {
    filteredQuery() {
      const query = this.$route.query;
      const allowed = ['topics', 'tags', 'speakers', 'date_recorded', 'in_playlists'];
      return Object.keys(query)
        .filter((key) => allowed.includes(key))
        .reduce((obj, key) => {
          obj[key] = query[key];
          return obj;
        }, {});
    },
    currentFacets() {
      const active = [];
      const query = this.filteredQuery;
      Object.keys(query).forEach((key) => {
        const value = query[key];
        if (value instanceof Array) {
          value.forEach((item) => {
            active.push({
              key,
              value: item,
            });
          });
        } else {
          active.push({
            key,
            value,
          });
        }
      });

      return [].concat(...active);
    },
  },
  methods: {
    handleClick(e) {
      const target = e.currentTarget;
      this.$router.push(`${target.pathname}${target.search}`);
    },
    query(key, value) {
      const param = `${key}=${encodeURIComponent(value)}`;
      let queryStr = stringifyQuery(this.$route.query);
      const routeQuery = this.$route.query;

      // If the query string contains pagination info, remove it
      if (routeQuery.page) {
        queryStr = queryStr.replace(`page=${routeQuery.page}`, '');
      }
      // If the querystring contains the current facet, genearate a new one without it.
      if (routeQuery[key] && (routeQuery[key] === value || routeQuery[key].includes(value))) {
        let processed = queryStr.replace(param, '');
        if (processed.slice(-1) === '&') {
          processed = processed.slice(0, -1);
        }
        return processed;
      }
      return queryStr === '' ? `${queryStr}${param}` : `${queryStr}&${param}`;
    },
    getValue(item, type) {
      return type === 'date' ? new Date(item.key_as_string).getFullYear() : item.key;
    },
  },
};
</script>
