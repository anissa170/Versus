<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proposition;
use AppBundle\Entity\Sondage;
use AppBundle\Entity\Reponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SondageController extends Controller
{
    /**
     * @Route("/sondages", name="afficherSondages")
     */
    public function sondagesAction(Request $request)
    {
        $sondages = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Sondage")
            ->findAll();

        return $this->render('default/sondage.html.twig', [
            'sondages' => $sondages
        ]);
    }

    /**
     * @Route("/sondage/{id}/answer", name="reponse")
     */
    public function answerAction(Request $request, $id)
    {	
    	$session = $request->getSession();

    	if ($request->getMethod() == 'POST') {
    		if (empty($request->request->get('answer_id'))) {
    			$session->getFlashBag()->add(
				    'error',
				    "Un problème est survenue lors de l'envoi de votre réponse."
				);
    		}
    		if (empty($request->request->get('zone_id'))) {
    			$session->getFlashBag()->add(
				    'error',
				    "Un problème est survenue lors de l'envoi de votre zone."
				);
    		}
    	}

    	if ($request->getMethod() == 'POST' && !$session->getFlashBag()->has('error')) {
    		$em = $this->getDoctrine()->getManager();
    		$reponse = new Reponse();
    		$reponse->setDatetime(new \DateTime());
    		$reponse->setProposition($this->getDoctrine()->getRepository("AppBundle:Proposition")->find($request->request->get('answer_id')));
    		$reponse->setLocalisation($this->getDoctrine()->getRepository("AppBundle:Localisation")->find($request->request->get('zone_id')));
    		$em->persist($reponse);
    		$em->flush();
    		return $this->render('sondage/answer-validate.html.twig', [
	        ]);
    	}
    	else {
	    	$sondage = $this->getDoctrine()->getRepository("AppBundle:Sondage")->find($id);
	    	$carte = $sondage->getCarte();
	    	$localisations = $carte->getLocalisations();
	    	$reponses = $sondage->getPropositions();
	        return $this->render('sondage/answer.html.twig', [
                'sondage' => $sondage,
	            'carte' => $carte,
	            'localisations' => $localisations,
	            'id' => $id,
	            'reponses' => $reponses,
	        ]);
    	}
    }

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
        if ($sondage->getAuteur() != $this->getUser() || !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("homepage");
        }
        $em = $this->getDoctrine()->getManager();

        $em->remove($sondage);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }
}
