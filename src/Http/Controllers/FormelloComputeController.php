<?php

namespace Metalogico\Formello\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\Formello;
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

        if (! $this->isFormClassAllowed($form_class)) {
            return response()->json(['error' => 'Form class not allowed'], 403);
        }

        if (! class_exists($form_class)) {
            return response()->json(['error' => 'Form class not found'], 404);
        }

        if (! is_subclass_of($form_class, Formello::class)) {
            return response()->json(['error' => 'Form class is invalid'], 403);
        }

        // Resolve model from request (model_class + model_id if editing)
        $model = $this->resolveModel($request);

        if ($model === false) {
            return response()->json(['error' => 'Model not authorized'], 403);
        }

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

    /**
     * Fail-closed: empty whitelist rejects all.
     * Explicit ['*'] allows any Formello subclass (development only).
     */
    protected function isFormClassAllowed(string $form_class): bool
    {
        $allowed = config('formello.reactive.allowed_forms', []);

        if (! is_array($allowed) || $allowed === []) {
            return false;
        }

        if (in_array('*', $allowed, true)) {
            return true;
        }

        return in_array($form_class, $allowed, true);
    }

    /**
     * @return mixed|\Illuminate\Database\Eloquent\Model|false
     *         false when authorize_model denies access
     */
    protected function resolveModel(Request $request): mixed
    {
        $model_class = $request->input('model_class');
        $model_id = $request->input('model_id');

        if ($model_class) {
            if (! class_exists($model_class) || ! is_subclass_of($model_class, Model::class)) {
                return false;
            }

            $model = $model_id
                ? $model_class::findOrFail($model_id)
                : new $model_class;
        } else {
            $model = new class extends Model {};
        }

        $authorize = config('formello.reactive.authorize_model');

        if (is_callable($authorize) && ! $authorize($request, $model)) {
            return false;
        }

        return $model;
    }
}
