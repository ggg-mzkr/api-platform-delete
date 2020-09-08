<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPersister implements ContextAwareDataPersisterInterface
{
    private $encoder;

    private $entityManager;

    /**
     * UserPersister constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager
    ) {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function persist($data, array $context = [])
    {
        if (!$data instanceof User) {
            return $data;
        }

        if ($data->getPlainPassword()) {
            $data->setPassword($this->encoder->encodePassword($data, $data->getPlainPassword()));
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        return $data;
    }

    public function remove($data, array $context = [])
    {
        return $data;
    }
}