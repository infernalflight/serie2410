<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SerieController extends AbstractController
{

    #[Route('/serie/test', name:'serie_test')]
    public function test(EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $serie->setName("Walking Dead")
            ->setOverview("Les zombies ont dominé la terre")
            ->setStatus("Ended")
            ->setGenres("Horror, Thriller, Gore")
            ->setFirstAirDate(new \DateTime("2010-03-25"))
            ->setLastAirDate(new \DateTime("2022-10-03"))
            ->setDateCreated(new \DateTime());

        $em->persist($serie);
        $em->flush();

        return new Response("Objet crée avec succès");
    }

    #[Route('/serie/list', name:'serie_list')]
    public function list(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findAll();


        // Exemple de Requête à partir de méthode héritée du Repo
        /**
        $series = $serieRepository->findBy(
            ['status' => "returning", 'genres' => "Comedy / Drama"],
            ['name' => 'DESC'],
        );
        **/

        // Exemple de Requête à partir d'une méthode custom
  //      $series = $serieRepository->getSeriesByPopularity(50);

        // Exemple de Requête à partir de méthode custom du Repo en DQL
//        $series = $serieRepository->getSeriesWithDql(75);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
        ]);
    }
}
