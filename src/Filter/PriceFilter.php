<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Common\Filter\RangeFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class PriceFilter extends AbstractFilter implements RangeFilterInterface
{

    protected function filterProperty(string $property,
                                      $value,
                                      QueryBuilder $queryBuilder,
                                      QueryNameGeneratorInterface $queryNameGenerator,
                                      string $resourceClass,
                                      Operation $operation = null,
                                      array $context = []): void
    {



        if ($property == "page") {
            return;
        }
        if($property == 'minValue'){
            $parameterName = $queryNameGenerator->generateParameterName($property);
            $alias = $queryBuilder->getRootAliases()[0];
            foreach ($this->properties as $key => $item){
                $queryBuilder
                    ->andWhere(sprintf('%1$s.%2$s >= :%3$s_1', $alias, $key, $parameterName))
                    ->setParameter(sprintf('%s_1', $parameterName), $value);
            }
        }
        if($property == 'maxValue'){
            $parameterName = $queryNameGenerator->generateParameterName($property);
            $alias = $queryBuilder->getRootAliases()[0];
            foreach ($this->properties as $key => $item){
                $queryBuilder
                    ->andWhere(sprintf('%1$s.%2$s <= :%3$s_1', $alias, $key, $parameterName))
                    ->setParameter(sprintf('%s_1', $parameterName), $value);
            }
        }





    }


    public function getDescription(string $resourceClass): array
    {
        return [
            'minValue'=>[
                'property'=>null,
                'type'=>'int',
                'required'=>false,
                'description'=>'Введите минимальное значение'
            ],
            'maxValue'=>[
                'property'=>null,
                'type'=>'int',
                'required'=>false,
                'description'=>'Введите максимальное значение'
            ]
        ];
    }
}