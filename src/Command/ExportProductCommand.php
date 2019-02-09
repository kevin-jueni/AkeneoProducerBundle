<?php

namespace Sylake\AkeneoProducerBundle\Command;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\ProductQueryBuilderFactoryInterface;
use Sylake\AkeneoProducerBundle\Connector\Projector\ItemProjectorInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class ExportProductCommand extends ContainerAwareCommand
{
    /** @var ProductQueryBuilderFactoryInterface */
    private $productQueryBuilderFactory;

    /** @var ItemProjectorInterface */
    private $itemProjector;

    /**
     * @param ProductQueryBuilderFactoryInterface $productQueryBuilderFactory
     * @param ItemProjectorInterface $itemProjector
     */
    public function __construct(
        ProductQueryBuilderFactoryInterface $productQueryBuilderFactory,
        ItemProjectorInterface $itemProjector
    ) {
        $this->productQueryBuilderFactory = $productQueryBuilderFactory;
        $this->itemProjector = $itemProjector;

        parent::__construct('sylake:producer:export-product');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument('sku', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sku = $input->getArgument('sku');

        $productQueryBuilder = $this->productQueryBuilderFactory->create();
        $productQueryBuilder->addFilter('sku', '=', $sku);

        $products = $productQueryBuilder->execute();

        if (count($products) === 0) {
            $output->writeln(sprintf('<error>Could not find product with SKU "%s"!</error>', $sku));

            return 1;
        }

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $output->writeln(sprintf('Exporting product with SKU "%s".', $sku));

            $this->itemProjector->__invoke($product);
        }
    }

    /**
     * @param $username
     * @return bool
     * @throws \Exception
     */
    protected function createToken(string $username): bool
    {
        $userRepository = $this->getContainer()->get('pim_user.repository.user');
        $user = $userRepository->findOneByIdentifier($username);

        if ($user === null) {
            throw new \Exception(
                sprintf(
                    'Username "%s" is unknown',
                    $username
                )
            );
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->getTokenStorage()->setToken($token);

        return true;
    }

    /**
     * @return TokenStorageInterface
     */
    protected function getTokenStorage()
    {
        return $this->getContainer()->get('security.token_storage');
    }
}
