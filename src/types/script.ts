export interface Script {
	id: number|null
	title: string
	description: string
	enabled: boolean
	program: string
}

export function defaultScript(): Script {
	return {
		id: null,
		title: '',
		description: '',
		enabled: false,
		program: ''
	}
}
