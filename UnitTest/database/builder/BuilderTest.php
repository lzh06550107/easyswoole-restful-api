<?php

namespace UnitTest\database\builder;

use EasySwoole\Mysqli\QueryBuilder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    protected $builder;

    protected function setUp(): void
    {
        $this->builder = new QueryBuilder();
        $this->builder->setPrefix('sc_');
    }

    public function testGetFullTable()
    {
        var_dump($this->builder->get('admin'));
    }
}
