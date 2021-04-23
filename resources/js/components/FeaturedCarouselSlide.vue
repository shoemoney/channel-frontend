<template>
  <div class="carousel__slide carousel__slide--featured">
    <UiCard :extra-classes="['ui-card--featured']">
      <RouterLink
        :to="{name: 'video', params: {id: id, slug: slug}}"
        class="ui-card--inner"
      >
        <div class="ui-card__thumbnail">
          <img
            class="ui-card__thumbnail-image"
            :src="thumbnailUrl"
            :alt="title"
          >
          <Duration :duration="duration" />
        </div>
        <article class="ui-card__info">
          <template v-if="quote">
            <q class="ui-card__quote">{{ quote }}</q>
            <div class="ui-card__teaser">
              <h2 class="ui-card__title">
                {{ title }}
              </h2>
            </div>
          </template>
          <template v-else>
            <span class="ui-card__title">{{ title }}</span>
            <div class="ui-card__teaser">
              <h2 class="ui-card__subtitle">
                {{ subtitle }}
              </h2>
              <RichText
                :classes="['ui-card__description']"
                :text="item.description"
                :truncate="100"
              />
            </div>
          </template>
        </article>
      </RouterLink>
    </UiCard>
  </div>
</template>

<script>
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
    quote() {
      return this.item.quote;
    },
    id() {
      return this.item.asset_id;
    },
    slug() {
      return this.item.title_slug;
    },
    subtitle() {
      return this.item.subtitle;
    },
    thumbnailUrl() {
      return `/images/d/large/${this.item.thumbnailId}.jpg`;
    },
    title() {
      return this.item.title;
    },
  },
};
</script>
