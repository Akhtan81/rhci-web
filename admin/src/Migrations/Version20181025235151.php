<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\SubscriptionType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181025235151 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function up(Schema $schema): void
    {

        $secret = $this->container->getParameter('stripe_client_secret');

        try {
            \Stripe\Stripe::setApiKey($secret);

            \Stripe\Plan::create([
                "amount" => 5000,
                "interval" => "month",
                "product" => [
                    "name" => "Purchase access to recycling orders for partners at mobilerecycling.kz"
                ],
                "currency" => "usd",
                "id" => SubscriptionType::RECYCLING_ACCESS
            ]);
        } catch (\Exception $ignore) {}

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
