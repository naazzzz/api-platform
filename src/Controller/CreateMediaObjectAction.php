<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\Media;
use App\Serializer\MediaObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsController]
final class CreateMediaObjectAction extends AbstractController
{

    public function __construct(
        public MediaObjectNormalizer $normalizer,
    )
    {
    }


    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request): Media
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $mediaObject = new Media();
        $mediaObject->file = $uploadedFile;

        return $mediaObject;
    }


}