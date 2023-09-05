<?php

namespace App\Entity\Localization;

use App\Entity\BaseEntity;
use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Locale extends BaseEntity
{
    public const DEFAULT_LOCALE = 'EU';

    public const LANGUAGES = [
        'RU',
        'EU'
    ];

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $ru = null;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $eu = null;

    #[ORM\Column(type: "string")]
    public string $propertyName;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    public Product $product;
}