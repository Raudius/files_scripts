<?php

namespace OCA\FilesScripts\Interpreter\Functions\Media;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use Throwable;

/**
 * `ffprobe(Node input_file): Table`
 *
 * Returns a table detailing the metadata information that could be retrieved from the input file using [ffprobe](https://ffmpeg.org/ffprobe.html).
 */
class Ffprobe extends RegistrableFunction {
	public function run($file = [], $saveName = null, $config = []): ?array {
		try {
			$fileNode = $this->getFile($this->getPath($file));
			$localPath = $fileNode ? $fileNode->getStorage()->getLocalFile($fileNode->getInternalPath()) : null;
			if (!$localPath) {
				return [];
			}

			$ffprobe = \FFMpeg\FFProbe::create();
			$ffprobeStream = $ffprobe->streams($localPath)
				->videos()
				->first();

			if (!$ffprobeStream) {
				return [];
			}

			return $ffprobeStream->all();
		} catch (Throwable $throwable) {
			return null;
		}
	}
}
