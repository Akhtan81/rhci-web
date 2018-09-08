<?php

namespace App\Service;

use App\Entity\District;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DistrictService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create($content)
    {
        $entity = new District();

        $content['locale'] = 'en';

        $this->update($entity, $content);

        return $entity;
    }

    public function update(District $district, $content)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();
        $cityService = $this->container->get(CityService::class);
        $locales = explode('|', $this->container->getParameter('supported_locales'));

        if (isset($content['locale'])) {
            if (!in_array($content['locale'], $locales)) {
                throw new \Exception($trans->trans('validation.invalid_locale'), 400);
            }
            $district->setLocale($content['locale']);
        }

        if (isset($content['postalCode'])) {
            $district->setPostalCode($content['postalCode']);
        }

        if (isset($content['name'])) {
            $district->setName($content['name']);
        }

        if (isset($content['city'])) {
            $city = $cityService->findOneByFilter([
                'id' => $content['city']
            ]);

            $district->setCity($city);
        }

        $match = $this->findOneByFilter([
            'postalCode' => $district->getPostalCode()
        ]);
        if ($match && $match !== $district) {
            throw new \Exception($trans->trans('validation.non_unique_district_postal_code'), 400);
        }

        $district->setFullName($district->getCity()->getFullName() . ', ' . $district->getName());

        $em->persist($district);
        $em->flush();
    }

    /**
     * @param array $filter
     *
     * @return int
     * @throws \Exception
     */
    public function countByFilter(array $filter = [])
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(District::class)->countByFilter($filter);
    }

    /**
     * @param array $filter
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function findByFilter(array $filter = [], $page = 0, $limit = 0)
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(District::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return District|null
     */
    public function findOneByFilter(array $filter = [])
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1'])), true);
    }

    public function serializeV2($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v2'])), true);
    }
}