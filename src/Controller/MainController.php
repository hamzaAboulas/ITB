<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(ProductRepository $repository,EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $products = $repository->findAll();
/*        $user = New User();
        $plaintextPassword = "123456";

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setEmail("aboulas@gmail.com");
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        $manager->flush();*/
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'products' => $products
        ]);
    }
}
