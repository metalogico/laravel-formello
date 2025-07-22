<?php

namespace Tests\Unit;

use Metalogico\Formello\Widgets\ColorSwatchWidget;
use PHPUnit\Framework\TestCase;

class ColorSwatchWidgetTest extends TestCase
{
    public function test_colorswatch_widget_is_instantiated_and_renders()
    {
        $widget = new ColorSwatchWidget();
        
        $this->assertEquals('colorswatch', $widget->getWidgetName());
        
        $viewData = $widget->getViewData('test_swatch', '#ff0000', [
            'label' => 'Choose Color from Swatches'
        ]);
        
        $this->assertEquals('test_swatch', $viewData['name']);
        $this->assertEquals('#ff0000', $viewData['value']);
        $this->assertEquals('Choose Color from Swatches', $viewData['label']);
        $this->assertArrayHasKey('data-formello-colorpicker', $viewData['config']['attributes']);
        
        // Check that Pickr options are properly configured for swatches-only
        $pickrOptions = json_decode($viewData['config']['attributes']['data-formello-colorpicker'], true);
        $this->assertEquals('nano', $pickrOptions['theme']);
        $this->assertEquals('#ff0000', $pickrOptions['default']);
        
        // Check that picker components are disabled
        $this->assertFalse($pickrOptions['components']['preview']);
        $this->assertFalse($pickrOptions['components']['opacity']);
        $this->assertFalse($pickrOptions['components']['hue']);
        $this->assertFalse($pickrOptions['components']['interaction']['hex']);
        
        // Check that swatches are present
        $this->assertArrayHasKey('swatches', $pickrOptions);
        $this->assertIsArray($pickrOptions['swatches']);
        $this->assertNotEmpty($pickrOptions['swatches']);
        $this->assertContains('#f44336', $pickrOptions['swatches']);
    }
    
    public function test_colorswatch_widget_uses_color_template()
    {
        $widget = new ColorSwatchWidget();
        
        // Mock the app helper to return a framework
        $template = str_replace(app('formello')->getCssFramework(), 'bootstrap5', $widget->getTemplate());
        $this->assertEquals('formello::widgets.bootstrap5.color', $template);
    }
}
