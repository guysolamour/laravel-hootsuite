<?php

namespace Guysolamour\Hootsuite\Tests;

use Guysolamour\Hootsuite\Facades\Hootsuite;
use Guysolamour\Hootsuite\ServiceProvider;
use Orchestra\Testbench\TestCase;

class HootsuiteTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'hootsuite' => Hootsuite::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
