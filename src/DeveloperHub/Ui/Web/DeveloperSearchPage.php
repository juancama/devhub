<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Ui\Web;

use Jcv\DeveloperHub\Application\Developer\FindDeveloperByUserNameQuery;
use Jcv\DeveloperHub\Application\Developer\FindDeveloperByUserNameUseCase;
use Jcv\Shared\Bus\Query\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeveloperSearchPage extends AbstractController
{
    private QueryBus $queryBus;

    public function __construct(queryBus $useCase)
    {
        $this->queryBus = $useCase;
    }

    public function form(Request $request): Response
    {
        $userName = $request->get('userName');

        //todo: handle query errors and prepare to print as html error message
        return $this->render('@forms/searchDeveloper.html.twig', [
            'userName' => $userName,
            'developer' => $this->getDeveloper($userName),
        ]);
    }

    private function getDeveloper(?string $userName): ?array
    {
        if (!$userName) {
            return null;
        }

        $developer = $this->queryBus->ask(
            new FindDeveloperByUserNameQuery(
                $userName
            )
        );

        return $developer ? $developer->payload() : null;
    }
}
