<?php

namespace app\widgets\fullcalendar;

/**
 * Class PrintAsset
 * @package app\widgets\fullcalendar
 */
class PrintAsset extends \yii\web\AssetBundle
{
	/** @var  array The CSS file for the print style */
	public $css = [
		'fullcalendar.print.css',
	];
	/** @var  array The CSS options */
	public $cssOptions = [
		'media' => 'print',
	];
	/** @var  string Bower path for the print settings */
	public $sourcePath = '@widgets/fullcalendar/assets/dist';
}

