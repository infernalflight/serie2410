<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieForm;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name:'serie')]
final class SerieController extends AbstractController
{

    #[Route('/test', name:'_test')]
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

    #[Route('/list/{status}/{page}', name:'_list', requirements: ['page' => '\d+'], defaults: ['status' => 'all', 'page' => 1])]
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

    #[Route('/detail/{serie}', name:'_detail', requirements: ['serie' => '\d+'])]
    public function detail(Serie $serie = null): Response
    {
        if (!$serie){
            return $this->redirectToRoute('serie_list');
        }

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }

    #[Route('/create', name:'_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $serieForm = $this->createForm(SerieForm::class, $serie);
        $serieForm->handleRequest($request);
        if ($serieForm->isSubmitted() && $serieForm->isValid()) {
            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Une nouvelle série a été enregistrée.');
            return $this->redirectToRoute('serie_detail', ['serie' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $serieForm,
        ]);
    }

    #[Route('/update/{serie}', name:'_update', requirements: ['serie' => '\d+'])]
    public function update(Serie $serie, Request $request, EntityManagerInterface $em): Response
    {
        $serieForm = $this->createForm(SerieForm::class, $serie);
        $serieForm->handleRequest($request);
        if ($serieForm->isSubmitted() && $serieForm->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Une nouvelle série a été mise à jour.');
            return $this->redirectToRoute('serie_detail', ['serie' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $serieForm,
        ]);
    }

}
