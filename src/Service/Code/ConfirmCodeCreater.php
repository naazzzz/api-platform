<?php

namespace App\Service\Code;

use App\Entity\Code\ConfirmCode;
use App\Entity\User;
use App\Service\RedisService;
use Doctrine\ORM\EntityManagerInterface;

class ConfirmCodeCreater
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public RedisService $redisService
    )
    {
    }

    public function codeCreate(User $user):ConfirmCode{

        $code = new ConfirmCode();
        $code->code = md5(uniqid(rand(), true));

        $this->redisService->setAction($code->code, $user->email);

        return $code;
}
}