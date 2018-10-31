<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\PartnerStatus;
use App\Entity\SubscriptionType;
use App\Service\CreditCardService;
use App\Service\PartnerService;
use App\Service\UserService;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181031110509 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function up(Schema $schema): void
    {

        $service = $this->container->get(CreditCardService::class);
        $em = $this->container->get('doctrine')->getManager();

        $cards = $service->findByFilter([
            'isPrimary' => true
        ]);
        foreach ($cards as $card) {
            try {
                $service->onPrimaryCardChanged($card);

                $em->persist($card);
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
