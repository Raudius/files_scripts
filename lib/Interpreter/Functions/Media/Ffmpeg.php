<?php

namespace OCA\FilesScripts\Interpreter\Functions\Media;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Audio\Aac;
use FFMpeg\Format\Audio\DefaultAudio;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Format\Audio\Vorbis;
use FFMpeg\Format\Audio\Wav;
use FFMpeg\Format\FormatInterface;
use FFMpeg\Format\Video\DefaultVideo;
use FFMpeg\Format\Video\Ogg;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\WMV;
use FFMpeg\Format\Video\WMV3;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\ITempManager;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * `ffmpeg(Node input_file, String output_name, Table config): Node|nil`
 *
 * Converts the input file using FFmpeg according to the specified configuration. The output file will be placed in the same directory as the input file.
 * The output file is returned, or `nil` if the operation failed.
 *
 * The `config` parameter expects a table with the following parameters (only the format.name parameter is needed, other config parameters are optional):
 * ```lua
 * local config = {
 *   timeout= 3600,
 *   format = {
 *     name= "x264",              -- ogg, webm, wmv, wmv3, x264, aac, mp3, vorbis, wav
 *     audio_channels= 2,
 *     audio_codec= "aac",
 *     video_codec= "libx264",
 *     audio_bitrate= 128,        -- in kilobits
 *     video_bitrate= 2500,       -- in kilobits
 *     initial_parameters= {},    -- https://github.com/PHP-FFMpeg/PHP-FFMpeg/tree/0.x#add-additional-parameters
 *     additional_parameters= {}, -- https://github.com/PHP-FFMpeg/PHP-FFMpeg/tree/0.x#add-additional-parameters
 *     ffmpeg_threads= 4
 *   },
 *   clip= {
 *     start= 0,      -- Start of the clip in seconds (also accepts a string in the format [hh]:[mm]:[ss]:[frames]), defaults to 0
 *     duration= 2,   -- Duration of the clip in seconds (defaults to the end of the stream)
 *   },
 *   width= 1920,     -- Sets output width in pixels
 *   height= 1080     -- Sets output height in pixels
 * }
 * ```
 *
 * **Example1** converts a file to MPEG-4 format, and sets the resolution to 500x400:
 * ```lua
 * local wmv = ffmpeg(get_input_files()[1], "output.mp4", {
 *   format = { name= "x264" },
 *   width= 500,
 *   height= 400
 * })
 * ```
 */
class Ffmpeg extends RegistrableFunction {
	private const CONFIG_DEFAULT = [
		'timeout' => 3600,
		'ffmpeg_threads' => 4,
		'format' => [],
		'clip' => [],
		'width' => null,
		'height' => null,
	];

	private const FORMAT_MAP = [
		'ogg' => Ogg::class,
		'webm' => WebM::class,
		'wmv' => WMV::class,
		'wmv3' => WMV3::class,
		'x264' => X264::class,
		'aac' => Aac::class,
		'mp3' => Mp3::class,
		'vorbis' => Vorbis::class,
		'wav' => Wav::class
	];

	private LoggerInterface $logger;
	private ITempManager $tempManager;

	public function __construct(LoggerInterface $logger, ITempManager $tempManager) {
		$this->logger = $logger;
		$this->tempManager = $tempManager;
	}

	public function run($file = [], $saveName = null, $config = []): ?array {
		try {
			$fileNode = $this->getFile($this->getPath($file));
			$localPath = $fileNode ? $fileNode->getStorage()->getLocalFile($fileNode->getInternalPath()) : null;
			$tempDir = $this->tempManager->getTemporaryFolder();
			$config = $this->getConfig($config, $tempDir);
			// Make sure the parameters are set.
			if (!$localPath || !is_string($saveName) || !isset($config['format'])) {
				return null;
			}

			// Make sure we won't overwrite an already-existing file;
			if ($fileNode->getParent()->nodeExists($saveName)) {
				return null;
			}

			// Make sure the format is valid
			$format = $this->getFormat($config['format']);
			if (!$format) {
				return null;
			}

			$ffmpeg = \FFMpeg\FFMpeg::create($config, $this->logger);
			$media = $ffmpeg->open($localPath);
			// Make sure that media is at leas tan instance of Audio (Video extends Audio)
			if ($media instanceof Audio === false) {
				return null;
			}

			// Clip a video or audio using a time-code either in string format (03:14:15:93) or a float value in seconds.
			if (isset($config['clip'])) {
				$start = $config['clip']['start'] ?? 0;
				$duration = $config['clip']['duration'] ?? null;
				$startObj = is_string($start) ? TimeCode::fromString($start) : TimeCode::fromSeconds($start);

				if ($duration) {
					$media = $media->clip($startObj, TimeCode::fromSeconds($duration));
				} else {
					$media = $media->clip($startObj);
				}
			}

			// Video specific configuration
			if ($media instanceof Video) {
				//Crop video
				isset($config['width'], $config['height']) && $media->filters()->resize(new Dimension($config['width'], $config['height']));
			}

			// Save file to temporary location
			$tempFile = tempnam($tempDir, "files_scripts_ffmpeg_output_") . $saveName;
			$media->save($format, $tempFile);

			// Copy file to new file in same folder as the input file.
			$newFile = $fileNode->getParent()->newFile($saveName, fopen($tempFile, 'rb'));

			$this->tempManager->clean();
			$this->tempManager->cleanOld();

			return $this->getNodeData($newFile);
		} catch (Throwable $throwable) {
			return null;
		}
	}

	/**
	 * Gets the initial config to pass to FFmpeg
	 */
	private function getConfig(array $configInput, string $tempDir): array {
		$config = [];
		foreach (self::CONFIG_DEFAULT as $key => $defaultValue) {
			$config[$key] = $configInput[$key] ?? $defaultValue;
		}

		if (!is_numeric($config['timeout']) || $config['timeout'] <= 0) {
			$config['timeout'] = self::CONFIG_DEFAULT['timeout'];
		}

		if (!is_numeric($config['ffmpeg_threads']) || $config['ffmpeg_threads'] <= 0) {
			$config['ffmpeg_threads'] = self::CONFIG_DEFAULT['ffmpeg_threads'];
		}

		$config['temporary_directory'] = $tempDir;
		$config['ffmpeg.threads'] = $config['ffmpeg_threads'];

		return $config;
	}

	/**
	 * Creates a {@see FormatInterface} object from the config options.
	 *
	 * @param array $formatConfig
	 * @return DefaultAudio|null
	 */
	private function getFormat(array $formatConfig): ?DefaultAudio {
		$formatName = $formatConfig['name'] ?? 'default';
		$formatClass = self::FORMAT_MAP[$formatName] ?? null;

		if (!class_exists($formatClass) || !is_a($formatClass, FormatInterface::class, true)) {
			return null;
		}

		$format = new $formatClass;
		if ($format instanceof DefaultAudio === false) {
			return null;
		}

		/** @var DefaultAudio $format */
		isset($formatConfig['audio_codec']) && $format->setAudioCodec($formatConfig['audio_codec']);
		isset($formatConfig['audio_bitrate']) && $format->setAudioKiloBitrate($formatConfig['audio_bitrate']);
		isset($formatConfig['audio_channels']) && $format->setAudioChannels($formatConfig['audio_channels']);

		if ($format instanceof DefaultVideo) {
			/** @var DefaultVideo $format */
			isset($formatConfig['video_codec']) && $format->setVideoCodec($formatConfig['video_codec']);
			isset($formatConfig['video_bitrate']) && $format->setKiloBitrate($formatConfig['video_bitrate']);
			isset($formatConfig['initial_parameters']) && $format->setAdditionalParameters($formatConfig['initial_parameters']);
			isset($formatConfig['additional_parameters']) && $format->setAdditionalParameters($formatConfig['additional_parameters']);
		}

		return $format;
	}
}
