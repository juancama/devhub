<?php
declare(strict_types=1);

namespace Colvin\DeveloperHub\Ui\Web;

use Colvin\DeveloperHub\Application\Developer\FindDeveloperByUserNameQuery;
use Colvin\DeveloperHub\Application\Developer\FindDeveloperByUserNameUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeveloperSearchPage extends AbstractController
{
    private FindDeveloperByUserNameUseCase $useCase;

    public function __construct(FindDeveloperByUserNameUseCase $useCase)
    {
        $this->useCase = $useCase;
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

        $developer = $this->useCase->__invoke(
            new FindDeveloperByUserNameQuery(
                $userName
            )
        );

        return $developer ? $developer->payload() : null;
    }
}
