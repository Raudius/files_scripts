module.exports = {
	extends: [
		'@nextcloud',
		"eslint:recommended",
	],
	parser: "vue-eslint-parser",
	parserOptions: {
		"parser": "@typescript-eslint/parser",
		ecmaVersion: 2020,
	},
	rules: {
		"no-prototype-builtins": "off",
		"vue/require-default-prop": "off",

		// Find a way to support ts imports without extension
		"node/no-missing-require": "off",
		"import/extensions": "off",
		"node/no-missing-import": "off",
		"import/no-unresolved": "off",
	}
}
