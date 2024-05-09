<?php

namespace Tests;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\InteractsWithAuthenticationRoles;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions, InteractsWithAuthenticationRoles;
}
