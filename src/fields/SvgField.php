<?php

namespace servd\svgfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Html;
use craft\web\assets\cp\CpAsset;
use yii\db\Schema;
use servd\svgfield\services\SvgSanitizer;

class SvgField extends Field
{
    public static function displayName(): string
    {
        return Craft::t('svg-field', 'SVG Field');
    }

    public static function icon(): string
    {
        return 'image';
    }

    public function getContentColumnType(): array|string
    {
        return Schema::TYPE_TEXT;
    }

    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Sanitize SVG content
        try {
            $sanitizer = new SvgSanitizer();
            return $sanitizer->sanitize($value);
        } catch (\Exception $e) {
            // Return original value if sanitization fails, validation will catch it
            return $value;
        }
    }

    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        return parent::serializeValue($value, $element);
    }

    protected function inputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(CpAsset::class);

        $id = Html::id($this->handle);
        $namespacedId = $view->namespaceInputId($id);

        return $view->renderTemplate('svg-field/_input', [
            'name' => $this->handle,
            'value' => $value,
            'field' => $this,
            'id' => $id,
            'namespacedId' => $namespacedId,
        ]);
    }

    public function getElementValidationRules(): array
    {
        $rules = parent::getElementValidationRules();
        
        $rules[] = [
            'validateSvgContent',
            'skipOnEmpty' => true,
        ];

        return $rules;
    }

    public function validateSvgContent(ElementInterface $element): void
    {
        $value = $element->getFieldValue($this->handle);
        
        if (!$value) {
            return;
        }

        // Use sanitizer for validation
        $sanitizer = new SvgSanitizer();
        if (!$sanitizer->isValidSvg($value)) {
            $element->addError($this->handle, Craft::t('svg-field', 'Invalid or potentially dangerous SVG content.'));
        }
    }

    public function getSvgContent(): ?string
    {
        return $this->value;
    }
}