<template>
  <button
    @click="onClick"
  >
    <slot />
  </button>
</template>

<script>
export default {
  name: 'BaseButton',
  props: {
    onClick: {
      type: Function,
      required: true,
    },
  },
};
</script>

<style>

</style>
