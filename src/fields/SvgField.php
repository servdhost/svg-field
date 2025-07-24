<?php

namespace servd\svgfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Html;
use servd\svgfield\web\assets\field\SvgFieldAsset;
use yii\db\Schema;

class SvgField extends Field
{
    public static function displayName(): string
    {
        return 'SVG Field';
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
        return $value;
    }

    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        return $value;
    }

    protected function inputHtml(mixed $value, ?ElementInterface $element = null, bool $inline = false): string
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(SvgFieldAsset::class);

        $id = Html::id($this->handle);
        $namespacedId = $view->namespaceInputId($id);

        $html = '<div class="svg-field" data-field-handle="' . Html::encode($this->handle) . '">';
        $html .= '<div class="svg-upload-area">';
        
        if ($value) {
            $html .= '<div class="svg-preview-container">';
            $html .= '<div class="svg-preview">' . $value . '</div>';
            $html .= '<div class="svg-actions">';
            $html .= '<button type="button" class="btn small svg-remove-btn">Remove</button>';
            $html .= '<button type="button" class="btn small svg-replace-btn">Replace</button>';
            $html .= '</div></div>';
        } else {
            $html .= '<div class="svg-upload-prompt">';
            $html .= '<div class="svg-upload-icon">';
            $html .= '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            $html .= '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>';
            $html .= '<circle cx="8.5" cy="8.5" r="1.5"/>';
            $html .= '<polyline points="21,15 16,10 5,21"/>';
            $html .= '</svg></div>';
            $html .= '<p>Click to upload an SVG file or drag and drop</p>';
            $html .= '</div>';
        }
        
        $html .= '<input type="file" class="svg-file-input" accept=".svg,image/svg+xml" style="display: none;">';
        $html .= Html::hiddenInput($this->handle, $value, [
            'id' => $namespacedId,
            'class' => 'svg-content-input'
        ]);
        $html .= '</div></div>';
        
        return $html;
    }

    public function getElementValidationRules(): array
    {
        return [
            ['validateSvgContent'],
        ];
    }

    public function validateSvgContent(ElementInterface $element): void
    {
        $value = $element->getFieldValue($this->handle);
        
        if ($value && !$this->isValidSvg($value)) {
            $element->addError($this->handle, 'The content must be a valid SVG.');
        }
    }

    private function isValidSvg(string $content): bool
    {
        $content = trim($content);
        
        if (empty($content)) {
            return true;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $loaded = $doc->loadXML($content);
        
        if (!$loaded) {
            return false;
        }

        return $doc->documentElement->tagName === 'svg';
    }
}