<?php

class IncludeContentHook implements IContentHook {

	public static function Process($includeFile, $pageParameters) {

		$uri = urldecode(
		    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
		);

		$path = __DIR__ . "/../../content" . self::renameHtmlIntoIncludeDir($uri) . trim($includeFile);
		$content = self::getContent($path);

		return ContentHooksFacade::Process($content, $pageParameters);
	}

	private function getContent($path) {
		$content = file_get_contents($path);
		$inclPartern = "/<!--CONTENTHOOK-BEGIN:include-->(.+?)<!--CONTENTHOOK-END-->/";
		if (preg_match_all($inclPartern, $content,$matches)) {
			foreach ($matches[1] as $key => $includeFile) {
				$pathTemp = $path;
				$path = self::renameHtmlIntoIncludeDir($path) . trim($includeFile);
				$content_C = self::getContent($path);
				$content = str_replace($matches[0][$key], $content_C, $content);
				$path = $pathTemp;
			}
		} else {
			return $content;
		}

		return $content;
	}


	private static function renameHtmlIntoIncludeDir($htmlExtensionFileName)
	{
		return str_replace("html", "include/", trim($htmlExtensionFileName));
	}

}