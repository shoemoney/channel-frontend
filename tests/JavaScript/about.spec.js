import { mount, createLocalVue, RouterLinkStub } from '@vue/test-utils';
import VueRouter from 'vue-router';
import expect from 'expect';
import About from '../../resources/js/components/video/About.vue';

const localVue = createLocalVue();
localVue.use(VueRouter);
const router = new VueRouter();

describe('Information tab', () => {
  const $route = {
    path: '/example',
  };

  const window = {
    location: {
      origin: 'http://example.com',
    },
  };

  const wrapper = mount(About, {
    mocks: {
      $route,
      window,
    },
    stubs: {
      RouterLink: RouterLinkStub,
    },
    propsData: {
      topics: ['topic1', 'topic2'],
      people: ['people1', 'people2'],
      playlists: ['playlist1', 'playlist2'],
      description: 'example description',
    },
  });

  it('displays topics', () => {
    expect(wrapper.html()).toContain('topic1');
    expect(wrapper.html()).toContain('topic2');
  });

  it('displays people', () => {
    expect(wrapper.html()).toContain('people1');
    expect(wrapper.html()).toContain('people2');
  });

  it('displays playlists', () => {
    expect(wrapper.html()).toContain('playlist1');
    expect(wrapper.html()).toContain('playlist2');
  });

  it('displays a description', () => {
    expect(wrapper.find('.video-meta__description').exists()).toBe(true);
    expect(wrapper.html()).toContain('example description');
  });

  it('works without a description', async () => {
    await wrapper.setProps({ description: '' });
    expect(wrapper.find('.video-meta__description').exists()).toBe(true);
  });
});
