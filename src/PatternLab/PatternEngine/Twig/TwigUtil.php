<?php

/*!
 * Twig Pattern Engine Loader Class - String
 *
 * Copyright (c) 2014 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 *
 * Sets an instance of Twig to deal with rendering of strings
 *
 */

namespace PatternLab\PatternEngine\Twig;

use \PatternLab\Config;
use \PatternLab\Console;
use \Symfony\Component\Finder\Finder;

class TwigUtil {
	
	/**
	* Load custom date formats for Twig
	* @param  {Instance}       an instance of the twig engine
	*
	* @return {Instance}       an instance of the twig engine
	*/
	public static function loadDateFormats($instance) {
		
		$dateFormat     = Config::getOption("twigDefaultDateFormat");
		$intervalFormat = Config::getOption("twigDefaultIntervalFormat");
		
		if ($dateFormat && $intervalFormat && !empty($dateFormat) && !empty($intervalFormat)) {
			$instance->getExtension("core")->setDateFormat($dateFormat, $intervalFormat);
		}
		
		return $instance;
		
	}
	
	/**
	* Enable the debug options for Twig
	* @param  {Instance}       an instance of the twig engine
	*
	* @return {Instance}       an instance of the twig engine
	*/
	public static function loadDebug($instance) {
		
		if (Config::getOption("twigDebug")) {
			$instance->addExtension(new \Twig_Extension_Debug());
		}
		
		return $instance;
		
	}
	
	/**
	* Load macros for the Twig PatternEngine
	* @param  {Instance}       an instance of the twig engine
	* @param  {String}         description of the loader type for the error
	*
	* @return {Instance}       an instance of the twig engine
	*/
	public static function loadMacros($instance, $instanceType) {
		
		// load defaults
		$macroDir = Config::getOption("sourceDir").DIRECTORY_SEPARATOR."_macros";
		$macroExt = Config::getOption("twigMacroExt");
		$macroExt = $macroExt ? $macroExt : "macro";
		
		if (is_dir($macroDir)) {
			
			// loop through some macro containing dir and run...
			$finder = new Finder();
			$finder->files()->name("*.".$macroExt)->in($macroDir);
			$finder->sortByName();
			foreach ($finder as $file) {
				$instance->addGlobal($file->getBasename(".".$macroExt), $instance->loadTemplate($file->getBasename()));
			}
			
		} else {
			
			// write warning because the macro dir doesn't exist
			$macroDirHR = Console::getHumanReadablePath($macroDir);
			Console::writeWarning("the path <path>".$macroDirHR."</path> doesn't exist so macros won't be loaded for the ".$instanceType." loader...");
			
		}
		
		return $instance;
		
	}
	
}
