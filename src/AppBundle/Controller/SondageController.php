<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SondageController extends Controller
{
    /**
     * @Route("/sondage/{id}/answer", name="reponse")
     */
    public function answerAction(Request $request, $id)
    {
    	if ($request->getMethod() == 'POST') {
    		dump($request->request->get('answer_id'));
    		dump($request->request->get('zone_id'));
    		return $this->render('sondage/answer-validate.html.twig', [
	        ]);
    	}
    	else {
	    	$sondage = $this->getDoctrine()->getRepository("AppBundle:Sondage")->find($id);
	    	$carte = $sondage->getCarte();
	    	$localisations = $carte->getLocalisations();
	    	$carteObj = array();
	    	foreach ($localisations as $key => $localisation) {
	    		$carteObj[$localisation->getLabel()] = $localisation->getPoints();
	    	}
	    	$imageCarte = $carte->getImage();
	        return $this->render('sondage/answer.html.twig', [
	            'image_carte' => $imageCarte,
	            'carte_zones' => $carteObj,
	            'id' => $id
	        ]);
    	}
    }
}
