import { shallowMount, createLocalVue } from '@vue/test-utils';
import VueRouter from 'vue-router';
import expect from 'expect';
import ClippingTool from '../../resources/js/components/video/ClippingTool.vue';

const localVue = createLocalVue();
localVue.use(VueRouter);
const router = new VueRouter();

describe('Clipping Tool', () => {
  const $route = {
    path: '/example',
  };

  const window = {
    location: {
      origin: 'http://example.com',
    },
  };

  const wrapper = shallowMount(ClippingTool, {
    mocks: {
      $route,
      window,
    },
  });

  it('displays clipping tool tips', () => {
    expect(wrapper.html()).toContain('Make a clip from this video to share. Set the video progress bar to the beginning of your clip, then click or touch ‘Set start time.’ Repeat for the end time.');
  });

  it('emits an event when user sets start time via button', async () => {
    const button = wrapper.find('#clip__start-input');
    await button.trigger('click');
    expect(wrapper.emitted()['update-clip'][0]).toEqual([0, 0]);
  });

  it('emits an event when user sets end time via button', async () => {
    const button = wrapper.find('#clip__end-input');
    await button.trigger('click');
    expect(wrapper.emitted()['update-clip'][0]).toEqual([0, 0]);
  });
});
