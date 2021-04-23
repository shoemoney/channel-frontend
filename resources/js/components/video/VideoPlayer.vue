<template>
  <div class="video-player__wrapper">
    <div class="video-player__wrapper-inner">
      <div
        ref="videoPlayerContainer"
        class="video-player__container vjs-hd"
      >
        <video
          ref="videoPlayer"
          class="video-js video-player vjs-default-skin"
          :poster="poster"
          playsinline
        >
          <p class="vjs-no-js">
            To view this video please enable JavaScript, and consider upgrading to a
            web browser that
            <a
              href="https://videojs.com/html5-video-support/"
              target="_blank"
            >
              supports HTML5 video
            </a>
          </p>
        </video>
      </div>
    </div>
    <ClipDisplay
      v-if="!isEmbed && hasActiveClip"
    >
      <template #description>
        {{ clipString }}
        <button
          class="button button--icon search-facet__item-remove"
          aria-label="Remove clip"
          @click="handleRemoveClip"
        >
          <BaseIcon
            width="12"
            height="12"
            view-box="0 0 36 36"
            icon-name="remove-clip"
            title="Remove clip"
            :classes="['icon--close']"
          >
            <CloseIcon />
          </BaseIcon>
        </button>
      </template>
      <template
        #controls
      >
        <template v-if="isClipEndState">
          This clip has ended.
          <button
            class="button button--action"
            @click="handleRemoveClip"
          >
            Continue playing
          </button>
          <button
            class="button button--action"
            @click="replayClip"
          >
            Replay clip
          </button>
        </template>
      </template>
    </ClipDisplay>
  </div>
</template>

<script>
import prettyms from 'humanize-duration';
import videojs from 'video.js';
import 'videojs-markers';
import 'videojs-offset';
import 'videojs-event-tracking';
import ClipDisplay from './ClipDisplay.vue';
import { convertSecondsToTime } from '../../utils';

window.VIDEOJS_NO_BASE_THEME = true;

export default {
  name: 'VideoPlayer',
  components: {
    ClipDisplay,
  },
  props: {
    clipStart: {
      type: Number,
      default() {
        return 0;
      },
    },
    clipEnd: {
      type: Number,
      default() {
        return 0;
      },
    },
    hasActiveClip: {
      type: Boolean,
      default: false,
    },
    isEmbed: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default() {
        return '';
      },
    },
    poster: {
      type: String,
      default() {
        return '';
      },
    },
    options: {
      type: Object,
      default() {
        return {};
      },
    },
    retrySources: {
      type: Array,
      default() {
        return [];
      },
    },
    timecode: {
      type: Number,
      default() {
        return 0;
      },
    },
    track: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  data() {
    return {
      currentTime: null,
      player: null,
      defaultOptions: {
        autoplay: false,
        controlBar: {
          muteToggle: false,
          pictureInPictureToggle: false,
          progressControl: {
            keepTooltipsInside: true,
          },
        },
        controls: true,
        fill: true,
        sources: null,
        textTrackSettings: false,
        preload: 'metadata',
        plugins: {
          eventTracking: true,
        },
      },
    };
  },
  computed: {
    clipString() {
      if (this.hasActiveClip) {
        const duration = prettyms((this.clipEnd - this.clipStart) * 1000);
        return `Currently selected clip: ${duration} from ${this.clipStartTime}`;
      }
      return null;
    },
    clipStartTime() {
      return convertSecondsToTime(this.clipStart);
    },
    isClipEndState() {
      return (this.hasActiveClip && (this.currentTime > this.clipEnd));
    },
    markerDefaults() {
      return {
        markerStyle: {
          'background-color': '#3b2aee',
          'border-radius': '0%',
          'z-index': '0',
          height: '8px',
          border: '1px solid white',
          top: '-2px',
        },
        markerTip: {
          display: false,
        },
      };
    },
    playerOptions() {
      return { ...this.defaultOptions, ...this.options };
    },
  },
  watch: {
    clipStart(start) {
      if (!this.isEmbed) {
        this.destroyClipMarkers();
        this.initClipMarkers(start, this.clipEnd);
        this.initClipPosition();
      }
    },
    clipEnd(end) {
      if (!this.isEmbed) {
        this.destroyClipMarkers();
        this.initClipMarkers(this.clipStart, end);
      }
    },
    hasActiveClip(valid) {
      if (!this.isEmbed) {
        if (valid) {
          this.initClipMarkers(this.clipStart, this.clipEnd);
          this.initClipPosition();
        } else {
          this.destroyClipMarkers();
        }
      }
    },
    isClipEndState(ended) {
      if (ended) {
        this.player.pause();
      }
    },
    timecode(val) {
      if (val > 0) {
        this.player.currentTime(val);
        this.player.play();
        this.$emit('timecode-reset');
      }
    },
    retrySources(sources) {
      if (sources !== null) {
        this.updatePlayerSrc(sources);
      }
    },
    playerOptions(newOptions) {
      this.player.pause();
      this.reInit(newOptions.sources);
    },
  },
  beforeDestroy() {
    if (this.player) {
      this.player.dispose();
    }
  },
  mounted() {
    this.initVideoPlayer();
  },
  methods: {
    initVideoPlayer() {
      const DEFAULT_EVENTS = [
        'canplay',
        'canplaythrough',
        'dispose',
        'durationchange',
        'ended',
        'enterFullWindow',
        'enterpictureinpicture',
        'error',
        'exitFullWindow',
        'fullscreenchange',
        'leavepictureinpicture',
        'loadeddata',
        'loadedmetadata',
        'loadstart',
        'pause',
        'play',
        'playerresize',
        'playing',
        'ready',
        'seeked',
        'seeking',
        'texttrackchange',
        'tracking:firstplay',
        'tracking:buffered',
        // 'useractive',
        // 'userinactive',
        'volumechange',
        'waiting',
      ];

      const self = this;
      const options = this.playerOptions;
      this.player = videojs(this.$refs.videoPlayer, options, function () {
        // events
        const events = DEFAULT_EVENTS;
        // watch events
        const onEdEvents = {};
        for (let i = 0; i < events.length; i += 1) {
          if (typeof events[i] === 'string' && onEdEvents[events[i]] === undefined) {
            ((event) => {
              onEdEvents[event] = null;
              this.on(event, (...args) => {
                self.$emit(event, args);
              });
            })(events[i]);
          }
        }

        this.on('error', function () {
          if (this.error().code === 2) {
            self.$emit('playbackerror');
          }
        });

        this.on('timeupdate', function () {
          self.currentTime = this.currentTime();
          self.$emit('timeupdate', self.currentTime);
        });

        this.on('fullscreenchange', function () {
          if (videojs.browser.IS_ANDROID) {
            self.onFullscreenChange();
          }
        });

        this.one('loadedmetadata', function () {
          const duration = Math.ceil(parseInt(this.duration(), 10));
          self.$emit('loadedmetadata', duration);

          if (self.hasActiveClip) {
            self.initClipMarkers(self.clipStart, self.clipEnd);
            self.initClipPosition();
          }
        });
      });

      if (!this.isEmbed) {
        this.player.markers(this.markerDefaults);
      } else {
        this.initOffset();
      }

      this.initOverlays();

      this.player.ready(function () {
        self.player.addRemoteTextTrack(self.track, true);
      });
    },
    async onFullscreenChange() {
      if (this.player.isFullscreen()) {
        try {
          // Make screen landscape even if device was previously portrait
          await window.screen.orientation.lock('landscape');
        } catch (err) {
          console.error(err);
        }
      } else {
        window.screen.orientation.unlock();
      }
    },
    replayClip() {
      this.player.currentTime(this.clipStart);
      this.player.play();
    },
    destroyClipMarkers() {
      this.player.markers.removeAll();
    },
    handleContinuePlaying() {
      this.handleRemoveClip();
      this.player.play();
    },
    handleRemoveClip() {
      this.destroyClipMarkers();
      this.$emit('remove-clip');
    },
    initClipPosition() {
      this.player.currentTime(this.clipStart);
      this.player.posterImage.hide();
    },
    initClipMarkers(start, end) {
      const marker = { time: start, duration: end ? end - start : 0 };
      if (marker.duration && end < this.player.duration()) {
        this.drawClipMarkers(marker);
      }
    },
    drawClipMarkers(markerObject) {
      const markers = [markerObject];
      this.player.markers.add(markers);
    },
    initOffset() {
      // If an embed is clipped then it is shown
      // using the offset plugin. We're accessing
      // the route directly here rather than wait
      // for clip validation from the parent. This
      // is to try to prevent uncessary buffering of the
      // video in the wrong time location.
      const offsetOptions = {
        start: 0,
        end: 0,
        restart_beginning: false,
      };
      const query = this.$route.query;
      if (Object.prototype.hasOwnProperty.call(query, 'start')) {
        offsetOptions.start = parseInt(query.start, 10);
        if (Object.prototype.hasOwnProperty.call(query, 'end')) {
          const end = parseInt(query.end, 10);
          offsetOptions.end = end > offsetOptions.start ? end : 0;
        }
      }
      this.player.offset(offsetOptions);
    },
    initOverlays() {
      // @todo this could be abstracted out into a child component
      // if multiple overlays were ever needed.
      const el = videojs.dom.createEl('div', {
        className: 'vjs-title-overlay',
      });
      el.innerHTML = `<span>${this.title}</span>`;
      this.player.el().appendChild(el);
    },
    reInit(val) {
      this.player.poster(this.poster);
      const textTracks = this.player.textTracks();
      for (let i = 0; i < textTracks.length; i += 1) {
        this.player.removeRemoteTextTrack(textTracks[i]);
      }
      this.player.addRemoteTextTrack(this.track, true);
      this.player.off('ready');
      this.player.src(val);
      this.initOverlays();
    },
    updatePlayerSrc(val) {
      const time = this.player.currentTime();
      let initdone = false;

      this.player.off('ready');
      this.player.src(val);

      // wait for video metadata to load, then set time.
      this.player.one('loadedmetadata', () => {
        if (this.hasActiveClip) {
          this.initClipPosition();
        } else {
          this.player.currentTime(time);
          this.player.play();
        }
      });

      // iPhone/iPad need to play first, then set the time
      // events: https://www.w3.org/TR/html5/isEmbedded-content-0.html#mediaevents
      this.player.one('canplaythrough', () => {
        if (!initdone) {
          if (!this.hasActiveClip) {
            this.player.currentTime(time);
            this.player.play();
          }
          initdone = true;
        }
      });
    },
  },
};
</script>

<style>
.video-player__wrapper {
  min-width: 100%;
  margin: 0 -16px;
}

.video-player__wrapper-inner {
  position: relative;
  padding-top: calc((9 / 16) * 100%);
}

.video-player__container {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

.video-player {
  width: 100%;
  height: 100%;
}

@media (min-width: 52.5em) {
  .video-player__wrapper {
    margin: 0;
  }
}
</style>
