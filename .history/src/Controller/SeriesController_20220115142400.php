<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\User;
use App\Entity\Series;
use App\Entity\Season;
use App\Form\SeriesType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Service\CallApiService;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use App\Entity\Rating;
use App\Form\RatingType;
use DateTime;

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
    public function series(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $repository = $entityManager->getRepository(Series::class);
        if (isset($_GET['terme'])) {
            $search = $_GET['terme'] . "%";
        } else {
            $search = '%';
        }
        $series = $paginator->paginate(
            $repository->findOneByName($search), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );


        return $this->render('series/show.html.twig', [
            'series' => $series,
        ]);
    }

    /**
     * @Route("/series/add/{imdbID}", name="serie_add", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, CallApiService $callApiService, String $imdbID): Response
    {
        dump($callApiService->getApi($imdbID));
        $response = $callApiService->getApi($imdbID);


        $serie = new Series();
       $serie->setTitle($response['Title']);
       $stringYear = $response['Year'];
       $stringYear = explode("-", $stringYear);
      var_dump((int)$stringYear[0]);

       $serie->setYearStart((int)$stringYear[0]);
       $serie->setPlot($response['Plot']);
       $serie->setImdb($response['imdbID']);
       $serie->setPoster($response['Poster']);
       $serie->setDirector($response['Director']);
       $serie->setAwards($response['Awards']);
       #$serie->addActor($response['Actors']);
       #$serie->addCountry($response['Country']);
       #$serie->addGenre($response['Genre']);
       $entityManager->persist($serie);
        $entityManager->flush();


        
        
        return $this->redirectToRoute('rating_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/series/{id}", name="series_show", methods={"GET","POST"})
     */
    public function show(Series $series,Request $request, EntityManagerInterface $entityManager): Response
    {
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);
        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setUser($this->getUser());
            $rating->setDate(new DateTime());
            $rating->setSeries($series);
            $entityManager->persist($rating);
            
            $entityManager->flush();
            return $this->redirectToRoute('rating_index', [], Response::HTTP_SEE_OTHER);
        }
        $ratings = $entityManager
            ->getRepository(Rating::class)
            ->findBy(
                ['series' => $series],
            );
        $moyenne = 0;
        $nbRating = 0;
        foreach($ratings as $rat){
            $moyenne = $moyenne + $rat->getValue();
            $nbRating ++;

        }
        $moyenne = $moyenne/$nbRating;
        $moyenne = number_format($moyenne,1);

        
        $em = $this -> getDoctrine()->getManager();
        $repository = $em->getRepository(Season::class);
        $season = $repository->findBy(['series' => $series->getId()], ['number' => 'ASC']);

        $urlYoutube = $series->getYoutubeTrailer();
        $arrayChaine = explode("/watch?v=", $urlYoutube);
        $id_video = $arrayChaine[1];

        return $this->render('series/infos.html.twig', [
            'idvideo' => $id_video,
            'series' => $series,
            'seasons' => $season,
            'rating' => $rating,
            'form' => $form->createView(),
            'ratings' => $ratings,
            'moyenne' => $moyenne,
        ]);


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

            return $this->redirectToRoute( 'rating_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('series/edit.html.twig', [
            'series' => $series,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/series/delete/{id}", name="series_delete", methods={"POST"})
     */
    public function delete(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $series->getId(), $request->request->get('_token'))) {
            $entityManager->remove($series);
            $entityManager->flush();
        }

        return $this->redirectToRoute('rating_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/poster/{id}", name="controleur_poster_series_show", methods={"GET"})
     */
    public function poster(Series $series): Response
    {
        return new Response(stream_get_contents($series->getPoster()), 200, array('content-type' => 'image/jpeg',));
    }

    /**
     * @Route("/suivre/{id}", name="suivre_serie", methods={"POST"})
     */
    public function suivre(Series $series, EntityManagerInterface $entityManager): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        if ($user->SerieSuivis($user, $series)) {
            $user->removeSeries($series);
        } else {
            $user->addSeries($series);
        }

        $entityManager->flush();
        return $this->redirectToRoute('series_show', ['id' => $series->getId()], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/suivreEp/{idEp}", name="suivre_episode", methods={"POST"})
     */
    public function suivreEp(Episode $idEp, EntityManagerInterface $entityManager): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        if ($user->EpisodeSuivis($user, $idEp)) {
            $user->removeEpisode($idEp);
        } else {
            $user->addEpisode($idEp);
        }

        $entityManager->flush();
        return $this->redirectToRoute('series_show', ['id' => $idEp->getSeason()->getSeries()->getId()], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/episode/{id}", name="episode_show", methods={"GET"})
     */
    public function episodes(Season $id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Episode::class);
        //$episodes = $repository->findBySeason($id);
        $episodes = $repository->findBy(['season' => $id->getId()], ['number' => 'ASC']);
     
        return $this->render('series/episode.html.twig', [
            'episodes' => $episodes,
            'season' => $id,
        ]);


    }
    /**
     * @Route("/calu/{id}", name="episode_show", methods={"GET"})
     */
    public function episodes(Season $id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Episode::class);
        //$episodes = $repository->findBySeason($id);
        $episodes = $repository->findBy(['season' => $id->getId()], ['number' => 'ASC']);
     
        return $this->render('series/episode.html.twig', [
            'episodes' => $episodes,
            'season' => $id,
        ]);


    }

    
}
