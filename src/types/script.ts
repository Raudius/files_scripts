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
		background: false,
		requestDirectory: false,
	}
}
