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

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Register field type
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = SvgField::class;
            }
        );

        Craft::info($this->name . ' plugin loaded', __METHOD__);
    }
}