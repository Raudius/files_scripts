/* eslint-disable n/no-extraneous-import */
const webpackConfig = require("@nextcloud/webpack-vue-config")
const webpackRules = require("@nextcloud/webpack-vue-config/rules")

const isTesting = !!process.env.TESTING

if (isTesting) {
	console.debug('TESTING MODE ENABLED')
}

webpackRules.RULE_SVG = {
	test: /\.svg$/,
	type: 'asset/source',
}

webpackRules.RULE_TS = {
	test: /\.tsx?$/,
	loader: 'ts-loader',
	exclude: /node_modules/,
	options: {
		appendTsSuffixTo: [/\.vue$/]
	}
}

webpackConfig.entry = {
	main: "./src/main.ts",
	workflow: "./src/workflow.ts"
}

// Replaces rules array
webpackConfig.module.rules = Object.values(webpackRules)
webpackConfig.resolve.extensions.push(".svg")
// Clean dist folder
webpackConfig.output.clean = true

export default webpackConfig
