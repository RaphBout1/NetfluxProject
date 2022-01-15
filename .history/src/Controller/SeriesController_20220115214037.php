<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\User;
use App\Entity\Series;
use App\Entity\Season;
use App\Form\SeriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\CallApiService;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/user", name="userInterface", methods={"GET"})
     */
    public function user(EntityManagerInterface $entityManager,PaginatorInterface $paginator,Request $request): Response
    {
         /** @var User $user */
        $user = $this->getUser();
        $series = $user->getSeries();
        $repository = $entityManager->getRepository(Series::class);


        // Pagination
        $series = $paginator->paginate(
            $series, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            12// Nombre de résultats par page
        );


        return $this->render('series/userInterface.html.twig',[
            'series' => $series,
        ]);
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

        $serie->setYearStart((int)$stringYear[0]);
        $serie->setPlot($response['Plot']);
        $serie->setImdb($response['imdbID']);
        $serie->setPoster($response['Poster']);
        $serie->setDirector($response['Director']);
        $serie->setAwards($response['Awards']);
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
        }
        $ratings = $entityManager
            ->getRepository(Rating::class)
            ->findBy(
                ['series' => $series],
            );

        
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
        ]);


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

    
}
