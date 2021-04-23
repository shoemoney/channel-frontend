<template>
  <div class="related-content__wrapper">
    <Carousel
      v-if="items && items.length"
      id="related"
      :classes="['carousel--related-content']"
      :options="{}"
      title="Related content"
      :controls="true"
      :show-heading="false"
    >
      <CarouselSlide
        v-for="item in items"
        :key="item.id"
        :item="item"
        show-date
        class="ui-card--dark-mode"
      />
    </Carousel>
    <VideoMeta>
      <template #highlighted>
        <div
          v-if="tags && tags.length"
          class="ui-table"
        >
          <h4 class="ui-list__title">
            or try
          </h4>
          <ul
            class="ui-list"
          >
            <li
              v-for="item in tags"
              :key="item"
              class="ui-list__item"
            >
              <RouterLink
                :class="['link', 'link--text', 'link--text-secondary']"
                :to="{name: 'search', query: { tags: item } }"
              >
                {{ item }}
              </RouterLink>
            </li>
          </ul>
        </div>
      </template>
    </VideoMeta>
  </div>
</template>

<script>
import Carousel from './Carousel.vue';
import CarouselSlide from './CarouselSlide.vue';
import VideoMeta from './VideoMeta.vue';

export default {
  name: 'RelatedContent',
  components: {
    Carousel,
    CarouselSlide,
    VideoMeta,
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
    tags: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style>
.carousel--related-content .carousel__slide {
  width: 75%;
}
</style>
