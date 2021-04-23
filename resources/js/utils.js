export function convertSecondsToTime(seconds) {
  return (new Date(seconds * 1000)).toUTCString().match(/(\d\d:\d\d:\d\d)/)[0];
}
export function convertTimeToSeconds(timeString) {
  return new Date(`1970-01-01T${timeString}Z`).getTime() / 1000;
}
