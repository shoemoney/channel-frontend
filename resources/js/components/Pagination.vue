<template>
  <nav
    v-if="totalPages > 1"
    class="pagination"
    aria-label="Search results pagination"
  >
    <ul class="pagination__list">
      <li
        class="pagination__list-item"
      >
        <template v-if="prevPageNumber">
          <RouterLink
            class="link link--text"
            :aria-label="`Go to previous page of results`"
            :to="{
              name: 'search',
              query: {
                ...$route.query,
                ...{ page: prevPageNumber }
              }
            }"
          >
            Previous
          </RouterLink>
        </template>
        <template v-else>
          <span
            aria-hidden="true"
            class="link link--text link--disabled"
          >Previous</span>
        </template>
      </li>

      <li
        v-for="i in pageRange"
        :key="i"
        :class="[
          'pagination__list-item',
          'pagination__list-item--numeric',
          {['pagination__list-item--current link--disabled']: i === currentPage}
        ]"
      >
        <RouterLink
          class="link link--text"
          :aria-label="i === currentPage ? `Current page. Page ${i}` : `Go to page ${i}`"
          :aria-current="i === currentPage ? true : false"
          :to="{
            name: 'search',
            query: {
              ...$route.query,
              ...{ page: i }
            }
          }"
        >
          {{ i }}
        </RouterLink>
      </li>

      <li
        class="pagination__list-item"
      >
        <template v-if="nextPageNumber">
          <RouterLink
            class="link link--text"
            :aria-label="`Go to next page of results`"
            :to="{
              name: 'search',
              query: {
                ...$route.query,
                ...{ page: nextPageNumber }
              }
            }"
          >
            Next
          </RouterLink>
        </template>
        <template v-else>
          <span
            aria-hidden="true"
            class="link link--text link--disabled"
          >Next</span>
        </template>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'Pagination',
  filters: {
    capitalize(value) {
      if (!value) return '';
      return value.toString().charAt(0).toUpperCase() + value.slice(1);
    },
  },
  props: {
    totalPages: {
      type: Number,
      default: 0,
    },
    currentPage: {
      type: Number,
      default: 0,
    },
    pagerQuery: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      maxPages: 6,
    };
  },
  computed: {
    pageRange() {
      const max = this.maxPages;
      const range = [];

      if (this.totalPages < max) {
        return this.totalPages;
      }

      const halfMax = (max / 2);
      let start = this.currentPage - halfMax;
      let stop = this.currentPage + halfMax;

      if (this.currentPage <= halfMax) {
        start = 1;
        stop = max;
      }

      if ((this.currentPage + halfMax) > this.totalPages) {
        start = this.totalPages - 6;
        stop = this.totalPages;
      }

      for (let i = start; i <= stop; i += 1) {
        range.push(i);
      }

      return range.sort((a, b) => a - b);
    },
    prevPageNumber() {
      if ((this.currentPage - 1) > 0) {
        return this.currentPage - 1;
      }
      return false;
    },
    nextPageNumber() {
      if ((this.currentPage + 1) <= this.totalPages) {
        return this.currentPage + 1;
      }
      return false;
    },
  },
};
</script>
