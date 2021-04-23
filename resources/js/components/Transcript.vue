<template>
  <VideoMeta>
    <template v-slot:highlighted>
      <div
        class="video-meta__transcript"
      >
        <template v-if="items.length">
          <div class="transcript__options">
            <button
              class="button button--action highlighter-toggle"
              aria-haspopup="true"
              :aria-expanded="highlightControlsActive ? 'true' : 'false'"
              data-tracking-gtm="video page links"
              @click="handleHighlighterToggle"
            >
              <span class="visually-hidden">Search within the transcript</span>
              <BaseIcon
                width="36"
                height="36"
                icon-name="search-transcript"
                title="Search within the transcript"
              >
                <SearchIcon />
              </BaseIcon>
            </button>
            <button
              class="button button--action"
              data-tracking-gtm="video page links"
              @click="initDownload"
            >
              <span class="download__title">Download transcript</span>
            </button>
          </div>
          <HighlightText
            :offset-right="scrollBarWidth"
            :show-highlighter="highlightControlsActive"
            @toggle-highlighter="handleHighlighterToggle"
            @scroll-to="handleHighlighterScroll"
          >
            <div
              ref="transcriptContent"
              class="transcript__content"
              tabindex="0"
            >
              <span
                id="transcript-anchor"
                class="visually-hidden"
              >Transcript content</span>
              <p
                v-for="item in items"
                :key="item.id"
                class="transcript__paragraph"
                :class="{
                  'transcript__paragraph--active': isActive(item.start, item.end, item.id)
                }"
              >
                <VTooltip
                  focus
                  tag="div"
                  :classes="{ toggle: 'tooltip--transcript', content: 'tooltip__content' }"
                >
                  <template #tooltip>
                    <button
                      :aria-label="`Go to ${item.timecode}`"
                      class="button button--light"
                      data-tracking-gtm="video page links"
                      @mousedown="handleTranscriptClick(item.start)"
                    >
                      <BaseIcon
                        width="18"
                        height="18"
                        view-box="0 0 448 512"
                        icon-name="play-from-position"
                        :title="`Play from ${item.timecode}`"
                      >
                        <PlayIcon />
                      </BaseIcon>
                      <time>{{ item.timecode }}</time>
                    </button>
                  </template>
                  {{ item.message }}
                </VTooltip>
              </p>
            </div>
          </HighlightText>
        </template>
        <template
          v-else-if="error"
        >
          Sorry, the transcript for this video failed to load correctly.
        </template>
        <template
          v-else
        >
          <div class="loading-icon">
            <svg
              viewBox="0 0 60 60"
            >
              <path
                d="M 0 0 H 60 V 60 H 0 Z"
                fill="transparent"
                stroke="white"
                stroke-width="4"
                class="svg-square-animate"
              />
            </svg>
          </div>
        </template>

        <BackToTop
          label="Go to top of transcript"
          :container="transcriptScrollContainer"
          scrollAnchor="#transcript-anchor"
          :isIOS="ios"
          @scroll-top="handleBackToTopScroll"
        >
          <span class="visually-hidden">Go to top of transcript</span>
          <BaseIcon
            width="36"
            height="36"
            view-box="0 0 36 36"
            icon-name="back-to-top-transcript"
            title="Back to top of transcript"
          >
            <NextIcon />
          </BaseIcon>
        </BackToTop>
      </div>
    </template>
  </VideoMeta>
</template>

<script>
import { saveAs } from 'file-saver';
import scrollIntoView from 'scroll-into-view';
import { vueWindowSizeMixin } from 'vue-window-size';
import { VTooltip } from 'vuetensils/src/components';
import HighlightText from './HighlightText.vue';
import BackToTop from './BackToTop.vue';
import isIos from '../mixins/isIos';
import { store, mutations } from '../store';

export default {
  name: 'Transcript',
  components: {
    BackToTop,
    HighlightText,
    VTooltip,
  },
  mixins: [vueWindowSizeMixin],
  props: {
    currentTimecode: {
      type: Number,
      default() {
        return 0;
      },
    },
    error: {
      type: Boolean,
      default: false,
    },
    items: {
      type: Array,
      default() {
        return [];
      },
    },
    title: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      currentPara: null,
      currentHighlight: null,
      ios: false,
      scrollInProgress: false,
      highlightControlsActive: false,
      scrollBarWidth: 16,
    };
  },
  computed: {
    clean() {
      return store.transcriptInit;
    },
    highlighterOffset() {
      return this.windowWidth < 840 ? ((window.innerHeight / 2) + 40) * -1 : -120;
    },
    transcriptHasLoaded() {
      return this.items.length;
    },
    transcriptScrollContainer() {
      if (this.windowWidth >= 840) {
        return '.tab--transcript .video-meta__inner';
      }
      return null;
    },
  },
  mounted() {
    this.toggleTranscriptInit();
    this.ios = isIos();
  },
  destroyed() {
    this.toggleTranscriptInit();
  },
  methods: {
    handleHighlighterToggle() {
      this.highlightControlsActive = !this.highlightControlsActive;
      const condition = (this.windowWidth < 840 || this.ios) && !this.highlightControlsActive;
      document.querySelector('html').classList.toggle('is-sticky', condition);
      // Get the width of the scrollbar to make sure it isn't covered
      const scrollContainer = document.querySelector('.video-meta__inner.video-meta__highlighted');
      this.scrollBarWidth = scrollContainer ? scrollContainer.offsetWidth - scrollContainer.clientWidth : 16;
      // Having to workaround iOS fixed positioning oddities
      // Only when closing the highlighter input.
      if (this.ios) {
        this.$root.$el.classList.toggle('highlighter--top');
        const offsetHeight = window.innerHeight / 2.667;
        const current = this.currentHighlight;
        const offsetParent = current.offsetParent ? current.offsetParent : null;
        const offset = offsetParent ? offsetParent.offsetTop - offsetHeight : 0;
        window.scrollTo(0, offset);
      }
    },
    handleHighlighterScroll({ el, keyboardBlurred }) {
      const offset = keyboardBlurred ? 100 : 0;
      this.currentHighlight = el;
      this.handleScrollTo(el, offset);
    },
    handleScrollTo(el, offset) {
      const width = this.windowWidth;
      scrollIntoView(el, {
        time: 0,
        align: {
          top: 0.5,
          topOffset: offset,
        },
        validTarget(target) {
          return width < 840 ? true : target !== window;
        },
      });
    },
    handleBackToTopScroll() {
      this.handleScrollTo(document.querySelector('#transcript-anchor'), 0);
    },
    handleTranscriptClick(timecode) {
      this.$emit('update-timecode', timecode);
    },
    initDownload() {
      const output = this.items.map((el) => `${el.message}${'\r\n\r\n'}`);
      const blob = new Blob(output, { type: 'text/plain;charset=utf-8' });
      saveAs(blob, `transcript_${this.title.replace(/[^a-z0-9]/gi, '-').toLowerCase()}.txt`);
    },
    isActive(start, end, paraId) {
      if (this.currentTimecode >= start && this.currentTimecode <= end) {
        this.currentPara = paraId;
        return true;
      }
      return false;
    },
    toggleTranscriptInit: mutations.toggleTranscriptInit,
  },
};
</script>
