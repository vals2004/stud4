<?php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserProvider implements UserProviderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserProvider constructor
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager 
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Load user by username
     *
     * @param String $username
     *
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */ 
    public function loadUserByUsername($username)
    {
        $query = $this->entityManager
            ->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.confirmationCode IS NULL')
            ->andWhere('u.email = :email')
            ->orWhere('u.phone = :phone')
            ->setParameter('email', $username)
            ->setParameter('phone', $username)
            ->setMaxResults(1)
        ;

        $user = $query->getQuery()
            ->getOneOrNullResult()
        ;

        if (!$user) {
            throw new UsernameNotFoundException;
        }

        return $user;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return 
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->entityManager
            ->getRepository('App:User')
            ->find($user->getId());
    }

    /**
     * @param string
     * @return boolean
     */
    public function supportsClass($class)
    {
        return $class === User::class;
    }
}

