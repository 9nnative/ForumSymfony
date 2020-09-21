<?php

namespace App\DataFixtures;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
    
        $user= new User();
        $plainPassword = 'multipass';
        $password = $this->encoder->encodePassword($user, $plainPassword);
        $user->setEmail('9nnative@gmail.com');
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);
        $user->SetName('9nnative');
        $manager->persist($user);
        $manager->flush();
    }
}
