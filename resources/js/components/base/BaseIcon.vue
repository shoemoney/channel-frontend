<template>
  <svg
    :class="className"
    xmlns="http://www.w3.org/2000/svg"
    :width="width"
    :height="height"
    :viewBox="viewBox"
    :aria-labelledby="iconName"
    role="presentation"
  >
    <title
      :id="iconName"
      lang="en"
    >{{ title }}</title>
    <g :fill="iconColor">
      <slot />
    </g>
  </svg>
</template>

<script>
export default {
  props: {
    classes: {
      type: Array,
      default: () => [],
    },
    iconName: {
      type: String,
      default: '',
    },
    title: {
      type: String,
      default: '',
    },
    width: {
      type: [Number, String],
      default: 36,
    },
    viewBox: {
      type: String,
      default: '0 0 36 36',
    },
    height: {
      type: [Number, String],
      default: 36,
    },
    iconColor: {
      type: String,
      default: 'currentColor',
    },
  },
  computed: {
    className() {
      return ['icon', ...this.classes];
    },
  },
};
</script>
