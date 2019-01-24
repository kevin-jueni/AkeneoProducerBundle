<?php

namespace Sylake\AkeneoProducerBundle\Connector\JobParameters;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;

/**
 * Non-final just to make it lazy-loadable.
 */
/* final */

class AkeneoProducer implements ConstraintCollectionProviderInterface, DefaultValuesProviderInterface
{
    /**
     * @var DefaultValuesProviderInterface
     */
    private $baseDefaultValuesProvider;

    /**
     * @var ConstraintCollectionProviderInterface
     */
    private $baseConstraintCollectionProvider;

    /**
     * @var string[]
     */
    private $supportedJobNames;

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $category;

    /**
     * @param DefaultValuesProviderInterface $baseDefaultValuesProvider
     * @param ConstraintCollectionProviderInterface $baseConstraintCollectionProvider
     * @param string[] $supportedJobNames
     * @param string[] $locales
     * @param string $channel
     * @param string $category
     */
    public function __construct(
        DefaultValuesProviderInterface $baseDefaultValuesProvider,
        ConstraintCollectionProviderInterface $baseConstraintCollectionProvider,
        array $supportedJobNames,
        array $locales,
        string $channel,
        string $category
    ) {
        $this->baseDefaultValuesProvider = $baseDefaultValuesProvider;
        $this->baseConstraintCollectionProvider = $baseConstraintCollectionProvider;
        $this->supportedJobNames = $supportedJobNames;
        $this->locales = $locales;
        $this->channel = $channel;
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        return array_replace($this->baseDefaultValuesProvider->getDefaultValues(), [
            'with_media' => true,
            'filters' => [
                'data' => [
//                    [
//                        'field' => 'enabled',
//                        'operator' => '=',
//                        'value' => true
//                    ],
                    [
                        'field' => 'categories',
                        'operator' => 'IN CHILDREN',
                        'value' => [
                            $this->category,
                        ],
                    ],
                    [
                        'field' => 'completeness',
                        'operator' => '>=',
                        'value' => 100,
                        'context' => [
                            'locales' => $this->locales,
                        ],

                    ],
                ],
                'structure' => [
                    'scope' => $this->channel,
                    'locales' => $this->locales,
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection()
    {
        return $this->baseConstraintCollectionProvider->getConstraintCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames, true);
    }
}
