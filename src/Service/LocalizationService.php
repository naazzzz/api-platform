<?php

namespace App\Service;
use App\Entity\Localization\Locale;
use App\Entity\Product;

class LocalizationService
{
    public function localize(string $propertyTranslate, Product $product, string $locale){
        $translation = $product->propertyTranslations->filter(function (Locale $locale) use ($propertyTranslate){
            return $locale->propertyName === $propertyTranslate;
        })->first();

        if(!$translation)
            return;


        $product->$propertyTranslate = $translation->$locale;
        return $product;
    }

}