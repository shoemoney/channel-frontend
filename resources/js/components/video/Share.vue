<template>
  <VideoMeta>
    <template v-slot:highlighted>
      <div class="share-buttons">
        <a
          class="share-button button button--icon"
          :href="facebook"
          target="blank"
          aria-label="Share on Facebook"
          data-tracking-gtm="video page links"
        >
          <BaseIcon
            width="36"
            height="36"
            view-box="0 0 512 512"
            icon-name="facebook"
            title="Share on facebook"
            :class="['icon--reverse']"
          >
            <FacebookIcon />
          </BaseIcon>
        </a>
        <a
          class="share-button button button--icon"
          :href="twitter"
          target="blank"
          aria-label="Share on Twitter"
          data-tracking-gtm="video page links"
        >
          <BaseIcon
            width="36"
            height="36"
            view-box="0 0 512 512"
            icon-name="twitter"
            title="Share on Twitter"
            :class="['icon--reverse']"
          >
            <TwitterIcon />
          </BaseIcon>
        </a>
        <button
          class="share-button button button--icon"
          aria-label="Get citation for video"
          data-tracking-gtm="video page links"
          @click="showCitation = !showCitation"
        >
          <BaseIcon
            width="36"
            height="36"
            view-box="0 0 448 512"
            icon-name="cite"
            title="Cite"
            :class="['icon--reverse']"
          >
            <CiteIcon />
          </BaseIcon>
          <span class="icon-text">Cite</span>
        </button>
      </div>
    </template>
    <template v-slot:content>
      <transition name="fade">
        <div
          v-show="showCitation"
          class="citation"
        >
          <h4 class="video-meta__label">
            Citation
          </h4>
          <p
            id="citation"
            name="citation"
            class="citation__content"
          >
            {{ citation }}
          </p>
          <div class="citation__copy">
            <button
              :class="['button', 'button--action']"
              aria-label="Copy citation to clipboard"
              data-tracking-gtm="video page links"
              @click="copyToClipboard(citation)"
            >
              <transition
                name="fade"
                mode="out-in"
                :duration="100"
              >
                <span :key="copied">{{ copied ? 'Copied' : 'Copy citation to clipboard' }}</span>
              </transition>
            </button>
          </div>
        </div>
      </transition>
    </template>
  </VideoMeta>
</template>

<script>
import VideoMeta from '../VideoMeta.vue';
import CopyTo from '../../mixins/copyToClipboard';

export default {
  name: 'Share',
  components: {
    VideoMeta,
  },
  mixins: [CopyTo],
  props: {
    date: {
      type: String,
      required: true,
    },
    duration: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      providers: {
        twitter: 'https://twitter.com/intent/tweet/?url=:url&text=:text',
        facebook: 'https://www.facebook.com/sharer/sharer.php?u=:u&title=:title',
      },
      showCitation: false,
    };
  },
  computed: {
    dateFormatted() {
      return this.$options.filters.dateFormat(new Date(this.date), 'MMMM DD, YYYY');
    },
    name() {
      return 'The Channel video';
    },
    text() {
      return this.title;
    },
    url() {
      return window.location.href;
    },
    citation() {
      return `"${this.title}", ${this.name}, ${this.duration}, ${this.dateFormatted}, ${this.url}`;
    },
    facebook() {
      return this.providers.facebook.replace(':u', encodeURIComponent(this.url)).replace(':title', encodeURIComponent(this.text));
    },
    twitter() {
      return this.providers.twitter.replace(':url', encodeURIComponent(this.url)).replace(':text', encodeURIComponent(this.text));
    },
  },
};
</script>
