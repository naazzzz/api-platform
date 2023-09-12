<?php

namespace App\Controller\Code;

use App\Entity\Code\ConfirmCode;
use App\Entity\User;
use App\Service\RedisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ConfirmRegistrationController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public RedisService $redisService,
    )
    {
    }


    public function __invoke(ConfirmCode $data, Request $request):Response
    {
        $response = $this->redisService->getAction($data->code);

            if (json_decode($response->getContent())->value){
                $email = json_decode($response->getContent())->value;

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                $user->isActivate=true;

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return new Response('Почта успешно подтверждена' ,Response::HTTP_ACCEPTED);
            }

        return new Response('Используемый код подтверждения истек или не существует',Response::HTTP_NOT_FOUND);
    }

}