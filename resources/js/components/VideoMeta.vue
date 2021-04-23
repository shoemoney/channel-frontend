<template>
  <div class="video-meta">
    <div class="video-meta__inner video-meta__highlighted">
      <slot name="highlighted" />
    </div>
    <div
      v-if="hasContentSlot"
      class="video-meta__inner"
    >
      <slot name="content" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'VideoMeta',
  computed: {
    hasContentSlot() {
      return !!this.$slots.content;
    },
  },
};
</script>
