<h1>Projet de programmation Web</h1>
<h2>    Netflux<h2>

Fonctionnalités :


>Une page d'accueil présentant le site
  
  https://user-images.githubusercontent.com/91502674/159134589-a27dc6e7-ae04-4c11-8fa5-b7b6c193708f.mp4

>Un menu facilitant la navigation dans le site

>Une page "à propos", décrivant le travail réalisé et précisant la liste des auteurs du site (le binôme), et éventuellement les difficultés rencontrées (ainsi que ce tableau)

>Une page présentant l'ensemble des séries de la base de données, l'affichage doit être paginé et il doit être possible de rechercher des séries
  
  https://user-images.githubusercontent.com/91502674/159134620-25dfc9ff-0443-4806-81e6-6fb04ed3b2c1.mp4

>La possibilité de naviguer dans les saisons d'une série, puis dans les épisodes

>Chaque fois que c'est pertinent on affichera le poster de la série

>Chaque fois que c'est pertinent on affichera le trailer <em>YouTube</em>

>Chaque fois que c'est pertinent on affichera un lien vers la fiche <em>IMDB</em> de la série ou de l'épisode

>La possibilité pour un utilisateur de s'inscrire et de se connecter avec un suivi de session
  
  
![Symfony Local Web Server_ Page not found - Google Chrome 25_01_2022 12_51_54](https://user-images.githubusercontent.com/91502674/159134688-e13e0676-baa2-4db0-82a7-d6fd2f221010.png)
  
  ![Symfony Local Web Server_ Page not found - Google Chrome 25_01_2022 12_52_24](https://user-images.githubusercontent.com/91502674/159134698-10d23627-5d32-4d43-99a0-2e2da8340a22.png)


>Respect des mesures classiques de sécurité (mot de passe non stocké en clair, CAPTCHA à l'inscription...)

>En étant inscrit et connecté seulement, possibilité de noter une série, en ajoutant éventuellement un commentaire optionnel (table <kbd>rating</kbd>)

>En étant inscrit et connecté seulement, possibilité de "suivre" des séries (à l'aide de la table <kbd>user_series</kbd>) et ces séries sont accessibles dans un menu "mes séries"

>En étant inscrit et connecté seulement, possibilité de marquer un épisode comme vu ou non vu. De cette manière, l'utilisateur doit pouvoir visualiser où il en est dans le visionnage d'une série. Il est pertinent de remonter l'information dans la page "saisons"

>Un utilisateur administrateur doit pouvoir modérer les commentaires en les supprimant

>Un administrateur devrait pouvoir incorporer des nouvelles séries dans la base de données à l'aide de l'API <em>OMDB</em>



