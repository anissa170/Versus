<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proposition;
use AppBundle\Entity\Sondage;
use AppBundle\Entity\Reponse;
use AppBundle\Form\SondageType;
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
				    "Un probleme est survenue lors de l'envoie de votre reponse."
				);
    		}
    		if (empty($request->request->get('zone_id'))) {
    			$session->getFlashBag()->add(
				    'error',
				    "Un probleme est survenue lors de l'envoie de votre zone."
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
	    	$imageCarte = $carte->getImage();
	        return $this->render('sondage/answer.html.twig', [
	            'image_carte' => $imageCarte,
	            'localisations' => $localisations,
	            'id' => $id,
	            'reponses' => $reponses
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
//        if (!$this->getUser()) {
//            return $this->redirectToRoute("homepage");
//        }

//        if ($request->getMethod() == 'POST') {
//
//            $em = $this->getDoctrine()->getManager();
//
//            $sondage = new Sondage();
//            $sondage->setAuteur($this->getUser());
//            $sondage->setCreationDate(new \DateTime());
//            $sondage->setPublier(false);
//
//            //Traitement image
//            dump($_FILES);
//            $sondage->setImage("nope");
//
//            //Traitement carte
////        $sondage->setCarte(1);
//
//            $proposition = new Proposition();
//            $proposition->setLabel("Test 1");
//            $proposition->setCouleur("red");
//            $proposition->setSondage($sondage);
//
//            $sondage->addProposition($proposition);
//
//            $em->persist($sondage);
//            $em->flush();
//
//            return $this->redirectToRoute("homepage");
//        }

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $sondage = new Sondage();
        $sondage->setAuteur($this->getUser());
        $sondage->setCreationDate(new \DateTime());
        $sondage->setPublier(false);

        $form = $this->get('form.factory')->create(SondageType::class, $sondage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
        }

        return $this->render("sondage/new-sondage.html.twig", [
            'form' => $form->createView()
        ]);
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
