export default {
  data: () => ({
    copied: false,
  }),
  watch: {
    copied() {
      setTimeout(() => {
        this.copied = false;
      }, 2000);
    },
  },
  methods: {
    async copyToClipboard(text) {
      if (!navigator.clipboard) {
        this.fallbackCopyToClipboard(text);
        return;
      }
      try {
        await navigator.clipboard.writeText(text);
        this.copied = true;
      } catch (err) {
        console.error('Unable to copy ', err);
      }
    },
    fallbackCopyToClipboard(text) {
      const textArea = document.createElement('textarea');
      textArea.value = text;

      // Avoid scrolling to bottom
      textArea.style.top = '0';
      textArea.style.left = '0';
      textArea.style.position = 'fixed';

      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();

      try {
        document.execCommand('copy');
      } catch (err) {
        console.error('Unable to copy', err);
      }

      document.body.removeChild(textArea);
    },
  },
};
