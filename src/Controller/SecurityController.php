<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, UserPasswordHasherInterface $hasher, ManagerRegistry $doctrine): Response
    {
        # On appel le manager pour enregistrer un nouvel utilisateur en bdd
        $manager = $doctrine->getManager();

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        # Récupérer la requête
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            # Hasher le password :
            $password = $user->getPassword();
        
            # dans une variable on enregistre la manip de hashage
            $passwordHasher = $hasher->hashPassword($user, $password);
            dump($passwordHasher);
            # on remplace l'ancien password par sa version cryptée
            $user->setPassword($passwordHasher);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
 
        // last username entered by the user
       $lastUsername = $authenticationUtils->getLastUsername();
 
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}
