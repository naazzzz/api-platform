<?php

namespace App\Serializer;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        public SerializerContextBuilderInterface $decorated,
        public AuthorizationCheckerInterface     $authorizationChecker
    )
    {
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;


        if ($resourceClass === User::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_ADMIN') && true === $normalization) {
            $context['groups'][] = 'GetUserFromAdmin';
            $context['groups'][] = 'GetDateFromAdmin';
        }
        if ($resourceClass === User::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_ADMIN') && false === $normalization) {
            $context['groups'][] = 'SetUserFromAdmin';
        }

        return $context;
    }
}