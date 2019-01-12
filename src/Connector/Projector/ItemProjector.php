<?php

namespace Sylake\AkeneoProducerBundle\Connector\Projector;

use Akeneo\Component\Batch\Item\ItemWriterInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ItemProjector implements ItemProjectorInterface
{
    /** @var NormalizerInterface */
    private $normalizer;

    /** @var ItemWriterInterface */
    private $writer;

    /** @var DefaultValuesProviderInterface|null */
    private $parametersProvider;

    public function __construct(
        NormalizerInterface $normalizer,
        ItemWriterInterface $writer,
        DefaultValuesProviderInterface $valuesProvider = null
    ) {
        $this->normalizer = $normalizer;
        $this->writer = $writer;
        $this->parametersProvider = $valuesProvider;
    }

    public function __invoke($item)
    {
        /*if ($this->processor instanceof StepExecutionAwareInterface) {
            $jobExecution = new JobExecution();
            $jobExecution->setUser('import');
            $jobExecution->setJobParameters(new JobParameters($this->parametersProvider->getDefaultValues()));

            $stepExecution = new StepExecution('42', $jobExecution);

            $this->processor->setStepExecution($stepExecution);
        }*/

        $this->writer->write([$this->normalizer->normalize($item)]);
    }
}
