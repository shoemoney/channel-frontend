export default function isIos() {
  let iOS = null;
  let isSafari = null;
  if (window.navigator && window.navigator.userAgent) {
    const ua = window.navigator.userAgent;
    const nav = window.navigator;
    iOS = (ua.match(/(iP(ad|od|hone))/)
      || (nav.platform === 'MacIntel' && nav.maxTouchPoints > 1));
    isSafari = nav.vendor && nav.vendor.indexOf('Apple') > -1
      && ua.indexOf('CriOS') === -1
      && ua.indexOf('FxiOS') === -1;
  }

  if (iOS && isSafari) {
    return true;
  }

  return false;
}
