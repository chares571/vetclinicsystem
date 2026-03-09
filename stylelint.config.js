module.exports = {
  // …existing config…
  plugins: [
    // optional, install with `npm i -D stylelint-config-tailwindcss`
    'stylelint-config-tailwindcss',
  ],
  extends: [
    'stylelint-config-standard',
    'stylelint-config-tailwindcss', // if you added the plugin
  ],
  rules: {
    // allow Tailwind’s at‑rules
    'at-rule-no-unknown': [true, {
      ignoreAtRules: [
        'tailwind',
        'apply',
        'variants',
        'responsive',
        'screen',
        // add any others you use
      ]
    }],
    // …other rules…
  },
};