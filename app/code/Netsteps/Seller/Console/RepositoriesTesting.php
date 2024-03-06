<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Console;

use Magento\Framework\App\State;
use Netsteps\Seller\Api\SellerOptionRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepositoriesTesting extends Command
{
    protected function configure()
    {
        $this->setName('netsteps:testing_repositories');
        $this->setDescription('Test code execution');
        parent::configure();
    }

    public function __construct(
        State $state
    )
    {
        $this->_state = $state;
        parent::__construct();
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $ob = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var SellerOptionRepositoryInterface $repo */
        $repo = $ob->get(SellerOptionRepositoryInterface::class);

        $instance = $repo->getEmptyOptionModel();
        $instance->setSellerId(23);
        $instance->setOptionName('description');
        $instance->setOptionValue('poutsos');
        $instance->setStoreId(0);
        $repo->save($instance);
//        /** @var SellerAdminInterface $interface */
//        $interface = $ob->get(\Netsteps\Seller\Api\Data\SellerAdminInterfaceFactory::class)->create();
//        $interface->setAdminUserId(2);
//        $interface->setSellerId(13);
//        dd($repo->getByUserId(2)->getData());

    }
}
