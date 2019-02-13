<?php

namespace App\Service;

use App\Entity\Country;
use App\Entity\CountryTranslation;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CountryService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create($content)
    {
        $country = new Country();

        $em = $this->container->get('doctrine')->getManager();
        $localeService = $this->container->get(LocaleService::class);

        foreach ($localeService->getSupportedLocales() as $supportedLocale) {
            $trans = new CountryTranslation();
            $trans->setCountry($country);
            $trans->setLocale($supportedLocale);
            $trans->setName('');

            $em->persist($trans);

            $country->addTranslation($trans);
        }

        $this->update($country, $content);

        return $country;
    }

    public function update(Country $country, $content)
    {
        $em = $this->container->get('doctrine')->getManager();

        if (isset($content['currency'])) {
            $country->setCurrency($content['currency']);
        }

        if (isset($content['translations'])) {
            foreach ($content['translations'] as $translationContent) {

                /** @var CountryTranslation $translation */
                foreach ($country->getTranslations() as $translation) {

                    if ($translation->getLocale() === $translationContent['locale']) {

                        if (isset($translationContent['name'])) {
                            $translation->setName(trim($translationContent['name']));
                        }
                    }

                    $em->persist($translation);
                }
            }
        }

        $em->persist($country);
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

        return $em->getRepository(Country::class)->countByFilter($filter);
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

        return $em->getRepository(Country::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return Country|null
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

        if ($content instanceof Country) {
            $this->onPostSerialize($result, $locale);
        } else {
            foreach ($result as &$item) {
                $this->onPostSerialize($item, $locale);
            }
        }
        return $result;
    }

    public function onPostSerialize(&$content, $locale)
    {
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

            unset($content['translations']);
        }
    }
}