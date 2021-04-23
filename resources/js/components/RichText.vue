<template>
  <p
    :class="classes"
    v-html="processed"
  />
</template>

<script>
export default {
  name: 'RichText',
  props: {
    classes: {
      type: Array,
      default() {
        return [];
      },
    },
    text: {
      type: String,
      default() {
        return '';
      },
    },
    trusted: {
      type: Boolean,
      default() {
        return false;
      },
    },
    truncate: {
      type: Number,
      default() {
        return 0;
      },
    },
  },
  computed: {
    processed() {
      const text = this.text;
      if (!this.trusted) {
        return this.truncateString(this.strip(text), this.truncate);
      }
      return this.truncateString(text, this.truncate);
    },
  },
  methods: {
    // We're removing known HTML.
    strip(str) {
      return str.replace(/<(.|\n)*?>/g, '');
    },
    truncateString(str, length) {
      const ending = '...';
      if (length > 0 && str.length > length) {
        return str.substring(0, length - ending.length) + ending;
      }
      return str;
    },
  },
};
</script>
