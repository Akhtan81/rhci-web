<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\PartnerStatus;
use App\Service\PartnerService;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181108110150 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function up(Schema $schema): void
    {

        $partnerService = $this->container->get(PartnerService::class);
        $em = $this->container->get('doctrine')->getManager();

        $partners = $partnerService->findByFilter([
            'status' => PartnerStatus::APPROVED
        ]);
        foreach ($partners as $partner) {
            try {
                $partnerService->createCustomer($partner, true);

                $em->persist($partner);
            } catch (\Exception $ignore) {}
        }

        $em->flush();
    }

    public function down(Schema $schema): void
    {

    }

    /**
     * Sets the container.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
