<?php

namespace App\Service;

use App\Entity\Media;
use App\Entity\MediaType;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param UploadedFile $file
     *
     * @return Media
     * @throws \Exception
     */
    public function create(UploadedFile $file)
    {
        $em = $this->container->get('doctrine')->getManager();

        $hash = hash_file('md5', $file->getPathname());

        $entity = $this->findOneByFilter([
            'hash' => $hash
        ]);
        if ($entity) return $entity;

        $name = md5(uniqid()) . '.' . $file->getClientOriginalExtension();

        $link = $this->upload($file, $name);

        $entity = new Media();
        $entity->setSize($file->getSize());
        $entity->setMimeType($file->getClientMimeType());
        $entity->setName($file->getClientOriginalName());
        $entity->setUrl($link);
        $entity->setType(MediaType::IMAGE);
        $entity->setHash($hash);

        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    private function upload(UploadedFile $image, $name)
    {
        $root = $this->container->getParameter('kernel.project_dir') . '/public';
        $imageDirectory = $this->container->getParameter('upload_image_dir');
        $host = $this->container->getParameter('project_host');

        $image->move($root . $imageDirectory, $name);

        return $host . $imageDirectory . '/' . $name;
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

        return $em->getRepository(Media::class)->countByFilter($filter);
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

        return $em->getRepository(Media::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return Media|null
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


}