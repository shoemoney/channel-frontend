<template>
  <div class="search-bar__option-content">
    <RouterLink
      v-for="item in items"
      :key="item.id"
      :class="['link--text', 'link--text-secondary', 'link--tag']"
      :to="{ name: 'search', query: item.query }"
      data-tracking-gtm="search menu links"
      @click.native="$emit('tag-selected')"
    >
      <span class="link--tag__text">{{ item.term }}</span>
    </RouterLink>
  </div>
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      default() {
        return [];
      },
    },
  },
};
</script>
