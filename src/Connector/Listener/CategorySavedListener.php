<?php

namespace Sylake\AkeneoProducerBundle\Connector\Listener;

use Akeneo\Tool\Component\Classification\Model\CategoryInterface;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Akeneo\Pim\Permission\Bundle\Entity\ProductCategoryAccess;
use Sylake\AkeneoProducerBundle\Connector\ItemSetInterface;

final class CategorySavedListener
{
    /** @var ItemSetInterface */
    private $itemSet;

    public function __construct(ItemSetInterface $itemSet)
    {
        $this->itemSet = $itemSet;
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this($event);
    }

    public function __invoke(LifecycleEventArgs $event)
    {
        $category = $event->getObject();

        if (!$category instanceof CategoryInterface && /*!$category instanceof CategoryTranslation &&*/
            !$category instanceof ProductCategoryAccess) {
            return;
        }

//        if ($category instanceof CategoryTranslation) {
//            $category = $category->getObject();
//        }

        if ($category instanceof ProductCategoryAccess) {
            $category = $category->getCategory();
        }

        $this->itemSet->add($category);
    }
}
