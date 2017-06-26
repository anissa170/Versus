<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sondage;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        return $this->render('backend/index.html.twig');
    }

    /**
     * @Route("/sondeurs", name="sondeursBackend")
     */
    public function sondeursAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $user = $this
                ->getDoctrine()
                ->getRepository("AppBundle:User")
                ->find($request->request->get("idUser"));

            if ($this->getUser() != $user) {
                if ($request->request->get("droit") == "admin") {
                    $user->addRole("ROLE_ADMIN");
                } else {
                    $user->removeRole("ROLE_ADMIN");
                }
            }
        }

        $sondeurs = $this
            ->getDoctrine()
            ->getRepository("AppBundle:User")
            ->findAll();

        return $this->render('backend/sondeurs.html.twig', [
            'sondeurs' => $sondeurs
        ]);
    }

    /**
     * @Route("/sondages/{page}", defaults={"page": "1"}, name="sondagesBackend")
     */
    public function sondagesAction($page)
    {
        $rawSondages = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Sondage");

        $sondages = $rawSondages
            ->findBy(
                array(), /* search criteria */
                array('id' => 'DESC'), /* order */
                10, // limit
                10 * ($page - 1)
            );

        $pages = ceil(count($rawSondages->findAll()) / 10);

        return $this->render('backend/sondages.html.twig', [
            'sondages' => $sondages,
            'pages' => $pages
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