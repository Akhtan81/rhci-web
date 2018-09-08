<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Partner;
use App\Service\CategoryService;
use App\Service\PartnerCategoryService;
use App\Service\PartnerService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncPartnerCategoryCommand extends ContainerAwareCommand
{

    const NAME = 'mrs:sync-partner-categories';

    protected function configure()
    {
        $this->setName(self::NAME);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('[+] Started');

        $categoryService = $this->getContainer()->get(CategoryService::class);
        $partnerService = $this->getContainer()->get(PartnerService::class);
        $partnerCategoryService = $this->getContainer()->get(PartnerCategoryService::class);

        $limit = 50;
        $partners = $partnerService->findByFilter();

        $total = $categoryService->countByFilter();

        $pages = intval(ceil($total / $limit));

        for ($page = 1; $page <= $pages; $page++) {

            $output->writeln('[+] Processing page ' . $page);

            $categories = $categoryService->findByFilter([], $page, $limit);

            /** @var Partner $partner */
            foreach ($partners as $partner) {
                /** @var Category $category */
                foreach ($categories as $category) {
                    $partnerCategoryService->create($partner, $category);
                }
            }
        }

        $output->writeln('[+] Finished');
    }

}