<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    /*#[IsGranted("ROLE_ADMIN")]*/
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new Utilisateur();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        $registrationForm ->handleRequest($request);

        if ($registrationForm ->isSubmitted() && $registrationForm ->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                $registrationForm ->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $user->setAdministrateur(false);
            $user->setActif(true);
            $user->setRoles((array)"ROLE_USER");
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('gestion_gestion_app');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $registrationForm ->createView(),
        ]);
    }
}
