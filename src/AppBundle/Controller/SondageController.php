<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proposition;
use AppBundle\Entity\Sondage;
use AppBundle\Entity\Reponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

use Carbon\Carbon;

class SondageController extends Controller
{
    /**
     * @Route("/sondages", name="showSondages")
     */
    public function sondagesAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        $sondages = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Sondage")
            ->findByAuteur($this->getUser());

        return $this->render('sondage/sondage-list.html.twig', [
            'sondages' => $sondages
        ]);
    }

    /**
     * @Route("/sondage/{id}/repondre", name="answer")
     */
    public function repondreAction(Request $request, $id)
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
            return $this->redirectToRoute('showSondage', array('id' => $id));
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
     * @Route("/sondage/{id}", name="showSondage")
     */
    public function sondageAction(Sondage $sondage)
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

        return $this->render('sondage/sondage-stats.html.twig', [
            'sondage' => $sondage,
            'lines_chart' => $lineResult,
            'carte_chart' => $reponseRegion,
            'carte' => $carte,
            'localisations' => $localisations,
            'reponses' => $propositions,
            'first_localisation_name' => $sondage->getCarte()->getLocalisations()[0]->getLabel(),
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
     * @Route("/sondage/{id}/publier", defaults={"id": "0"}, name="publierSondage")
     * @Method("Post")
     */
    public function publierSondage() {

        $sondage = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Sondage')
            ->find($_POST['id']);

        if ($sondage->getAuteur() != $this->getUser()) {
            return $this->redirectToRoute("homepage");
        }

        $em = $this->getDoctrine()->getManager();

        if (!$sondage->getPublier()) {
            $sondage->setPublier(true);
        } else {
            $sondage->setPublier(false);
        }

        $em->persist($sondage);
        $em->flush();

        return new JsonResponse([true]);
    }

    /**
     * @Route("/ajouterSondage/{publier}", defaults={"publier": "false"}, name="addSondage")
     */
    public function addSondageAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        if ($request->getMethod() == 'POST') {
            $sondage = new Sondage();
            $sondage->setTitre($request->request->get('titre'));
            $sondage->setAuteur($this->getUser());
            $sondage->setCreationDate(new \DateTime());

            $sondage->setPublier(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($sondage);

            if ($_POST['proposition_1']) {
                $proposition_1 = new Proposition();
                $proposition_1->setLabel($_POST['proposition_1']);
                $proposition_1->setCouleur($_POST['proposition_1_color']);
                $proposition_1->setSondage($sondage);
                $sondage->addProposition($proposition_1);
            }

            if ($_POST['proposition_2']) {
                $proposition_2 = new Proposition();
                $proposition_2->setLabel($_POST['proposition_2']);
                $proposition_2->setCouleur($_POST['proposition_2_color']);
                $proposition_2->setSondage($sondage);
                $sondage->addProposition($proposition_2);
            }

            if ($_POST['proposition_3']) {
                $proposition_3 = new Proposition();
                $proposition_3->setLabel($_POST['proposition_3']);
                $proposition_3->setCouleur($_POST['proposition_3_color']);
                $proposition_3->setSondage($sondage);
                $sondage->addProposition($proposition_3);
            }

            if ($_POST['proposition_4']) {
                $proposition_4 = new Proposition();
                $proposition_4->setLabel($_POST['proposition_4']);
                $proposition_4->setCouleur($_POST['proposition_4_color']);
                $proposition_4->setSondage($sondage);
                $sondage->addProposition($proposition_4);
            }

            if ($_POST['proposition_5']) {
                $proposition_5 = new Proposition();
                $proposition_5->setLabel($_POST['proposition_5']);
                $proposition_5->setCouleur($_POST['proposition_5_color']);
                $proposition_5->setSondage($sondage);
                $sondage->addProposition($proposition_5);
            }

            $em->persist($sondage);
            $em->flush();

            if (!empty($_FILES['image']['name'])) {
                $dossier = 'assets/img/uploaded/';
                $fichier = basename($_FILES['image']['name']);
                $taille_maxi = 1000000;
                $taille = filesize($_FILES['image']['tmp_name']);
                $extensions = array('.png', '.gif', '.jpg', '.jpeg');
                $extension = strrchr($_FILES['image']['name'], '.');
                $nameFile = "imgSondage_" . $sondage->getId() . "_" . $fichier;
                //Début des vérifications de sécurité...
                if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                {
                    $erreur = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg, txt ou doc...';
                }
                if($taille>$taille_maxi)
                {
                    $erreur = 'Le fichier est trop gros...';
                }
                if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
                {
                    //On formate le nom du fichier ici...
                    $fichier = strtr($fichier,
                        'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
                        'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
                    $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $dossier . $nameFile)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
                    {
                        $sondage->setImage($nameFile);
                    }
                }
            }

            $carte = $this->getDoctrine()->getRepository('AppBundle:Carte')->find($_POST['carte']);
            $sondage->setCarte($carte);

            $em->persist($sondage);
            $em->flush();

            return $this->redirectToRoute('showSondages');
        }

        $cartes = $this->getDoctrine()->getRepository("AppBundle:Carte")->findAll();

        return $this->render("sondage/new-sondage.html.twig", [
            'cartes' => $cartes
        ]);
    }

    /**
     * @Route("/editerSondage/{id}", name="editSondage")
     */
    public function editSondageAction(Sondage $sondage)
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
     * @Route("/supprimerSondage/{id}", name="deleteSondage")
     */
    public function deleteSondageAction(Sondage $sondage)
    {
        if ($sondage->getAuteur() != $this->getUser() || !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("homepage");
        }

        if (file_exists("assets/img/uploaded/".$sondage->getImage())) {
            unlink("assets/img/uploaded/".$sondage->getImage());
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($sondage);
        $em->flush();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("sondagesBackend");
        }

        return $this->redirectToRoute("showSondages");
    }

    /**
     * @Route("/supprimerProposition/{id}", name="deleteProposition")
     */
    public function deletePropositionAction(Proposition $proposition)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($proposition);
        $em->flush();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("sondagesBackend");
        }

        return $this->redirectToRoute("showSondages");
    }
}
