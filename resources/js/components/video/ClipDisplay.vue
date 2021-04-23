<template functional>
  <div class="clip-display">
    <h4 class="clip-display__heading">
      Clip information
    </h4>
    <p class="clip-display__content">
      <slot name="description" />
    </p>
    <slot name="controls" />
  </div>
</template>
