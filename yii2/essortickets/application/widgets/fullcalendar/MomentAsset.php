<?php

namespace app\widgets\fullcalendar;

/**
 * Class MomentAsset
 * @package app\widgets\fullcalendar
 */
class MomentAsset extends \yii\web\AssetBundle
{
	/** @var  array  The javascript file for the Moment library */
	public $js = [
		'moment.js',
	];
	/** @var  string  The location of the Moment.js library */
	public $sourcePath = '@bower/moment';
}