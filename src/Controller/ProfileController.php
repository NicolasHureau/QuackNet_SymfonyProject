<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/duck/edit', name: 'app_duck_profile', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $duck = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $duck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quack_index');
        }

        return $this->render('profile/profile.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }

    #[Route('/duck/changePass', name: 'change_password', methods: ['GET', 'POST'])]

    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $duck = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $duck);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            if ($passwordHasher->isPasswordValid($duck, $oldPassword)) {
                $newPassword = $form->get('newPassword')->getData();
                $encodedPassword = $passwordHasher->hashPassword($duck, $newPassword);

                $duck->setPassword($encodedPassword);

                $entityManager->getEventManager();
                $entityManager->persist($duck);
                $entityManager->flush();

                $this->addFlash('success', 'Password changed successfully.');

                // Rediriger vers la page appropriée après le changement de mot de passe
                return $this->redirectToRoute('app_duck_profile');
            } else {
                $this->addFlash('error', 'Old password is incorrect.');
            }
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}