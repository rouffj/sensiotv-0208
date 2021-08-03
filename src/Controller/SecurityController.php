<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig', []);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request,
                             ValidatorInterface $validator,
                             EntityManagerInterface $entityManager
    ): Response
    {
        $user = new User();
        $user->setFirstName('test');

        $form = $this->createForm(RegisterType::class, $user);

        // Gère la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            dump($user);
        }

        return $this->render('security/register.html.twig', [
            'register_form' => $form->createView()
        ]);
    }
}
