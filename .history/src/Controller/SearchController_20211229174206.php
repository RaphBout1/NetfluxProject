<?php

namespace App\Controller;

use App\Entity\Series;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ArticleRepository;

use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    
 
    
 
/**
     * @Route ("/Search", name="Search", methods={"GET","POST"})
     */
       
    public function search(Request $request)
    {
        $form = $this->createFormBuilder()->add('recherche', SearchType::class)->getForm();
              
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->container->get('doctrine')->getManager();
            $series = $em->getRepository('App\Entity\Series')->search($data['recherche']);
            return $this->render('series/Searchresult.html.twig', [
                'series' => $series
            ]);
        }
           
        return $this->render('series/Search.html.twig', [
            'form' => $form->createView()
        ]);
   
          
    }
    

   
}
