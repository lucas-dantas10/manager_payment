<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $seed = true;

    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::where('is_admin', true)->first();
        $this->actingAs($user);
    }
}
