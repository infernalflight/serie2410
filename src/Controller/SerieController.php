<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    #[Route('/serie/list/{status}/{page}', name:'serie_list', requirements: ['page' => '\d+'], defaults: ['status' => 'all', 'page' => 1])]
    public function list(SerieRepository $serieRepository, ParameterBagInterface $parameters, string $status, int $page): Response
    {
        //$series = $serieRepository->findAll();

        $nbByPage = $parameters->get('max_by_page');
        $offset = ($page - 1) * $nbByPage;

        $criterias = $status !== 'all' ? ['status' => $status] : [];
        // Exemple de Requête à partir de méthode héritée du Repo

        $series = $serieRepository->findBy(
            $criterias,
            ['popularity' => 'DESC'],
            $nbByPage,
            $offset
        );

        $nbTotalPages = ceil($serieRepository->count($criterias) / $nbByPage);

        // Exemple de Requête à partir d'une méthode custom
  //      $series = $serieRepository->getSeriesByPopularity(50);

        // Exemple de Requête à partir de méthode custom du Repo en DQL
//        $series = $serieRepository->getSeriesWithDql(75);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => $page,
            'nb_total_pages' => $nbTotalPages,
        ]);
    }
}
