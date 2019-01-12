<?php

namespace Sylake\AkeneoProducerBundle\Connector\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use PimEnterprise\Component\ProductAsset\Model\AssetInterface;
use Sylake\AkeneoProducerBundle\Connector\ItemSetInterface;

final class AssetSavedListener
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
        $asset = $event->getObject();

        if (!$asset instanceof AssetInterface) {
            return;
        }

        $this->itemSet->add($asset);
    }
}
