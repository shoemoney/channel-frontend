<template>
  <VideoMeta>
    <template v-slot:highlighted>
      <div
        v-if="playlists.length"
        class="ui-table"
      >
        <h4 class="ui-list__title">
          Part of
        </h4>
        <ul
          class="ui-list"
        >
          <li
            v-for="item in playlists"
            :key="item"
            class="ui-list__item"
          >
            <RouterLink
              :class="['link', 'link--text', 'link--text-secondary']"
              :to="{name: 'search', query: { in_playlists: item } }"
            >
              {{ item }}
            </RouterLink>
          </li>
        </ul>
      </div>
      <div
        v-if="people.length"
        class="ui-table"
      >
        <h4 class="ui-list__title">
          Featuring
        </h4>
        <ul
          class="ui-list"
        >
          <li
            v-for="item in people"
            :key="item"
            class="ui-list__item"
          >
            <RouterLink
              :class="['link', 'link--text', 'link--text-secondary']"
              :to="{name: 'search', query: { speakers: item } }"
            >
              {{ item }}
            </RouterLink>
          </li>
        </ul>
      </div>
      <div
        v-if="topics.length"
        class="ui-table"
      >
        <h4 class="ui-list__title">
          Topics
        </h4>
        <ul
          class="ui-list"
        >
          <li
            v-for="item in topics"
            :key="item"
            class="ui-list__item"
          >
            <RouterLink
              :class="['link', 'link--text', 'link--text-secondary']"
              :to="{name: 'search', query: { topics: item } }"
            >
              {{ item }}
            </RouterLink>
          </li>
        </ul>
      </div>
    </template>
    <template v-slot:content>
      <RichText
        :classes="['video-meta__description']"
        :text="description"
        trusted
      />
    </template>
  </VideoMeta>
</template>

<script>
import RichText from '../RichText.vue';
import VideoMeta from '../VideoMeta.vue';

export default {
  name: 'About',
  components: {
    RichText,
    VideoMeta,
  },
  props: {
    dateRecorded: {
      type: String,
      default: '',
    },
    description: {
      type: String,
      default: '',
    },
    people: {
      type: Array,
      default() {
        return [];
      },
    },
    playlists: {
      type: Array,
      default() {
        return [];
      },
    },
    topics: {
      type: Array,
      default() {
        return [];
      },
    },
  },
};
</script>
