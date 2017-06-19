<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sondage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/backend")
 * @Security("has_role('ROLE_ADMIN')")
 */
class BackendController extends Controller
{
    /**
     * @Route("/", name="homeBackend")
     */
    public function indexAction()
    {
        return $this->render('backend/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR
        ]);
    }

    /**
     * @Route("/sondeurs", name="sondeursBackend")
     */
    public function sondeursAction()
    {
        $sondeurs = $this->getDoctrine()->getRepository("AppBundle:User")->findAll();

        return $this->render('backend/sondeurs.html.twig', [
            'sondeurs' => $sondeurs
        ]);
    }

    /**
     * @Route("/sondages", name="sondagesBackend")
     */
    public function sondagesAction()
    {
        $sondages = $this->getDoctrine()->getRepository("AppBundle:Sondage")->findAll();

        return $this->render('backend/sondages.html.twig', [
            'sondages' => $sondages
        ]);
    }

    /**
     * @Route("/sondage/{id}", name="propositionsFromSondageBackend")
     */
    public function propositionsFromSondageAction(Sondage $sondage)
    {
        return $this->render('backend/propositions.html.twig', [
            'propositions' => $sondage->getPropositions()
        ]);
    }
}