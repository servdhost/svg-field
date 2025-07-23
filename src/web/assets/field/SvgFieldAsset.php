<?php

namespace servd\svgfield\web\assets\field;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SvgFieldAsset extends AssetBundle
{
    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/dist';
        $this->depends = [CpAsset::class];
        $this->js = ['svg-field.js'];
        $this->css = ['svg-field.css'];
        parent::init();
    }
}