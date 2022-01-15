


### permet d'initialiser les moyennes
        $series = $entityManager
            ->getRepository(Series::class)
            ->findAll();
        

        foreach($series as $serie){
            $ratings = $entityManager
            ->getRepository(Rating::class)
            ->findBy(
                ['series' => $serie],
            );
            $moyenne = 0;
            $nbRating = 0;
            foreach($ratings as $rat){
                 $moyenne = $moyenne + $rat->getValue();
                 $nbRating ++;

            }
            if($nbRating!=0){
                $moyenne = $moyenne/$nbRating;
                $moyenne = number_format($moyenne,1);
                $serie->setnoteUser($moyenne);
            }
            

        }   