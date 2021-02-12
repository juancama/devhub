<?php
declare(strict_types=1);

namespace Colvin\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Colvin\DeveloperHub\Domain\Developer\UserName;
use Colvin\DeveloperHub\Domain\Service\Developer\DeveloperFinder;

class GitHubDeveloperFinder implements DeveloperFinder
{
    public function findByUserName(UserName $userName): array
    {
        // TODO: search developer on GitHub
//        {
//          “userName”: “thecolvinco”,
//          “followBacks”: {
//            “count”: 12,
//            “userNames”: [...],
//           }
//        }

        return  [
            'userName' => 'thecolvinco',
            'followBacks' => [
                'count' => 12,
                'userNames' =>  [

                ],
            ]
        ];
    }
}
