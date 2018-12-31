<?php

namespace Sylake\AkeneoProducerBundle\Connector\Listener;

use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Sylake\AkeneoProducerBundle\Connector\ItemSetInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductSavedListener
{
    /** @var ItemSetInterface */
    private $itemSet;

    public function __construct(ItemSetInterface $itemSet)
    {
        $this->itemSet = $itemSet;
    }

    public function __invoke(GenericEvent $event)
    {
        $product = $event->getSubject();

        if (!$product instanceof ProductInterface) {
            return;
        }

        if ($product instanceof ProductInterface) {
            $this->itemSet->add($product);
        } elseif ($product instanceof ProductModelInterface) {
            foreach ($product->getProducts() as $variant) {
                $this->itemSet->add($variant);
            }
        }
    }
}
