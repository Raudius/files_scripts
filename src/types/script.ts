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
		options: {
			type: 'text',
			multiselectOptions: [],
			filepickMimes: [],
		} as ScriptInputOptions
	} as ScriptInput
}

export function inflateScriptInputOptions(scriptInput: ScriptInput): ScriptInput {
	return {
		...scriptInput,
		options: {
			type: scriptInput.options.type ?? 'text',
			multiselectOptions: scriptInput.options.multiselectOptions ?? [],
			filepickMimes: scriptInput.options.filepickMimes ?? []
		}
	}
}
