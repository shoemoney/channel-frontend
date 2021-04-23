<template>
  <div id="app" data-id="vue-app">
    <vue-progress-bar />
    <vue-announcer />
    <the-header />
    <transition name="fade">
      <main class="main">
        <router-view ref="routerView" />
      </main>
    </transition>
  </div>
</template>
