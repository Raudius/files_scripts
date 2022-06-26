/**
 * Translates a string.
 *
 * @param {string} str String to be translated
 * @param {object} params Transaltion parameters
 */
export function translate(str: String, params?: Object) {
	return t('files_scripts', str, params)
}
