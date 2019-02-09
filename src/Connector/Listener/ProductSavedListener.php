<?php

namespace Sylake\AkeneoProducerBundle\Connector\Listener;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
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

        if ($product instanceof ProductInterface || $product instanceof ProductModelInterface) {
            if ($product instanceof ProductInterface) {
                $this->itemSet->add($product);
            } elseif ($product instanceof ProductModelInterface) {
                foreach ($product->getProducts() as $variant) {
                    $this->itemSet->add($variant);
                }
            }
        } else {
            return;
        }
    }
}
