<?php

namespace App\UserBundle\Controllers;

use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Validator\Validator;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\User;
use App\Entity\UserCar;
use App\Repository\UserRepository;
use App\Service\EmailSender;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsController]
class UserRegistrationController extends AbstractController
{
    public function __construct(
        public RateLimiterFactory $anonymousApiLimiter,
        public EmailSender $emailSender,
        public EntityManagerInterface $entityManager,
        public ValidatorInterface $validator,
        public UserPasswordHasherInterface $hasher,
    )
    {
    }

    /**
     * @param User $data
     * @param Request $request
     * @return User
     * @throws TransportExceptionInterface
     */
    public function __invoke(User $data, Request $request): User
    {

        $limiter = $this->anonymousApiLimiter->create($request->getClientIp());

        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $this->validator->validate($data);

        if ($data->getPlainPassword()) {
            $data->setPassword($this->hasher->hashPassword($data, $data->getPlainPassword()));
            $car = new UserCar();
            $car->setUser($data);
            $data->setUsersCars($car);
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->emailSender->sendMail($data);

        return $data;

    }

}