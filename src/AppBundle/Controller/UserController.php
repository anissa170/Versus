<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/users", name="afficherUsers")
     */
    public function usersAction(Request $request)
    {
        $users = $this
            ->getDoctrine()
            ->getRepository("AppBundle:User")
            ->findAll();

        return $this->render('default/sondage.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/{id}", name="afficherUser")
     */
    public function userAction(Request $request, User $user)
    {
        return $this->render('default/sondage.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/addUser", name="ajouterUser")
     */
    public function addUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/editUser/{id}", name="editerUser")
     */
    public function editSondageAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $user->setUsername("Boby");

        $em->flush();

        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/deleteUser/{id}", name="supprimerUser")
     */
    public function deleteSondageAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }
}