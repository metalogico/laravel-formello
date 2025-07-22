<?php

namespace Tests\Unit;

use Metalogico\Formello\Widgets\ColorWidget;
use PHPUnit\Framework\TestCase;

class ColorWidgetTest extends TestCase
{
    public function test_color_widget_is_instantiated_and_renders()
    {
        $widget = new ColorWidget();
        
        $this->assertEquals('color', $widget->getWidgetName());
        
        $viewData = $widget->getViewData('test_color', '#ff0000', [
            'label' => 'Choose Color'
        ]);
        
        $this->assertEquals('test_color', $viewData['name']);
        $this->assertEquals('#ff0000', $viewData['value']);
        $this->assertEquals('Choose Color', $viewData['label']);
        $this->assertArrayHasKey('data-formello-colorpicker', $viewData['config']['attributes']);
        
        // Check that Pickr options are properly encoded
        $pickrOptions = json_decode($viewData['config']['attributes']['data-formello-colorpicker'], true);
        $this->assertEquals('nano', $pickrOptions['theme']);
        $this->assertEquals('#ff0000', $pickrOptions['default']);
        $this->assertTrue($pickrOptions['components']['preview']);
        $this->assertTrue($pickrOptions['components']['opacity']);
        $this->assertTrue($pickrOptions['components']['hue']);
    }
}
