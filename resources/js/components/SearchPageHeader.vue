<template>
  <header class="search-header">
    <div class="search-header__inner search-header__inner-layout">
      <h1 :class="headingClasses">
        Search results
      </h1>
      <div
        class="search-header__summary"
      >
        <div v-if="!loading">
          <slot name="summary" />
        </div>
        <div v-else>
          <svg
            width="20px"
            height="20px"
          >
            <path
              d="M 0 0 H 20 V 20 H 0 Z"
              fill="transparent"
              stroke="#4d4b4b"
              stroke-width="2"
              class="svg-square-animate"
            />
          </svg>
        </div>
      </div>
      <div class="search-header__extras">
        <slot name="extras" />
      </div>
    </div>
    <div class="search-header__inner">
      <slot />
    </div>
  </header>
</template>

<script>
export default {
  name: 'SearchPageHeader',
  props: {
    extraClasses: {
      type: Array,
      default() {
        return [];
      },
    },
    loading: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      classes: ['search-header__heading'],
    };
  },
  computed: {
    headingClasses() {
      return [this.classes, ...this.extraClasses];
    },
  },
};
</script>
