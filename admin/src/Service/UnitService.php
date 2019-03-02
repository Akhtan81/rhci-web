<?php

namespace App\Service;

use App\Entity\Unit;
use App\Entity\UnitTranslation;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UnitService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $content
     *
     * @return Unit
     * @throws \Exception
     */
    public function create($content)
    {
        $em = $this->container->get('doctrine')->getManager();
        $localeService = $this->container->get(LocaleService::class);

        $entity = new Unit();

        foreach ($localeService->getSupportedLocales() as $supportedLocale) {

            $trans = new UnitTranslation();
            $trans->setLocale($supportedLocale);
            $trans->setUnit($entity);
            $trans->setName('');

            $em->persist($trans);

            $entity->addTranslation($trans);
        }

        $this->update($entity, $content);

        return $entity;
    }

    /**
     * @param Unit $entity
     * @param $content
     *
     * @param bool $flush
     * @throws \Exception
     */
    public function update(Unit $entity, $content, $flush = true)
    {
        $em = $this->container->get('doctrine')->getManager();

        if (isset($content['translations'])) {
            foreach ($content['translations'] as $translationContent) {

                /** @var UnitTranslation $translation */
                foreach ($entity->getTranslations() as $translation) {

                    if ($translation->getLocale() === $translationContent['locale']) {

                        if (isset($translationContent['name'])) {
                            $translation->setName(trim($translationContent['name']));
                        }
                    }

                    $em->persist($translation);
                }
            }
        }

        $em->persist($entity);

        $flush && $em->flush();
    }

    /**
     * @param Unit $entity
     *
     * @throws \Exception
     */
    public function remove(Unit $entity)
    {
        $em = $this->container->get('doctrine')->getManager();

        $now = new \DateTime();

        $translations = $em->getRepository(UnitTranslation::class)->findBy([
            'unit' => $entity->getId()
        ]);

        /** @var UnitTranslation $translation */
        foreach ($translations as $translation) {
            $translation->setDeletedAt($now);

            $em->persist($translation);
        }

        $entity->setDeletedAt($now);

        $em->persist($entity);
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

        return $em->getRepository(Unit::class)->countByFilter($filter);
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

        return $em->getRepository(Unit::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return Unit|null
     */
    public function findOneByFilter(array $filter = [])
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    public function serialize($content, $locale, $groups = [])
    {
        $groups[] = 'api_v1';

        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups($groups)), true);

        if ($content instanceof Unit) {
            $this->onPostSerialize($result, $locale);
        } else {
            foreach ($result as &$item) {
                $this->onPostSerialize($item, $locale);
            }
        }
        return $result;
    }

    public function serializeV2($content, $locale)
    {
        return $this->serialize($content, $locale, ['api_v2']);
    }

    public function onPostSerialize(&$content, $locale)
    {
        $isAdmin = $this->container->get(UserService::class)->getAdmin();

        if (isset($content['translations']) && count($content['translations'])) {

            $translation = null;

            foreach ($content['translations'] as $item) {
                if ($item['locale'] === $locale) {
                    $translation = $item;
                    break;
                }
            }

            if (!$translation) {
                $translation = $content['translations'][0];
            }

            $content['name'] = $translation['name'];
            $content['locale'] = $translation['locale'];

            if (!$isAdmin) {
                unset($content['translations']);
            }
        }
    }

}