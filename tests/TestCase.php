<?php

namespace Tests;

use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    public function actingAsAuthenticatedTestUser()
    {
        $mockUserData = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            // other user attributes as needed
        ];

        Http::fake([
            env("USERS_MS") . '/*' => Http::response($mockUserData, 200),
        ]);
    }
    public function actingAsUnAuthenticatedTestUser()
    {
        Http::fake([
            env("USERS_MS") . '/*' => Http::response('unauthorized', 401),
        ]);
    }
}
