<template>
  <div
    class="carousel__slide"
  >
    <UiCard>
      <RouterLink
        :to="{name: 'video', params: {id: id, slug: slug }}"
      >
        <div class="ui-card__thumbnail">
          <img
            :src="thumbnailUrl"
            class="ui-card__thumbnail-image"
            alt=""
          >
        </div>
        <h2 class="ui-card__title">
          <span>{{ title }}</span>
        </h2>
        <span
          v-if="showDate"
          class="ui-card__date"
        >
          {{ new Date(item.date_recorded) | dateFormat('MMM D, YYYY') }}
        </span>
        <RichText
          :classes="['ui-card__description']"
          :text="item.description"
          :truncate="100"
        />
        <Duration :duration="duration" />
      </RouterLink>
    </UiCard>
  </div>
</template>

<script>
import truncate from 'lodash/truncate';
import RichText from './RichText.vue';
import UiCard from './UiCard.vue';

export default {
  components: {
    RichText,
    UiCard,
  },
  props: {
    item: {
      type: Object,
      default() {
        return {};
      },
    },
    showDate: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    description() {
      return truncate(this.item.description, {
        length: 100,
      });
    },
    duration() {
      return this.item.duration;
    },
    id() {
      return this.item.asset_id;
    },
    slug() {
      return this.item.title_slug;
    },
    thumbnailUrl() {
      return `/images/d/medium/${this.item.thumbnailId}.jpg`;
    },
    title() {
      return this.item.title;
    },
  },
};
</script>
