<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proposition;
use AppBundle\Entity\Sondage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SondageController extends Controller
{
    /**
     * @Route("/sondage/{id}", name="afficherSondage")
     */
    public function sondageAction(Request $request, Sondage $sondage)
    {
        return $this->render('default/sondage.html.twig', [
            'sondage' => $sondage
        ]);
    }

    /**
     * @Route("/addSondage", name="ajouterSondage")
     */
    public function addSondageAction(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute("homepage");
        }
        $em = $this->getDoctrine()->getManager();

        $sondage = new Sondage();
        $sondage->setTitre("Testyui");
        $sondage->setAuteur($this->getUser());
        $sondage->setImage("nope");
//        $sondage->setCarte(1);
        $sondage->setCreationDate(new \DateTime());
        $sondage->setPublier(true);

        $proposition = new Proposition();
        $proposition->setLabel("Test 1");
        $proposition->setCouleur("red");
        $proposition->setSondage($sondage);

        $sondage->addProposition($proposition);

        $em->persist($sondage);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/editSondage/{id}", name="editerSondage")
     */
    public function editSondageAction(Request $request, Sondage $sondage)
    {
        if ($sondage->getAuteur() != $this->getUser()) {
            return $this->redirectToRoute("homepage");
        }
        $em = $this->getDoctrine()->getManager();

        $sondage->setTitre("Wolololo");

        $em->flush();

        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/deleteSondage/{id}", name="supprimerSondage")
     */
    public function deleteSondageAction(Request $request, Sondage $sondage)
    {
        if ($sondage->getAuteur() != $this->getUser()) {
            return $this->redirectToRoute("homepage");
        }
        $em = $this->getDoctrine()->getManager();

        $em->remove($sondage);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }
}