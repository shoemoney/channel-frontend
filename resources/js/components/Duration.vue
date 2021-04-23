<template>
  <time
    class="ui-card__duration"
    :datetime="durationString"
  >{{ duration }}</time>
</template>

<script>
export default {
  name: 'Duration',
  props: {
    duration: {
      type: String,
      required: true,
    },
  },
  computed: {
    durationString() {
      // See https://www.w3.org/TR/2014/REC-html5-20141028/infrastructure.html#valid-duration-string
      const [hours, mins, secs] = this.duration.split(':');
      return `PT${hours}H${mins}M${secs}`;
    },
  },
};
</script>
