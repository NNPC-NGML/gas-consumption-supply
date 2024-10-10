<?php

namespace Tests;

use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    public function actingAsAuthenticatedTestUser()
    {
        Http::fake([
            env("USERS_MS") . '/*' => Http::response(["id"=> 1], 200),
        ]);

    }
    public function actingAsUnAuthenticatedTestUser()
    {
        Http::fake([
            env("USERS_MS") . '/*' => Http::response('unauthorized', 401),
        ]);

    }
}
