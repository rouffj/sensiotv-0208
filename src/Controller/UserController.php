<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/edit", name="edit_user")
     */
    public function userEdit(): Response
    {
        return new Response('You can edit your profile on /user/edit');
    }

    /**
     * @Route("/admin/user_add", name="user_add")
     */
    public function adminUserAdd(): Response
    {
        return new Response('You can add new user on /admin/user_add');
    }

    /**
     * @Route("/admin/user_remove", name="user_add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminUserRemove(): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_SUPERVISOR');

        return new Response('You can remove user on /admin/user_remove');
    }
}
