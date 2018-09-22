<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshAccessTokenCommand extends ContainerAwareCommand
{

    const NAME = 'mrs:refresh-tokens';

    protected function configure()
    {
        $this->setName(self::NAME);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('[+] Started');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $userService = $this->getContainer()->get(UserService::class);

        $limit = 50;

        $filter = [
            'isActive' => true,
            'isTokenExpired' => true
        ];

        $total = $userService->countByFilter($filter);

        $pages = intval(ceil($total / $limit));

        for ($page = 1; $page <= $pages; $page++) {

            $output->writeln('[+] Processing page ' . $page);

            $users = $userService->findByFilter($filter, $page, $limit);

            /** @var User $user */
            foreach ($users as $user) {
                $user->refreshToken();

                $em->persist($user);
            }

            $em->flush();

            $em->clear();
        }

        $output->writeln('[+] Finished');
    }

}