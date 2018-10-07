<?php

namespace App\Classes;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

class AsIsNamingStrategy implements PropertyNamingStrategyInterface
{
    public function translateName(PropertyMetadata $metadata)
    {
        return $metadata->name;
    }

}