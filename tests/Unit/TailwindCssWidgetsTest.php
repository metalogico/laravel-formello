<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

class TailwindCssWidgetsTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('formello.css_framework', 'tailwindcss4');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Metalogico\Formello\FormelloServiceProvider::class,
        ];
    }

    private function makeDummyModel()
    {
        return new class extends Model {
            public function getTable() { return 'dummy'; }
        };
    }

    private function makeForm(array $fields)
    {
        $model = $this->makeDummyModel();

        return new class($model, new ViewErrorBag(), $fields) extends Formello {
            private array $fieldDefs;

            public function __construct($model, $errors, array $field_defs)
            {
                $this->fieldDefs = $field_defs;
                parent::__construct($model, $errors);
            }

            protected function fields(): array { return $this->fieldDefs; }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_text_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('name')->widget('text')->label('Name'),
        ]);
        $output = $form->renderField('name');
        $this->assertStringContainsString('text-gray-700', $output);
        $this->assertStringContainsString('rounded-md', $output);
        $this->assertStringContainsString('name="name"', $output);
    }

    public function test_textarea_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('bio')->widget('textarea')->label('Bio'),
        ]);
        $output = $form->renderField('bio');
        $this->assertStringContainsString('<textarea', $output);
        $this->assertStringContainsString('rounded-md', $output);
    }

    public function test_select_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('country')
                ->widget('select')
                ->label('Country')
                ->choices(['it' => 'Italy', 'us' => 'USA']),
        ]);
        $output = $form->renderField('country');
        $this->assertStringContainsString('<select', $output);
        $this->assertStringContainsString('Italy', $output);
        $this->assertStringContainsString('border-gray-300', $output);
    }

    public function test_toggle_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('active')->widget('toggle')->label('Active'),
        ]);
        $output = $form->renderField('active');
        $this->assertStringContainsString('type="checkbox"', $output);
        $this->assertStringContainsString('peer', $output);
    }

    public function test_radio_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('gender')
                ->widget('radio')
                ->label('Gender')
                ->choices(['m' => 'Male', 'f' => 'Female']),
        ]);
        $output = $form->renderField('gender');
        $this->assertStringContainsString('type="radio"', $output);
        $this->assertStringContainsString('Male', $output);
        $this->assertStringContainsString('Female', $output);
    }

    public function test_checkboxes_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('tags')
                ->widget('checkboxes')
                ->label('Tags')
                ->choices(['a' => 'Alpha', 'b' => 'Beta']),
        ]);
        $output = $form->renderField('tags');
        $this->assertStringContainsString('type="checkbox"', $output);
        $this->assertStringContainsString('Alpha', $output);
    }

    public function test_range_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('volume')->widget('range')->label('Volume'),
        ]);
        $output = $form->renderField('volume');
        $this->assertStringContainsString('type="range"', $output);
        $this->assertStringContainsString('slider', $output);
    }

    public function test_date_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('birthday')->widget('date')->label('Birthday'),
        ]);
        $output = $form->renderField('birthday');
        $this->assertStringContainsString('data-formello-datepicker', $output);
        $this->assertStringContainsString('rounded-md', $output);
    }

    public function test_color_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('color')->widget('color')->label('Color'),
        ]);
        $output = $form->renderField('color');
        $this->assertStringContainsString('data-formello-colorpicker', $output);
        $this->assertStringContainsString('name="color"', $output);
    }

    public function test_upload_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('avatar')->widget('upload')->label('Avatar'),
        ]);
        $output = $form->renderField('avatar');
        $this->assertStringContainsString('type="file"', $output);
        $this->assertStringContainsString('file:', $output);
    }

    public function test_wysiwyg_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('content')->widget('wysiwyg')->label('Content'),
        ]);
        $output = $form->renderField('content');
        $this->assertStringContainsString('<textarea', $output);
        $this->assertStringContainsString('data-formello-wysiwyg', $output);
    }

    public function test_separator_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('sep')->widget('separator')->label('Section'),
        ]);
        $output = $form->renderField('sep');
        $this->assertStringContainsString('<hr', $output);
        $this->assertStringContainsString('Section', $output);
        $this->assertStringContainsString('text-gray-500', $output);
    }

    public function test_tomselect_widget_renders_tailwind()
    {
        $form = $this->makeForm([
            FormelloField::make('category')
                ->widget('tomselect')
                ->choices(['1' => 'Cat A']),
        ]);
        $output = $form->renderField('category');
        $this->assertStringContainsString('data-formello-tomselect', $output);
        $this->assertStringContainsString('Cat A', $output);
    }

    public function test_full_form_renders_tailwind_grid()
    {
        $form = $this->makeForm([
            FormelloField::make('name')->widget('text')->label('Name'),
            FormelloField::make('email')->widget('text')->label('Email')->type('email'),
        ]);
        $output = $form->render();
        $this->assertStringContainsString('grid grid-cols-12', $output);
        $this->assertStringContainsString('col-span-', $output);
    }
}
