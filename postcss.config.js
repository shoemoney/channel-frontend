const autoprefixer = require('autoprefixer');
const postcssImport = require('postcss-import');
const postcssNested = require('postcss-nested');
const postcssColor = require('postcss-color-function');
const postcssHexrgba = require('postcss-hexrgba');
const postcssCustomProperties = require('postcss-custom-properties');
const postcssPxtorem = require('postcss-pxtorem');
const cssDeclarationSorter = require('css-declaration-sorter');
const cssnano = require('cssnano');

module.exports = (ctx) => ({
  map: !ctx.env || ctx.env !== 'production' ? { inline: false } : false,
  plugins: [
    autoprefixer(),
    postcssImport(),
    postcssNested(),
    postcssColor(),
    postcssHexrgba(),
    postcssCustomProperties(),
    postcssPxtorem({
      rootValue: 16,
    }),
    cssDeclarationSorter({ order: 'smacss' }),
    cssnano(),
  ],
});
