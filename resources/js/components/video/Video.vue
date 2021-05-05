<template>
  <div class="container">
    <div
      v-if="video"
      id="start-of-content"
      class="page-wrapper page--video"
    >
      <div class="video-meta__breadcrumb">
        <RouterLink
          v-if="prevRoute"
          class="link--text link--text-secondary"
          :to="prevRoute"
        >
          {{ breadcrumb }}
        </RouterLink>
      </div>
      <header class="video-meta video-meta__header">
        <h1 class="heading heading--primary video-meta__title">
          {{ video.title }}
        </h1>
        <div
          v-show="video.date_recorded"
        >
          <span>Original program date: </span>
          <span class="video-meta__date">{{ new Date(video.date_recorded) | dateFormat('MMM D, YYYY') }}</span>
        </div>
      </header>
      <div
        ref="panels"
        class="panels"
      >
        <div
          ref="videoPlayer"
          class="panel--left"
        >
          <VideoPlayer
            :options="options"
            :retry-sources="retrySources"
            :title="video.title"
            :poster="poster"
            :timecode="timecode"
            :track="track"
            :clip-start="clipStart"
            :clip-end="clipEnd"
            :has-active-clip="isClip && isValidClip"
            @loadedmetadata="onLoadedMetadata"
            @playbackerror="onPlayerError"
            @timeupdate="onTimeUpdate"
            @timecode-reset="onTimecodeReset"
            @remove-clip="onRemoveClip"
            @play="onPlay"
            @pause="onPause"
            @ended="onEnded"
            @seeking="onSeek"
            @error="onError"
          />
        </div>
        <div class="panel--right">
          <BTabs
            v-model="tabIndex"
            class="vp__tabs"
            lazy
          >
            <BTab
              active
              @click="jumpToLowerPanel"
            >
              <template v-slot:title>
                <BaseIcon
                  width="18"
                  height="18"
                  view-box="0 0 24 17"
                  icon-name="info"
                  title="Information about the video"
                >
                  <InfoIcon />
                </BaseIcon>
                <h3
                  class="vp__tabs__label"
                  data-tracking-gtm="video page links"
                >
                  Info
                </h3>
              </template>
              <About
                :description="video.description"
                :date-recorded="video.date_recorded"
                :people="video.speakers"
                :playlists="videoPlaylists"
                :topics="video.topics"
              />
            </BTab>

            <BTab
              class="tab--transcript"
              @click="jumpToLowerPanel"
            >
              <template v-slot:title>
                <BaseIcon
                  width="18"
                  height="18"
                  view-box="0 0 24 17"
                  icon-name="transcript"
                  title="View transcript"
                >
                  <TranscriptIcon />
                </BaseIcon>
                <h3
                  class="vp__tabs__label"
                  data-tracking-gtm="video page links"
                >
                  Transcript
                </h3>
              </template>
              <Transcript
                :title="video.title"
                :error="transcriptError"
                :items="processedTranscript"
                :current-timecode="currentTimecode"
                @update-timecode="updateTimecode"
              />
            </BTab>

            <BTab @click="jumpToLowerPanel">
              <template v-slot:title>
                <BaseIcon
                  width="18"
                  height="18"
                  view-box="0 0 24 17"
                  icon-name="clip"
                  title="Clip this video"
                >
                  <ClipIcon />
                </BaseIcon>
                <h3
                  data-tracking-gtm="video page links"
                  :class="['vp__tabs__label', {'vp__tabs__label--notify': isClip}] "
                >
                  Clip
                </h3>
              </template>
              <ClippingTool
                :clip-start="clipStart"
                :clip-end="clipEnd"
                :has-active-clip="isClip && isValidClip"
                :current-timecode="currentTimecode"
                @update-clip="onUpdateClip"
                @remove-clip="onRemoveClip"
              />
            </BTab>

            <BTab @click="jumpToLowerPanel">
              <template v-slot:title>
                <BaseIcon
                  width="18"
                  height="18"
                  view-box="0 0 24 17"
                  icon-name="share"
                  title="Share the video"
                >
                  <ShareIcon />
                </BaseIcon>
                <h3
                  class="vp__tabs__label"
                  data-tracking-gtm="video page links"
                >
                  Share
                </h3>
              </template>
              <Share
                :title="video.title"
                :date="video.date_recorded"
                :duration="video.duration"
              />
            </BTab>

            <BTab @click="jumpToLowerPanel">
              <template v-slot:title>
                <BaseIcon
                  width="18"
                  height="18"
                  view-box="0 0 24 17"
                  icon-name="related"
                  title="Related videos"
                >
                  <RelatedIcon />
                </BaseIcon>
                <h3
                  class="vp__tabs__label"
                  data-tracking-gtm="video page links"
                >
                  Related
                </h3>
              </template>
              <RelatedContent
                :items="relatedContent"
                :tags="video.tags"
              />
            </BTab>
          </BTabs>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { BTabs, BTab } from 'bootstrap-vue';
import debounce from 'lodash/debounce';
import throttle from 'lodash/throttle';
import getRouteData from '../../mixins/getRouteData';
import About from './About.vue';
import ClippingTool from './ClippingTool.vue';
import RelatedContent from '../RelatedContent.vue';
import Transcript from '../Transcript.vue';
import Share from './Share.vue';
import VideoPlayer from './VideoPlayer.vue';
import { store } from '../../store';

export default {
  name: 'VideoComponent',
  components: {
    About,
    BTabs,
    BTab,
    ClippingTool,
    RelatedContent,
    Share,
    Transcript,
    VideoPlayer,
  },
  mixins: [getRouteData],
  props: {
    id: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      clipStart: null,
      clipEnd: null,
      currentTimecode: 0,
      datastore: process.env.MIX_DATASTORE_URL,
      debouncedResizeObserver: null,
      duration: null,
      hasReachedSticky: false,
      isSticky: false,
      isClip: false,
      options: {
        sources: null,
      },
      playerMarkers: [],
      prevRoute: null,
      relatedContent: [],
      retrySources: null,
      tabIndex: 0,
      timecode: 0,
      track: null,
      transcript: [],
      transcriptLoaded: false,
      transcriptError: false,
      video: null,
    };
  },
  computed: {
    breadcrumb() {
      const routeName = this.prevRoute.name === null || this.prevRoute.name === 'app' ? 'home' : this.prevRoute.name;
      return `Return to ${routeName} page`;
    },
    isValidClip() {
      const start = this.clipStart;
      const end = this.clipEnd;
      if (this.duration === null || (Number.isNaN(end) || Number.isNaN(start)) || start === 0 || end < start || end > this.duration) {
        return false;
      }
      return true;
    },
    processedTranscript() {
      if (!this.transcript) {
        return [];
      }

      const { paragraphs, speakers } = this.transcript;

      return Object.keys(paragraphs).map((id) => {
        const para = paragraphs[id];
        const paraStart = para[0].time;

        if (paraStart === undefined || typeof paraStart !== 'number') {
          return false;
        }

        const paraEnd = para[para.length - 1].time;
        const duration = (paraEnd - paraStart);

        let speaker = '';
        const speakerId = para[0].speaker;
        if (speakerId !== null && Object.hasOwnProperty.call(speakers, speakerId)) {
          speaker = speakers[speakerId].name;
        }

        // VideoJS compatible timecode values.
        const start = paraStart / 1000;
        const end = paraEnd / 1000;

        // Formatted timecode for display.
        const timecode = new Date(paraStart).toISOString().slice(11, -5);

        // Text content.
        const message = para.map((item) => item.value).join(' ');

        return {
          id,
          message,
          speaker,
          start,
          end,
          duration,
          timecode,
        };
      }).filter((item) => item);
    },
    poster() {
      const video = this.video;
      if (!video.thumbnailId) {
        return '';
      }
      return `/images/d/large/${video.thumbnailId}.jpg`;
    },
    transcriptInit() {
      return store.transcriptInit;
    },
    videoPlaylists() {
      const v = this.video;
      return typeof v.in_playlists === 'string' ? [] : v.in_playlists;
    },
  },
  watch: {
    '$route.query.start': function () {
      this.checkRouteClipStatus();
    },
    '$route.query.end': function () {
      this.checkRouteClipStatus();
    },
    video() {
      this.updateVideo();
      this.$announcer.set(`The page for video titled: ${this.video.title}, has loaded`);
      document.title = `${this.video.title} | The Channel `;
      this.$gtm.trackEvent({
        event: 'virtualPageView',
        virtualPageURL: this.$route.fullPath,
        virtualPageTitle: document.title,
        topic: [this.video.topics].join(),
        partOf: this.videoPlaylists.join(),
      });
    },
    transcriptInit(init) {
      if (init && !this.transcriptLoaded) {
        this.fetchTranscript();
      }
    },
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      vm.prevRoute = from;
    });
  },
  beforeRouteUpdate(to, from, next) {
    if (to.path !== from.path) {
      axios.get(`/api${to.path}`).then(({ data }) => {
        this.setData(data);
        next();
      });
    } else {
      next();
    }
  },
  mounted() {
    document.body.classList.add('vp');
    this.debouncedResizeListener = debounce(this.onResize, 1000);
    this.throttledScrollListener = throttle(this.onScroll, 100);
    window.addEventListener('resize', this.debouncedResizeListener);
    window.addEventListener('scroll', this.throttledScrollListener);
    this.checkRouteClipStatus();
  },
  updated() {
    this.$nextTick(() => {
      this.onResize();
    });
  },
  destroyed() {
    document.body.classList.remove('vp');
    window.removeEventListener('resize', this.debouncedResizeListener);
    window.removeEventListener('scroll', this.throttledScrollListener);
  },
  methods: {
    jumpToLowerPanel() {
      if (this.isSticky && !this.hasReachedSticky) {
        window.scrollTo(0, this.$refs.videoPlayer.offsetTop + 24);
      }
    },
    onUpdateClip(start, end) {
      this.clipStart = start;
      if (end < this.duration) {
        this.clipEnd = end;
      }
    },
    onRemoveClip() {
      this.$router.push({ path: this.$route.path }).catch();
    },
    checkRouteClipStatus() {
      const query = this.$route.query;

      if (Object.prototype.hasOwnProperty.call(query, 'start')) {
        this.isClip = true;
        const start = parseInt(query.start, 10);
        this.clipStart = start;

        if (Object.prototype.hasOwnProperty.call(query, 'end')) {
          const end = parseInt(query.end, 10);

          if (end > start) {
            this.clipEnd = end;
          }
        }
      } else {
        this.isClip = false;
      }
    },
    onResize() {
      const playerHeight = window.innerWidth * (9 / 16);
      const condition = window.innerHeight > (playerHeight * 2);
      document.querySelector('html').classList.toggle('is-sticky', condition);
      this.isSticky = condition;
    },
    onScroll() {
      const el = document.querySelector('.panels');
      const { top } = el.getBoundingClientRect();
      const condition = top <= 18;
      document.querySelector('html').classList.toggle('has-reached-sticky', condition);
      this.hasReachedSticky = condition;
    },
    setVideoSource(url) {
      const sources = [{
        src: url,
        type: 'video/mp4',
      }];
      this.options = { ...this.options, sources };
      this.retrySources = null;
    },
    updateVideo() {
      this.setVideoSource(this.video.src);
      this.track = {
        src: `/api/videos/${this.$route.params.id}/transcript?format=vtt`,
        kind: 'captions',
        language: 'en',
        label: 'English',
        default: false,
      };
      this.fetchRelatedContent();
      this.transcript = null;
      this.transcriptLoaded = false;
    },
    setData(data) {
      this.tabIndex = 0;
      this.relatedContent = null;
      this.video = data.video;
    },
    setupObservers() {
      const stickyElm = document.querySelector('.panel--left');
      const observer = new IntersectionObserver(
        this.observerCallback,
        { threshold: [1] },
      );
      observer.observe(stickyElm);
    },
    observerCallback(e) {
      const el = document.querySelector('.panels');
      const condition = e[0].intersectionRatio < 1;
      el.classList.toggle('has-reached-sticky', condition);
      this.hasReachedSticky = condition;
    },
    fetchRelatedContent() {
      axios
        .get(`${this.datastore}videos/${this.$route.params.id}/related`)
        .then((response) => {
          this.relatedContent = response.data.data;
        }).catch((err) => {
          console.error(err);
        });
    },
    fetchTranscript() {
      axios
        .get(`/api/videos/${this.$route.params.id}/transcript?format=json`)
        .then((response) => {
          // Group word level data into paragraph level data.
          const { words, speakers } = response.data;
          const map = new Map(Array.from(words, (obj) => [obj.paragraphId, []]));
          words.forEach((obj) => map.get(obj.paragraphId).push(obj));
          const paragraphs = Array.from(map.values());

          this.transcript = { paragraphs, speakers };
          this.transcriptLoaded = true;
        }).catch((err) => {
          this.transcriptError = true;
          console.error(err);
        });
    },
    dataLayerPush(type, time) {
      this.$gtm.trackEvent({
        event: 'Video',
        category: 'VideoJS',
        action: type,
        label: 'video',
        videoCurrentTime: Math.ceil(parseInt(time, 10)),
      });
    },
    onEnded() {
      this.dataLayerPush('100%', this.currentTimecode);
    },
    onError(e) {
      this.dataLayerPush('Video error', e.message);
    },
    onLoadedMetadata(duration) {
      this.duration = duration;
      if (this.clipEnd > this.duration) {
        this.clipEnd = null;
      }
    },
    onPause() {
      this.dataLayerPush('Paused video', this.currentTimecode);
    },
    onPlay() {
      const playResume = this.currentTimecode < 2 ? 'Played video' : 'Resumed video';
      this.dataLayerPush(playResume, this.currentTimecode);
    },
    onPlayerError() {
      axios
        .get(`/api/video/${this.$route.params.id}/${this.$route.params.slug}`)
        .then((response) => {
          this.retrySources = [{ src: response.data.video.src, type: 'video/mp4' }];
        }).catch((err) => {
          console.error(err);
        });
    },
    onSeek() {
      this.dataLayerPush('Timeline Jump', this.currentTimecode);
    },
    onTimeUpdate(timecode) {
      this.currentTimecode = timecode;
      // Event tracking percentage played.
      const markers = [5, 10, 15, 20, 25, 50, 75, 100];
      const percentPlayed = Math.floor((timecode * 100) / this.duration);
      if (markers.indexOf(percentPlayed) > -1
        && this.playerMarkers.indexOf(percentPlayed) === -1) {
        this.playerMarkers.push(percentPlayed);
        this.dataLayerPush(`${percentPlayed}%`, timecode);
      }
    },
    onTimecodeReset() {
      // If trying to replay a section whilst still within the
      // section, the timecode needs to be reset once set to
      // maintain reactivity.
      this.timecode = 0;
    },
    updateTimecode(timecode) {
      this.timecode = timecode;
    },
  },
};
</script>
