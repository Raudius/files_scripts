// eslint-disable-next-line
import {generateFilePath} from "@nextcloud/router";
import Flow from "./views/Flow.vue";

declare let appName: string
// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, '', 'js/');

(function() {
	OCA.WorkflowEngine.registerOperator({
		id: 'OCA\\FilesScripts\\Flow\\Operation',
		operation: '',
		options: Flow,
	});
})();
