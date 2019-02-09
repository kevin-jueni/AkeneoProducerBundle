<?php

namespace Sylake\AkeneoProducerBundle\Connector\Listener;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Sylake\AkeneoProducerBundle\Connector\ItemSetInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class AttributeSavedListener
{
    /** @var ItemSetInterface */
    private $itemSet;

    public function __construct(ItemSetInterface $itemSet)
    {
        $this->itemSet = $itemSet;
    }

    public function __invoke(GenericEvent $event)
    {
        $attribute = $event->getSubject();

        if (!$attribute instanceof AttributeInterface) {
            return;
        }

        $this->itemSet->add($attribute);

        foreach ($attribute->getOptions() as $attributeOption) {
            $this->itemSet->add($attributeOption);
        }
    }
}
