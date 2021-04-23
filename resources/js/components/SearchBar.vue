<template>
  <div
    :class="['search-bar', ...classes]"
  >
    <div
      class="search-bar__action"
    >
      <div class="form__input-wrapper form__input-wrapper--search-bar">
        <VInput
          ref="input"
          v-model="clonedTerm"
          :classes="{
            input: ['form__input', 'form__input--search', 'form__input--search-bar'],
            text: 'visually-hidden'
          }"
          type="text"
          :name="inputId"
          label="Search"
          aria-label="Search"
          :placeholder="placeholder"
          @keydown.enter.prevent="search"
          @focus="placeholder = 'Type something'"
          @blur="placeholder = 'Search'"
        />

        <div class="form__submit-wrapper">
          <button
            :class="['form__submit', 'button', 'button--icon']"
            @click.stop="search"
          >
            <BaseIcon
              width="48"
              height="48"
              view-box="0 0 24 24"
              icon-name="search-bar"
              title="Submit search"
            >
              <SearchIcon />
            </BaseIcon>
          </button>
        </div>
      </div>

      <div class="search-bar__options">
        <div class="search-bar__option search-bar__option--left">
          <span class="search-bar__option-label">or</span>
          <RouterLink
            :class="['link--text', 'link--text-secondary', 'link--tag']"
            :to="{ name: 'search', query: {} }"
            data-tracking-gtm="search menu links"
            @click.native="close"
          >
            <span class="link--tag__text">show me everything</span>
          </RouterLink>
        </div>
        <div
          v-if="showTags"
          class="search-bar__option search-bar__option--right"
        >
          <span class="search-bar__option-label">or try</span>
          <TagGroup
            :items="tags"
            @tag-selected="close"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { VInput } from 'vuetensils/src/components';
import TagGroup from './TagGroup.vue';
import { store, mutations } from '../store';

export default {
  name: 'SearchBar',
  components: {
    TagGroup,
    VInput,
  },
  props: {
    classes: {
      type: Array,
      default: () => [],
    },
    focus: {
      type: Boolean,
      default: false,
    },
    tags: {
      type: Array,
      default: () => [],
    },
    showTags: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      clonedTerm: '',
      placeholder: 'Search',
    };
  },
  computed: {
    inputId() {
      return `search-${Math.random().toString(12).substring(4, 8)}`;
    },
    tagClasses() {
      return ['search-facet__item-link', 'link--text', 'link--tag'];
    },
    searchTerm: {
      get() {
        return store.searchTerm;
      },
      set(value) {
        this.setSearchTerm(value);
      },
    },
  },
  mounted() {
    if (this.focus) {
      this.$nextTick(() => {
        if (window.innerWidth > 840) {
          this.$refs.input.$refs.input.focus();
        }
      });
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    setSearchTerm: mutations.setSearchTerm,
    search() {
      this.setSearchTerm(this.clonedTerm);
      this.$router.push({ name: 'search', query: { term: this.clonedTerm } }).catch(() => {
        // @todo Log these rather than swallow?
      });
      this.clonedTerm = '';
      this.close();
    },
  },
};
</script>
