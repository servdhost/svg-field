<?php

namespace servd\svgfield;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use servd\svgfield\fields\SvgField;
use yii\base\Event;

class Plugin extends BasePlugin
{
    public static $plugin;
    
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = false;
    public bool $hasCpSection = false;

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Register translations
        Craft::$app->i18n->translations['svgfield'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/translations',
            'fileMap' => [
                'svgfield' => 'svgfield.php',
            ],
        ];


        // Register field type
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = SvgField::class;
            }
        );

        Craft::info(
            Craft::t('svgfield', '{name} plugin loaded', ['name' => $this->name]),
            __METHOD__
        );
    }
}