<?php

namespace Sylake\AkeneoProducerBundle\Connector\Listener;

use Akeneo\Pim\Enrichment\Component\Product\Model\GroupTranslationInterface;
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
        $group = $event->getObject();

        if (!$group instanceof GroupInterface && !$group instanceof GroupTranslationInterface) {
            return;
        }

        if ($group instanceof GroupInterface) {
            $this->itemSet->add($group);
        }

        if ($group instanceof GroupTranslationInterface) {
            $this->itemSet->add($group->getForeignKey());
        }
    }
}
