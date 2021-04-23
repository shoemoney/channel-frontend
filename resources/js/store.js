import Vue from 'vue';
import SiteCopy from './siteCopy';

export const store = Vue.observable({
  searchOverlayActive: false,
  facetOverlayActive: false,
  footerActive: false,
  searchTerm: '',
  transcriptInit: false,
  copy: SiteCopy,
});

export const mutations = {
  toggleFooterActive() {
    store.footerActive = !store.footerActive;
  },
  toggleFacetOverlayActive() {
    store.facetOverlayActive = !store.facetOverlayActive;
  },
  toggleSearchOverlayActive() {
    store.searchOverlayActive = !store.searchOverlayActive;
  },
  setSearchTerm(term) {
    store.searchTerm = term;
  },
  toggleTranscriptInit() {
    store.transcriptInit = !store.transcriptInit;
  },
};
