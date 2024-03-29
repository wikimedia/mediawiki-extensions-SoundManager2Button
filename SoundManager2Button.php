<?php
# Initialization file for SoundManager2Button extension.
#
# @file SoundManager2Button.php
# @ingroup SoundManager2Button
# @defgroup SoundManager2Button SoundManager2Button
#
# @author kroocsiogsi
#
# @license GNU GPL v3
#   The SoundManager 2 files are licensed under a BSD license,
#   which is included with this extension.
#
# Tag:
#   <sm2>uploaded filename.mp3</sm2>
#
# Required in $wgExtensionAssetsPath/SoundManager2:
#
#   SoundManager2Button.php
#
#   css/mp3-player-button.css
#   css/debug.css
#
#   image/arrow-right-black-2x.png
#   image/arrow-right-black.gif
#   image/arrow-right-black.png
#   image/arrow-right-white-2x.png
#   image/arrow-right-white.gif
#   image/arrow-right-white.png
#
#   script/mp3-player-button.js
#
#   script/soundmanager2.js
#   script/soundmanager2-jsmin.js
#   script/soundmanager2-nodebug.js        <- We use this one in production.
#   script/soundmanager2-nodebug-jsmin.js
#
#   swf/soundmanager2.swf                  <- We use this one in production.
#   swf/soundmanager2_debug.swf
#   swf/soundmanager2_flash9.swf
#   swf/soundmanager2_flash9_debug.swf
#
# To activate the extension:
#   At the end of your LocalSettings.php: require_once("extensions/SoundManager2Button/SoundManager2Button.php");
#
# For debug mode:
#   In $wgResourceModules: add css/debug.css
#   In $wgResourceModules: replace script/soundmanager2-nodebug.js with script/soundmanager2.js
#   In mp3-player-button.js: toggle soundManager.debugMode = false;

use MediaWiki\MediaWikiServices;

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the SoundManager2Button MediaWiki extension. It cannot be run standalone.\n";
	die( -1 );
}

$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'SoundManager2Button',
	'author'         => 'kroocsiogsi',
	'url'            => 'https://www.mediawiki.org/wiki/Extension:SoundManager2Button',
	'descriptionmsg' => 'soundmanager2button-desc',
	'version'        => '0.4.2',
);

$wgMessagesDirs['SoundManager2Button'] = __DIR__ . '/i18n';

$wgHooks['ParserFirstCallInit'][] = 'wfSoundManager2Button';

$wgResourceModules['ext.wfSoundManager2Button'] = array(
	'scripts' => array( 'script/soundmanager2-nodebug.js', 'script/mp3-player-button.js' ),
	'styles' => 'css/mp3-player-button.css',
	'localBasePath' => dirname( __FILE__ ),
	'remoteExtPath' => 'SoundManager2Button',
);

/**
 * Hook our callback function into the parser
 *
 * @param $parser Parser
 * @return bool
 */
function wfSoundManager2Button( &$parser ) {
	$parser->setHook( 'sm2', 'renderSM2' );
	return true;
}

/**
 * Execute
 * @param $input
 * @param $args
 * @param $parser Parser
 * @param $frame PPFrame
 * @return string
 */
function renderSM2( $input, $args, $parser, $frame ) {
	$parser->getOutput()->addModules( [ 'ext.wfSoundManager2Button' ] );
	$input = $parser->recursiveTagParse( $input, $frame );

	if ( method_exists( MediaWikiServices::class, 'getRepoGroup' ) ) {
		// MediaWiki 1.34+
		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile($input);
	} else {
		$file = wfFindFile($input);
	}
	$output = '';
	if( $file ) {
		$url = $file->getFullURL();
		$output='<a href="'.$url.'" title="'.wfMessage('soundmanager2button-play')->inContentLanguage()->escaped().'" class="sm2_button">'.wfMessage('soundmanager2button-play')->inContentLanguage()->escaped().'</a>';
	}

	return $output;
}
