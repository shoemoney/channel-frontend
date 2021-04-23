<template>
  <VideoMeta>
    <template v-slot:highlighted>
      <div class="clip">
        <p>{{ tips }}</p>
        <div class="clip__controls">
          <div class="clip__control clip__control--left">
            <button
              id="clip__start-input"
              class="clip__control__button clip__control__button--left"
              data-tracking-gtm="video page links"
              @click="setTime('start')"
            >
              Set start time
            </button>
            <VInput
              ref="startInput"
              v-model="clipStartTime"
              label="Set clip start time"
              placeholder="00:00:00"
              pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}"
              :classes="{
                root: 'clip-control__input-wrap',
                text: 'visually-hidden',
                input: 'clip__control__input',
                description: 'clip-control__error'
              }"
            >
              <template #description="input">
                <template v-if="input.error">
                  <small v-if="input.invalid.pattern">
                    Please enter a valid timestamp: HH:MM:SS
                  </small>
                </template>
              </template>
            </VInput>
          </div>

          <div class="clip__control clip__control--right">
            <button
              id="clip__end-input"
              class="clip__control__button clip__control__button--right"
              data-tracking-gtm="video page links"
              @click="setTime('end')"
            >
              Set end time
            </button>
            <VInput
              ref="endInput"
              v-model="clipEndTime"
              label="Set clip end time"
              placeholder="00:00:00"
              pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}"
              :classes="{
                root: 'clip-control__input-wrap',
                text: 'visually-hidden',
                input: 'clip__control__input',
                description: 'clip-control__error'
              }"
            >
              <template #description="input">
                <template v-if="input.error">
                  <small v-if="input.invalid.pattern">
                    Please enter a valid timestamp: HH:MM:SS
                  </small>
                </template>
              </template>
            </VInput>
          </div>
        </div>
        <div
          class="share-link"
        >
          <input
            v-if="canGenerateClip"
            v-model="clipUrl"
            readonly
            class="clip__control__input clip__control__input--unrestrained"
          >
        </div>
        <div class="clip__controls clip__sharing">
          <button
            :disabled="!canGenerateClip"
            :class="['button', 'button--action']"
            aria-label="Copy citation to clipboard"
            data-tracking-gtm="video page links"
            @click="copyToClipboard(clipUrl)"
          >
            <transition
              name="fade-text"
              mode="out-in"
            >
              <span
                :key="copied"
                class="copy-status"
                :aria-label="copied ? 'Clip link copied to clipboard' : 'Click to copy clip link to clipboard'"
              >{{ copied ? 'Copied' : 'Copy link' }}</span>
            </transition>
            <BaseIcon
              width="18"
              height="18"
              view-box="0 0 24 17"
              icon-name="copy"
              title="Copy link to clipboard"
            >
              <CopyIcon />
            </BaseIcon>
          </button>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <p
        v-if="hasActiveClip"
        class="video-meta__description cliptool--remove"
      >
        Remove existing clip
        <button
          class="button button--icon search-facet__item-remove"
          aria-label="Remove clip"
          data-tracking-gtm="video page links"
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
      </p>
    </template>
  </VideoMeta>
</template>

<script>
import { VInput } from 'vuetensils/src/components';
import { convertTimeToSeconds, convertSecondsToTime } from '../../utils';
import VideoMeta from '../VideoMeta.vue';
import BaseIcon from '../base/BaseIcon.vue';
import CloseIcon from '../icons/CloseIcon.vue';
import CopyIcon from '../icons/CopyIcon.vue';
import CopyTo from '../../mixins/copyToClipboard';

export default {
  components: {
    BaseIcon,
    CloseIcon,
    CopyIcon,
    VInput,
    VideoMeta,
  },
  mixins: [CopyTo],
  props: {
    currentTimecode: {
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
    clipStart: {
      type: Number,
      default() {
        return 0;
      },
    },
    hasActiveClip: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      domain: window.location.origin,
      path: this.$route.path,
      tips: `Make a clip from this video to share. Set the video progress bar to the beginning of your clip, then click or touch ‘Set start time.’ Repeat for the end time.`,
    };
  },
  computed: {
    canGenerateClip() {
      const start = this.clipStart;
      const end = this.clipEnd;
      return start !== null && (end !== null ? end > start : true);
    },
    clipUrl() {
      if (!this.canGenerateClip) { return ''; }
      const start = this.clipStart;
      const end = this.clipEnd;
      let url = `${this.domain + this.path}`;
      if (start !== null) {
        url += `?start=${start}`;
      }
      if (end !== null && end > start) {
        url += `&end=${this.clipEnd}`;
      }
      return url;
    },
    clipStartTime: {
      get() {
        if (this.clipStart === null) { return '00:00:00'; }
        return convertSecondsToTime(this.clipStart);
      },
      set(value) {
        if (!Number.isNaN(convertTimeToSeconds(value))) {
          this.$emit('update-clip', convertTimeToSeconds(value), this.clipEnd);
        }
      },
    },
    clipEndTime: {
      get() {
        if (this.clipEnd === null) { return '00:00:00'; }
        return convertSecondsToTime(this.clipEnd);
      },
      set(value) {
        if (!Number.isNaN(convertTimeToSeconds(value))) {
          this.$emit('update-clip', this.clipStart, convertTimeToSeconds(value));
        }
      },
    },
  },
  methods: {
    handleRemoveClip() {
      this.$emit('remove-clip');
    },
    setTime(input) {
      if (input === 'start') {
        this.$emit('update-clip', this.currentTimecode, this.clipEnd);
        this.clipStartTime = convertSecondsToTime(this.currentTimecode);
      } else if (input === 'end') {
        this.$emit('update-clip', this.clipStart, this.currentTimecode);
        this.clipEndTime = convertSecondsToTime(this.currentTimecode);
      }
    },
  },
};
</script>
