export default function stringifyQuery(obj, parentName) {
  const res = obj ? Object.keys(obj).map((key) => {
    const val = obj[key];
    let ckey = key;
    if (parentName) {
      ckey = `${parentName}[${ckey}]`;
    }

    if (val === undefined) {
      return '';
    }

    if (val === null) {
      return encodeURIComponent(ckey);
    }

    if (!Array.isArray(val) && typeof val === 'object') {
      return stringifyQuery(val, ckey).slice(1);
    }

    if (Array.isArray(val)) {
      const result = [];

      val.forEach((val2) => {
        if (val2 === undefined) {
          return;
        }
        if (val2 === null) {
          result.push(encodeURIComponent(`${ckey}`));
        } else {
          result.push(`${encodeURIComponent(`${ckey}`)}=${encodeURIComponent(val2)}`);
        }
      });

      return result.join('&');
    }

    return `${encodeURIComponent(ckey)}=${encodeURIComponent(val)}`;
  }).filter((x) => x.length > 0).join('&') : null;

  return res ? `?${res}` : '';
}
