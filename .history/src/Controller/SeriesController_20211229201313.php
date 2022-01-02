<?php

namespace App\Controller;

use App\Entity\Series;
use App\Entity\Season;
use App\Form\SeriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Entity\Repository;



/**
 * @Route("/")
 */
class SeriesController extends AbstractController
{
    
    /**
     * @Route("/", name="page_accueil", methods={"GET"})
     */
    public function accueil(EntityManagerInterface $entityManager): Response
    {
        return $this->render('series/accueil.html.twig');
    }

    /**
     * @Route("/Apropos", name="page_A_propos", methods={"GET"})
     */
    public function Propos(EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('series/propos.html.twig');

    }

    /**
     * @Route("/series", name="series_poster", methods={"GET"})
     */
    public function series( EntityManagerInterface $entityManager,Request $request): Response
    {
        $params = $request->attributes->get('_route_params');
        dump($params);

        $s = $entityManager
            ->getRepository(Series::class)
            ->findAll();
        $recherche= $_GET['q'];
        
        if($recherche!=NULL) {
            $query = $entityManager->createQuery('SELECT s
            FROM App:Series s
            WHERE s.title LIKE :recherche');
            $s = $query->getResult();
        }
            
        return $this->render('series/shows.html.twig', [
            'series' => $s
        ]);
        
     }

    
     
    /**
     * @Route("/handleSearch", name="handleSearch")
     * @param Request $request
     */
    public function handleSearch(Series $series,EntityManagerInterface $entityManager )
    {
        $query = $_GET['q'];
        
        if($query!=NULL) {
            $series = $series->findArticlesByName($query,$entityManager);
        }
        return $this->render('series/shows.html.twig', [
            'series' => $series
        ]);
    }

    

    /**
     * @Route("/series/new", name="series_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $series = new Series();
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($series);
            $entityManager->flush();

            return $this->redirectToRoute('series_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('series/new.html.twig', [
            'series' => $series,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/series/{id}", name="series_show", methods={"GET"})
     */
    public function show(Series $series): Response
    {
        $em = $this -> getDoctrine()->getManager();
        $repository = $em->getRepository(Season::class);
        $season = $repository->findBy(['series'=>$series->getId()],['number'=>'ASC']);
        

        return $this->render('series/info.html.twig', [
            'series' => $series,
            'seasons' => $season,
        ]);

        $em2 = $this->getDoctrine()
        ->getRepository(Season::class)
        ->findAll();
    }

    /**
     * @Route("/series/{id}/edit", name="series_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('series_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('series/edit.html.twig', [
            'series' => $series,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/series/{id}", name="series_delete", methods={"POST"})
     */
    public function delete(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$series->getId(), $request->request->get('_token'))) {
            $entityManager->remove($series);
            $entityManager->flush();
        }

        return $this->redirectToRoute('series_index', [], Response::HTTP_SEE_OTHER);
    }

   
     /**
     * @Route("/poster/{id}", name="controleur_poster_series_show", methods={"GET"})
     */
    public function poster(Series $series): Response
    {
        return new Response(stream_get_contents($series->getPoster()), 200, array('content-type' => 'image/jpeg', ));
    }
}
