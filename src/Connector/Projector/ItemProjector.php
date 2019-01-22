<?php

namespace Sylake\AkeneoProducerBundle\Connector\Projector;

use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Component\Batch\Item\ItemWriterInterface;
use Akeneo\Component\Batch\Job\JobParameters;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Akeneo\Component\Batch\Model\JobExecution;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Akeneo\Component\Classification\CategoryAwareInterface;
use Akeneo\Component\Classification\Model\CategoryInterface;
use Pim\Component\Catalog\Model\Product;
use Pim\Component\Catalog\Model\ProductModel;

final class ItemProjector implements ItemProjectorInterface
{
    /** @var ItemProcessorInterface */
    private $processor;

    /** @var ItemWriterInterface */
    private $writer;

    /** @var DefaultValuesProviderInterface|null */
    private $parametersProvider;

    /**
     * @var JobParameters
     */
    private $jobParameters;

    public function __construct(
        ItemProcessorInterface $processor,
        ItemWriterInterface $writer,
        DefaultValuesProviderInterface $valuesProvider = null
    ) {
        $this->processor = $processor;
        $this->writer = $writer;
        $this->parametersProvider = $valuesProvider;
        $this->jobParameters = new JobParameters($this->parametersProvider->getDefaultValues());
    }

    /**
     * @param object $item
     * @return bool
     * @throws \Akeneo\Component\Batch\Item\InvalidItemException
     * @throws \Exception
     */
    public function __invoke($item): bool
    {
        if ($this->isExportable($item) === false) {
            return false;
        }

        if ($this->processor instanceof StepExecutionAwareInterface) {
            $jobExecution = new JobExecution();
            $jobExecution->setUser('import');
            $jobExecution->setJobParameters($this->jobParameters);

            $stepExecution = new StepExecution('42', $jobExecution);

            $this->processor->setStepExecution($stepExecution);
        }

        $this->writer->write([$this->processor->process($item)]);

        return true;
    }

    /**
     * @param $item
     * @return bool
     */
    protected function isExportable($item): bool
    {
        $getRootCategory = function (CategoryInterface $category) use (&$getRootCategory) {
            if ($category->getParent()) {
                return $getRootCategory($category->getParent());
            }

            return $category;
        };

        $channel = $this->jobParameters->get('filters')['structure']['scope'];

        foreach ($this->jobParameters->get('filters')['data'] as $filter) {
            switch ($filter['field']) {
                case 'categories':
                    $isValid = false;

                    if ($item instanceof CategoryAwareInterface) {
                        foreach ($item->getCategories() as $category) {
                            $rootCategory = $getRootCategory($category);

                            if (in_array($rootCategory->getCode(), $filter['value'])) {
                                $isValid = true;
                                break;
                            }
                        }

                        if ($isValid === false) {
                            return false;
                        }

                    } elseif ($item instanceof CategoryInterface) {
                        $rootCategory = $getRootCategory($item);

                        if (in_array($rootCategory->getCode(), $filter['value'])) {
                            $isValid = true;
                        }

                        if ($isValid === false) {
                            return false;
                        }
                    }
                    break;
                case 'enabled':
                    if (method_exists($item, 'isEnabled') && $item->isEnabled() !== $filter['value']) {
                        return false;
                    }
                    break;
                case 'completeness':
                    if ($item instanceof Product or $item instanceof ProductModel) {
                        foreach ($item->getCompletenesses() as $completeness) {
                            if ($channel === $completeness->getChannel()->getCode() && $completeness->getRatio() < $filter['value']) {
                                return false;
                            }
                        }
                    }
                    break;
            }
        }

        return true;
    }
}
