export interface Script {
	id: number
	title: string
	description: string
	program: string
	enabled: boolean
	background: boolean
	requestDirectory: boolean,
}

export interface ScriptInput {
	id: number
	scriptId: number
	name: string
	description: string
	value: string
}

export function defaultScript(): Script {
	return {
		id: null,
		title: '',
		description: '',
		enabled: false,
		program: '',
		background: false,
		requestDirectory: false,
	}
}

export function defaultScriptInput(scriptId: number = null): ScriptInput {
	return {
		id: null,
		scriptId: scriptId,
		name: '',
		description: '',
		value: '',
	}
}
