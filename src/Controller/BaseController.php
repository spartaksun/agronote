<?php

namespace App\Controller;

use App\Entity\User;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected function getUser(): ?User
    {
        if (!$this->container->has('security.token_storage')) {
            throw new LogicException('The SecurityBundle is not registered".');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        return $token->getUser();
    }
}