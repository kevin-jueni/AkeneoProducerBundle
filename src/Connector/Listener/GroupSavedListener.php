<?php

namespace Sylake\AkeneoProducerBundle\Connector\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Akeneo\Pim\Enrichment\Component\Category\Model\GroupInterface;
use Sylake\AkeneoProducerBundle\Connector\ItemSetInterface;

final class GroupSavedListener
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

        if (!$category instanceof GroupInterface) {
            return;
        }

        $this->itemSet->add($category);
    }
}
