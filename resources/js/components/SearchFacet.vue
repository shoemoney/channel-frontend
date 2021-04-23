<template>
  <div
    class="search-facet__list"
  >
    <div
      v-for="option in facet.items"
      ref="listChildren"
      :key="option.key"
      :class="{'search-facet__item': true, 'search-facet__item--active': isActive(getValue(option, facet.type), facet.id)}"
    >
      <a
        :href="`/search?${query(facet.id, getValue(option, facet.type))}`"
        class="search-facet__item-link"
        tabindex="0"
        :aria-label="`Add ${getValue(option, facet.type)} to selection`"
        :data-tracking-gtm="`search filter - ${facet.id}`"
        @click.prevent="handleClick($event, facet.id, getValue(option, facet.type))"
      >
        <span
          v-if="!isActive(getValue(option, facet.type), facet.id)"
          class="search-facet__item-text"
        >{{ getValue(option, facet.type) }}</span>
        <span
          v-else
          class="search-facet__item-text"
        >{{ getValue(option, facet.type) }}
          <button
            class="button button--icon search-facet__item-remove"
            :aria-label="`Remove ${getValue(option, facet.type)} from selection`"
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
        </span>
      </a>
    </div>
  </div>
</template>

<script>
import stringifyQuery from '../mixins/stringifyQuery';

export default {
  name: 'SearchFacet',
  props: {
    facet: {
      type: Object,
      required: true,
    },
    activeFacets: {
      type: Object,
      required: true,
    },
  },
  mounted() {
    this.$refs.listChildren[0].firstChild.focus();
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
      // If the querystring contains the current facet, genearate a new one without it.
      if (routeQuery[key] && (routeQuery[key] === value || routeQuery[key].includes(value))) {
        const processed = queryStr.replace(param, '');
        return processed;
      }
      return queryStr === '' ? `${queryStr}${param}` : `${queryStr}&${param}`;
    },
    isActive(value, key) {
      return this.activeFacets[key] && (this.activeFacets[key] === value || this.activeFacets[key].includes(value));
    },
    getValue(item, type) {
      return type === 'date' ? new Date(item.key_as_string).getFullYear() : item.key;
    },
  },
};
</script>
