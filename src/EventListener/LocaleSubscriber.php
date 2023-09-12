<?php

namespace App\EventListener;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\State\Pagination\PartialPaginatorInterface;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BaseEntity;
use App\Entity\Product;
use App\Service\LocalizationService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class LocaleSubscriber implements EventSubscriberInterface
{


    public function __construct(
        public LocalizationService $localizationService,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['chooseLocale', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function chooseLocale(ViewEvent $event):void
    {
        $method = $event->getRequest()->getMethod();

        if ($method=='GET'){
            $locale = $event->getRequest()->get('locale');

            if(is_null($locale)){
                return;
            }
        }
        else return;

        $data =$event->getControllerResult();
        $data = $this->extract($data);

        $needTranlate = new ArrayCollection();

        //Используем рефлексию для поиска полей с необходимой нам группой сериализации
        $reflector = new \ReflectionClass(get_class($data[0]));
        $properties = $reflector->getProperties();

        foreach ($properties as $property){
            if ($attribute = $property->getAttributes('Symfony\Component\Serializer\Annotation\Groups')){
                $groups=$attribute[0]->getArguments();
            }
            else continue;

            if(in_array(BaseEntity::S_GROUP_TRANSLATABLE ,$groups[0])){
                $needTranlate->add($property->getName());
            }
            else continue;
        }

        foreach ($data as $localizeObject){
            foreach ($needTranlate as $translateProperty){
                $this->localizationService->localize($translateProperty, $localizeObject, $locale);
            }

        }

    }

    private function extract(mixed $result): array
    {
        if ($result instanceof PartialPaginatorInterface) {
            return iterator_to_array($result);
        } else {
            return [$result];
        }
    }


}