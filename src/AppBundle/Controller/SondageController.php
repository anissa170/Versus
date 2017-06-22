<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proposition;
use AppBundle\Entity\Sondage;
use AppBundle\Entity\Reponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

use Carbon\Carbon;

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
        $propositions = $sondage->getPropositions();
        $lineResult = array();
        $reponseRegion = array();

        $em = $this->getDoctrine()->getManager();
        foreach ($propositions as $proposition) {

            //line graf
            $queryLine = $em->createQuery(
                'SELECT r
                FROM AppBundle:Reponse r
                WHERE r.proposition = :id
                AND r.datetime >= :dateBegin
                AND r.datetime <= :dateEnd'
            );
            $beginDate = Carbon::instance($sondage->getCreationDate())->setTime(0,0,0);
            $idPorposition = $proposition->getId();

            $propositionCountPerDay = array();
            $tempReponseRegion = array();
            $propositionCountPerDay['proposition'] = $proposition;
            $tempReponseRegion['proposition'] = $proposition;

            $dates = array();

            for ($i=0; $i < 10; $i++) {
                $beginDateString = $beginDate->toDateTimeString();
                $dates[$beginDateString] =  array();

                $endDateString = Carbon::instance($beginDate)->setTime(23,59,59)->toDateTimeString();
                $beginDate->addDay(1);
                
                $queryLine->setParameter('id', $idPorposition);
                $queryLine->setParameter('dateBegin', $beginDateString);
                $queryLine->setParameter('dateEnd', $endDateString);
                $dates[$beginDateString] = $queryLine->getResult();
            }

            $propositionCountPerDay['dates'] = $dates;

            //1st region selected
            $queryLocation = $em->createQuery(
                'SELECT r
                FROM AppBundle:Reponse r
                WHERE r.proposition = :id_propo
                AND r.localisation = :id_loca'
            );

            $queryLocation->setParameter('id_propo', $proposition->getId());
            $queryLocation->setParameter('id_loca', $sondage->getCarte()->getLocalisations()[0]->getId());

            $tempReponseRegion['reponses'] = $queryLocation->getResult();

            array_push($reponseRegion, $tempReponseRegion);

            array_push($lineResult, $propositionCountPerDay);

        }

        $carte = $sondage->getCarte();
        $localisations = $carte->getLocalisations();

        return $this->render('default/sondage.html.twig', [
            'sondage' => $sondage,
            'lines_chart' => $lineResult,
            'carte_chart' => $reponseRegion,
            'carte' => $carte,
            'localisations' => $localisations,
            'reponses' => $propositions,
        ]);
    }

    /**
     * @Route("/sondage/{id}/ajax", name="ajaxSondage")
     */
    public function ajaxAction(Request $request, Sondage $sondage)
    {
        $id = $request->request->get('zone_id');
        $propositions = $sondage->getPropositions();
        $em = $this->getDoctrine()->getManager();
        $arrayReturn = array();
        foreach ($propositions as $proposition) {

            $array = array();
            $queryLocation = $em->createQuery(
                'SELECT r
                FROM AppBundle:Reponse r
                WHERE r.proposition = :id_propo
                AND r.localisation = :id_loca'
            );

            $queryLocation->setParameter('id_propo', $proposition->getId());

            $queryLocation->setParameter('id_loca', $id);

            $array['proposition'] = array('label' => $proposition->getLabel(), 'couleur' => $proposition->getCouleur());
            $array['reponses'] = count($queryLocation->getResult());
            array_push($arrayReturn, $array);
        }

        return new JsonResponse($arrayReturn);
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
