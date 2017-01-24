<?php

namespace frontend\assets;

use frontend\assets\VpluseAsset;

/**
 * Main backend application asset bundle.
 */
class GraphicsAsset extends VpluseAsset
{

    public $css = [
        'css/daterangepicker.css',

    ];

    public $js = [
        'js/highcharts/highcharts.js',
        'js/highcharts/graphics.js',
        'js/daterangepicker/moment.min.js',
        'js/daterangepicker/daterangepicker.js',
    ];
}
