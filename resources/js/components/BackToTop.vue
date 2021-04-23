<template>
  <transition name="fade">
    <button
      v-show="visible"
      :class="classNames"
      :aria-label="label"
      :tabindex="visible ? 0 : -1"
      @click="handleClick"
    >
      <slot />
    </button>
  </transition>
</template>

<script>
import throttle from 'lodash/throttle';
import getOrientation from '../mixins/getOrientation';

export default {
  name: 'BackToTop',
  props: {
    classes: {
      type: Array,
      default: () => [],
    },
    container: {
      type: String,
      default: '',
    },
    isIOS: {
      type: Boolean,
      default: false,
    },
    label: {
      type: String,
      required: true,
    },
    scrollAnchor: {
      type: String,
      required: true,
    }
  },
  data() {
    return {
      scrollContainer: null,
      throttledScrollListener: null,
      visible: false,
    };
  },
  computed: {
    classNames() {
      return [
        'button',
        'button--to-top',
        ...(this.isIOS ? ['button--safe-area-bottom'] : []),
        ...this.classes
      ];
    },
  },
  watch: {
    container() {
      this.scrollContainer.removeEventListener('scroll', this.throttledScrollListener);
      this.init();
    },
  },
  mounted() {
    this.throttledScrollListener = throttle(this.scrollListener, 600);
    this.init();
  },
  beforeDestroy() {
    this.scrollContainer.removeEventListener('scroll', this.throttledScrollListener);
  },
  methods: {
    handleClick() {
      this.$emit('scroll-top');
    },
    init() {
      if (this.container !== null) {
        this.scrollContainer = document.querySelector(this.container);
      } else {
        this.scrollContainer = window;
      }
      this.scrollContainer.addEventListener('scroll', this.throttledScrollListener);
    },
    scrollListener() {
      if (this.container !== null) {
        this.visible = this.scrollContainer.scrollTop > 150;
      } else {
        const orientation = getOrientation();
        // On landscape devices, show the BackToTop button when the
        // scroll anchor element has gone above the viewport
        this.visible = orientation === 'landscape' && document.querySelector(this.scrollAnchor)
          ? document.querySelector(this.scrollAnchor).getBoundingClientRect().top < 0
          : window.pageYOffset > 350
      }
    },
  },
};
</script>
