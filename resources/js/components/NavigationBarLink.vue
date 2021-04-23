<template>
  <a
    :href="href | filterId | anchor"
    :class="['link', {'link--active': activeItem === item }]"
  >{{ item }}</a>
</template>

<script>
export default {
  name: 'NavigationBarLink',
  filters: {
    filterId(value) {
      return value.replace(/[\s&]/gi, '').toLowerCase();
    },
    anchor(value) {
      return `#${value}`;
    },
  },
  props: {
    href: {
      type: Object,
      default() {
        return {};
      },
    },
    text: {
      type: String,
      default() {
        return '';
      },
    },
    classes: {
      type: Array,
      default() {
        return [];
      },
    },
  },
};
</script>
