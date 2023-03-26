export interface Script {
	id: number
	title: string
	description: string
	program: string
	enabled: boolean
	limitGroups: string[]
	public: boolean
	mimetype: string
}

export interface ScriptInput {
	id: number
	scriptId: number
	name: string
	description: string
	options: ScriptInputOptions
	value: string
}

export interface ScriptInputOptions {
	type: string,
	multiselectOptions: string[]
	filepickMimes: string[]
	allowMultiple: boolean
	textarea: boolean
}

/**
 * Generates an empty Script.
 */
export function defaultScript(): Script {
	return {
		id: null,
		title: '',
		description: '',
		enabled: false,
		program: '',
		public: false,
		limitGroups: [],
		mimetype: ''
	}
}

export function createScriptInput(name: string, description: string): ScriptInput {
	return {
		name,
		description,
		options: defaultScriptInputOptions()
	} as ScriptInput
}

export function inflateScriptInputOptions(scriptInput: ScriptInput): ScriptInput {
	const defaultOptions = defaultScriptInputOptions()
	return {
		...scriptInput,
		options: {
			type: scriptInput.options.type ?? defaultOptions.type,
			multiselectOptions: scriptInput.options.multiselectOptions ?? defaultOptions.multiselectOptions,
			filepickMimes: scriptInput.options.filepickMimes ?? defaultOptions.filepickMimes,
			allowMultiple: scriptInput.options.allowMultiple ?? defaultOptions.allowMultiple,
			textarea: scriptInput.options.textarea ?? defaultOptions.textarea,
		}
	}
}

export function defaultScriptInputOptions(): ScriptInputOptions {
	return {
		type: 'text',
		multiselectOptions: [],
		filepickMimes: [],
		allowMultiple: false,
		textarea: false,
	}
}
