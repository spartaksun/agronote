<?php

namespace App;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use VertigoLabs\DoctrineFullTextPostgres\ORM\Mapping\TsVector;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @throws Exception
     */
    public function boot(): void
    {
        parent::boot();
        AnnotationRegistry::loadAnnotationClass(TsVector::class);
    }
}
