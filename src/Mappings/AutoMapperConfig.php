<?php

namespace App\Mappings;


use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\MappingOperation\Operation;

use App\Entity as Ent;
use App\Dto as DTO;
use App\PropertyAccessors\CustomDoctrineProxyPropertyAccessor;

class AutoMapperConfig implements AutoMapperConfiguratorInterface
{

    public function configure(AutoMapperConfigInterface $config):void    
    {        
        $config->getOptions()->setPropertyAccessor(new CustomDoctrineProxyPropertyAccessor());

        
        $config->registerMapping(Ent\Register::class, DTO\RegisterDto::class);
        
        
    }
}
