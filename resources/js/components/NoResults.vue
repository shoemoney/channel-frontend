<template>
  <div class="search__no-results">
    <span class="label">
      There are no results that match your criteria.
    </span>
    <transition name="fade">
      <div
        v-show="tagItems"
        class="search__onward"
      >
        <span class="search-bar__option-label">Try:</span>
        <TagGroup :items="tagItems" />
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';
import TagGroup from './TagGroup.vue';

export default {
  components: {
    TagGroup,
  },
  data() {
    return {
      tagItems: null,
    };
  },
  mounted() {
    this.getSuggestions();
  },
  methods: {
    getSuggestions() {
      axios
        .get('/api/suggestions')
        .then((response) => {
          this.tagItems = response.data;
        }).catch((err) => {
          // @todo Log these rather than swallow?
        });
    },
  },
};
</script>
