<?php

namespace Metalogico\Formello\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeFormelloCommand extends Command
{
    protected $signature = 'make:formello {--model=}';
    protected $description = 'Create a new Formello form class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $model = $this->option('model');
        
        if (!$model) {
            $this->error('The --model option is required.');
            return;
        }

        $modelClass = $this->qualifyModel($model);
        
        if (!class_exists($modelClass)) {
            $this->error("Model {$modelClass} does not exist.");
            return;
        }

        $formName = class_basename($model) . 'Form';
        $formPath = app_path("Forms/{$formName}.php");

        if ($this->files->exists($formPath)) {
            $this->error("Form {$formName} already exists!");
            return;
        }

        $this->makeDirectory($formPath);

        // compiling the stub file
        $stubPath = __DIR__.'/stubs/formello.stub';
        $stub = $this->files->get($stubPath);
        $content = $this->replaceNamespace($stub, $formName);
        $content = $this->replaceClass($content, $formName);
        $content = $this->replaceModel($content, $model);
        $content = $this->replaceFields($content, $modelClass);
        $this->files->put($formPath, $content);

        $this->info("Form {$formName} created successfully.");
    }

    protected function qualifyModel($model)
    {
        $model = ltrim($model, '\\/');
        $model = str_replace('/', '\\', $model);
        $rootNamespace = $this->laravel->getNamespace();

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('Models'))
            ? $rootNamespace.'Models\\'.$model
            : $rootNamespace.$model;
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    protected function replaceNamespace($stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace'],
            [$this->getNamespace($name), $this->rootNamespace()],
            $stub
        );
        return $stub;
    }

    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        $stub = str_replace('DummyClass', $class, $stub);
        return $stub;
    }

    protected function replaceModel($stub, $model)
    {
        $stub = str_replace('DummyModel', $model, $stub);
        return $stub;
    }

    protected function replaceFields($stub, $modelClass)
    {
        $model = new $modelClass;
        $fillable = $model->getFillable();
    
        $fields = '';
        foreach ($fillable as $field) {
            $fields .= "            '{$field}' => [\n";
            $fields .= "                'label' => __('" . Str::title(str_replace('_', ' ', $field)) . "'),\n";
            $fields .= "            ],\n";
        }
    
        $stub = str_replace('DummyFields', $fields, $stub);
        return $stub;
    }

    protected function getNamespace($name)
    {
        return 'App\\Forms';
    }

    protected function rootNamespace()
    {
        return $this->laravel->getNamespace();
    }
}