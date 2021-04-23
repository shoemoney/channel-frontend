module.exports = {
  "extends": [
    "airbnb-base",
    "plugin:vue/recommended",
    "plugin:vuejs-accessibility/recommended",
  ],
  "globals": {
    "Vue": true,
    "window": true,
  },
  ignorePatterns: ["webpack.config.*"],
  "rules": {
    "quotes": ["error", "single", { "avoidEscape": true, "allowTemplateLiterals": true }],
    "func-names": ["off"],
    "prefer-arrow-callback": ["off"],
    "prefer-destructuring": ["off"],
    "no-debugger": "warn",
    "vue/component-name-in-template-casing": "error"
  }
}
