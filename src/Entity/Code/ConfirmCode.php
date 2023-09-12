<?php

namespace App\Entity\Code;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Code\ConfirmRegistrationController;
use App\Controller\Code\ConfirmResetPassController;
use App\Entity\User;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    description: 'Подтверждение пользователей',
    operations: [
        new Post(
            uriTemplate: '/confirm-registration',
            controller: ConfirmRegistrationController::class
        ),
        new Post(
            uriTemplate: '/reset-password',
            controller: ConfirmResetPassController::class,
            security: "is_granted('ROLE_USER')",
        ),
        new Post(
            uriTemplate: '/resend-activation-code',
//            controller: ActionResendRegisterCode::class
        )
    ],
)]
class ConfirmCode
{
    public string $code;

}