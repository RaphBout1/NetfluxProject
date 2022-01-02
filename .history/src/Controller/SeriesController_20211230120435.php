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
    public function series(): Response
    {
        $sql = 'SELECT title FROM series ORDER by TITLE ASC';

        $query = $this->pdo->query($sql);

        $series = $query->fetchAll();

        
           
        return $this->render('series/shows.html.twig', [
            'series' => $series
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

    }


   
     /**
     * @Route("/poster/{id}", name="controleur_poster_series_show", methods={"GET"})
     */
    public function poster(Series $series): Response
    {
        return new Response(stream_get_contents($series->getPoster()), 200, array('content-type' => 'image/jpeg', ));
    }
}
