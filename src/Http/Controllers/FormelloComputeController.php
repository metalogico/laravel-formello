<?php

namespace Metalogico\Formello\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\Support\FormelloState;

class FormelloComputeController
{
    public function handle(Request $request): JsonResponse
    {
        $request->validate([
            'form_class' => ['required', 'string'],
            'changed_field' => ['required', 'string'],
            'form_data' => ['required', 'array'],
        ]);

        $form_class = $request->input('form_class');
        $changed_field = $request->input('changed_field');
        $form_data = $request->input('form_data', []);

        // Security: only allow registered form classes
        $allowed = config('formello.reactive.allowed_forms', []);
        if (! empty($allowed) && ! in_array($form_class, $allowed)) {
            return response()->json(['error' => 'Form class not allowed'], 403);
        }

        if (! class_exists($form_class)) {
            return response()->json(['error' => 'Form class not found'], 404);
        }

        // Resolve model from request (model_class + model_id if editing)
        $model = $this->resolveModel($request);

        $form = new $form_class($model, new ViewErrorBag);

        $field_config = $form->getFieldConfig($changed_field);
        $server_callbacks = $field_config['reactive']['server'] ?? null;

        if (! $server_callbacks) {
            return response()->json([]);
        }

        // Normalize to array
        $callbacks = is_array($server_callbacks) ? $server_callbacks : [$server_callbacks];

        $state = new FormelloState($form_data);

        foreach ($callbacks as $method) {
            if (method_exists($form, $method)) {
                $form->{$method}($state);
            }
        }

        return response()->json($state->getChanges());
    }

    protected function resolveModel(Request $request): mixed
    {
        $model_class = $request->input('model_class');
        $model_id = $request->input('model_id');

        if ($model_class && class_exists($model_class)) {
            if ($model_id) {
                return $model_class::findOrFail($model_id);
            }

            return new $model_class;
        }

        // Fallback: anonymous model
        return new class extends \Illuminate\Database\Eloquent\Model {};
    }
}
