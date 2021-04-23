export default function getOrientation() {
  if (typeof window.screen.orientation !== 'undefined') {
    const { type } = window.screen.orientation;
    return type.includes('landscape') ? 'landscape' : 'portrait';
  }

  // iOS/safari
  return Math.abs(+window.orientation) === 90 ? 'landscape' : 'portrait';
}
