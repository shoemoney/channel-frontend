<template>
  <div
    :class="navigationClasses"
    aria-hidden="true"
  >
    <Flickity
      v-if="items && Object.keys(items).length > 0"
      ref="flickity"
      :options="flickityOptions"
      role="navigation"
      aria-label="List of video topics"
      class="navigation-bar__inner"
      @init="initNavigationBar"
    >
      <div
        v-for="({id, label}) in items"
        :key="id"
        :data-selector="id"
        class="navigation-bar__item"
      >
        <a
          href="#"
          data-tracking-gtm="home filters"
          :aria-label="`Topic ${label}`"
          :class="['link', {'link--active': activeItem === id }]"
          tabindex="-1"
          @click.prevent="scrollTo(id)"
        >{{ label }}</a>
      </div>
      <div
        class="navigation-bar__item"
      >
        <a
          href="#"
          tabindex="-1"
          :class="['link']"
          @click.prevent="scrollToTop()"
        >Back to top
          <BaseIcon
            width="36"
            height="36"
            view-box="0 0 36 36"
            icon-name="back-to-top"
            title="Back to top"
            :classes="['icon--nav-bar-link']"
          >
            <NextIcon />
          </BaseIcon>
        </a>
      </div>
    </Flickity>
  </div>
</template>

<script>
import Flickity from 'vue-flickity';

export default {
  name: 'NavigationBar',
  components: {
    Flickity,
  },
  filters: {
    filterId(value) {
      return value.replace(/[\s&]/gi, '').toLowerCase();
    },
    anchorLink(value) {
      return `#${value}`;
    },
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
    activeItem: {
      type: String,
      default: () => '',
    },
    classes: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      flickityOptions: {
        freeScroll: true,
        wrapAround: false,
        contain: false,
        prevNextButtons: false,
        pageDots: false,
        cellAlign: 'left',
        accessibility: false,
      },
    };
  },
  computed: {
    navigationClasses() {
      return ['navigation-bar', ...this.classes];
    },
  },
  watch: {
    activeItem(item) {
      if (item) {
        this.selectNavigationItem(item);
      }
    },
  },
  methods: {
    scrollTo(id) {
      const offset = -85;
      this.$scrollTo(`#${id}`, 0, { offset });
    },
    scrollToTop() {
      this.$refs.flickity.selectCell(0, false);
      this.$scrollTo(`body`, 0);
    },
    selectNavigationItem(item) {
      this.$refs.flickity.selectCell(`[data-selector="${item}"]`, false);
    },
    initNavigationBar() {
      const self = this;

      this.$refs.flickity.on('staticClick', (event, pointer, cellElement, cellIndex) => {
        if (typeof cellIndex === 'number') {
          self.$refs.flickity.selectCell(cellIndex);
        }
      });

      setTimeout(() => {
        this.$refs.flickity.resize();
      }, 1000);
    },
  },
};
</script>
