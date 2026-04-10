<?php

namespace JustBetter\BardLinkSuggestions\Tests\Commands;

use JustBetter\BardLinkSuggestions\Contracts\DispatchesIndexSitemaps;
use JustBetter\BardLinkSuggestions\Jobs\DispatchIndexSitemapsJob;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class DispatchIndexSitemapsJobTest extends TestCase
{
    #[Test]
    public function it_calls_an_action(): void
    {
        $this->mock(DispatchesIndexSitemaps::class, function (MockInterface $mock): void {
            $mock
                ->shouldReceive('dispatch')
                ->once();
        });

        DispatchIndexSitemapsJob::dispatch();
    }
}
