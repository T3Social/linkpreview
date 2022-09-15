<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\linkpreview\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
    /**
     * v1.5 compatibility defer script loading
     *
     * Migrate to HumHub AssetBundle once minVersion is >=1.5
     *
     * @var bool
     */
    public $defer = true;
    
    public $publishOptions  = ['forceCopy' => false];
    
    public $sourcePath = '@linkpreview/resources';

    public $css = [
        'css/linkpreview.css',
    ];

    public $js = [
        'js/humhub.linkpreview.js'
    ];
}
