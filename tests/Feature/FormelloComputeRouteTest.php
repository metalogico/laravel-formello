<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class FormelloComputeRouteTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Metalogico\Formello\FormelloServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('formello.reactive.compute_path', null);
        $app['config']->set('formello.reactive.middleware', ['web', 'auth']);
    }

    public function test_compute_route_is_not_registered_when_path_is_disabled(): void
    {
        $this->assertFalse(Route::has('formello.compute'));
        $this->postJson('/formello/compute', [])->assertNotFound();
    }
}
