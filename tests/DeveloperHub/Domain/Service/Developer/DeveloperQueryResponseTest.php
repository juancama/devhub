<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub\Domain\Service\Developer;

use Jcv\DeveloperHub\Domain\Service\Developer\DeveloperQueryResponse;
use PHPUnit\Framework\TestCase;

class DeveloperQueryResponseTest extends TestCase
{
    /** @test */
    public function should_contain_userName_in_payload()
    {
        $userName = 'colvin';

        $developer = new DeveloperQueryResponse($userName, [], 0);

        $this->assertEquals($userName, $developer->payload()['userName']);
    }

    /** @test */
    public function should_contain_followBacks_count_in_payload()
    {
        $followersUserNames = ['jhon', 'mike', 'chloe', 'anne'];

        $totalFollowers = count($followersUserNames);

        $developer = new DeveloperQueryResponse('colvin', $followersUserNames, $totalFollowers);

        $this->assertEquals($totalFollowers, $developer->payload()['followBacks']['count']);
    }

    /** @test */
    public function should_contain_followBacks_userNames_in_payload()
    {
        $followersUserNames = ['jhon', 'mike', 'chloe', 'anne'];

        $developer = new DeveloperQueryResponse('colvin', $followersUserNames, count($followersUserNames));

        $this->assertEquals($followersUserNames, $developer->payload()['followBacks']['userNames']);
    }
}
