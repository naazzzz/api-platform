<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Entity\UserCar;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class UserProcessor implements ProcessorInterface
{

    public function __construct(
        public UserPasswordHasherInterface $hasher,
        public ProcessorInterface          $innerProcessor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof User && $data->getPlainPassword()) {
            $data->setPassword($this->hasher->hashPassword($data, $data->getPlainPassword()));
            $car = new UserCar();
            $car->setUser($data);
            $data->setUsersCars($car);
        }

        $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
