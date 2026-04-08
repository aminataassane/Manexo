<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\CreatesApiToken;
use Tests\Support\CreatesTicketDependencies;

abstract class TestCase extends BaseTestCase
{
    use CreatesApiToken;
    use CreatesTicketDependencies;
}
