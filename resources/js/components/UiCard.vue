<template>
  <div :class="classes">
    <slot />
  </div>
</template>

<script>
export default {
  name: 'UiCard',
  props: {
    extraClasses: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  computed: {
    classes() {
      return ['ui-card', ...this.extraClasses];
    },
  },
};
</script>
