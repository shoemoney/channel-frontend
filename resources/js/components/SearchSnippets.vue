<template>
  <div class="ui-card__snippets">
    <template v-if="!snippetsAreEmpty">
      <p class="ui-card__snippet" v-for="match in descriptionMatches" v-html="match" />
      <em class="ui-card__snippet" v-for="match in transcriptMatches" v-html="match" />
    </template>
    <template v-else>
      <p>{{ defaultContent }}</p>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    defaultContent: {
      type: String,
      default: '',
    },
    snippets: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  computed: {
    snippetsAreEmpty() {
      return Object.keys(this.snippets).length === 0;
    },
    transcriptMatches() {
      if ('transcription_txt' in this.snippets) {
        return this.snippets.transcription_txt.map((snippet) => {
          if (/[.,:!?]/.test(snippet.trim().charAt(snippet.length - 1))) {
            return `&ldquo;${snippet}...&rdquo;`;
          }
          return `&ldquo;${snippet}&rdquo;`;
        });
      }
      return false;
    },
    descriptionMatches() {
      if ('description' in this.snippets) {
        return this.snippets.description.map((snippet) => {
          if (/[^.,:!?]/.test(snippet.trim().charAt(snippet.length - 1))) {
            return `${snippet}...`;
          }
          return snippet;
        });
      }
      return false;
    },
  },
};
</script>
