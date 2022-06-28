/**
 * Translates a string.
 *
 * @param {String} ctxt Translation context
 * @param {string} str String to be translated
 * @param {object} params Translation parameters
 */
export function translate(ctxt: String, str: String, params?: Object) {
	return t(ctxt, str, params)
}
