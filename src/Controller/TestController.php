<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Test;
use App\Entity\Entry;
use App\Form\TestType;
use App\Repository\TestRepository;

#[Route('/épreuves')]
#[IsGranted('ROLE_USER')]
class TestController extends AbstractController
{
    #[Route('/', name: 'test.index')]
    public function index(TestRepository $repository): Response
    {
        $tests = $repository->findAll();
        if (count($tests) > 0) {
            return $this->render('test/index.html.twig', [
                'tests' => $tests,
            ]);
        } else {
            return $this->render('shared/empty.html.twig', [
                "message" => "Il n'y a pas encore d'épreuve enregistrée, veuillez en créer une pour pouvoir inscrire vos élèves !",
                'redirectLabel' => 'Créer une épreuve',
                'redirectLink' => 'test.create'
            ]);
        }
    }

    #[Route('/new', name:'test.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($test);
            $em->flush();
            $this->addFlash('success', "L'épreuve a bien été créée");
            return $this->redirectToRoute('test.index');
        }
        return $this->render('test/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name:'test.detail')]
    public function getDetail(int $id, TestRepository $repository, EntityManagerInterface $em): Response
    {
        $test = $repository->findOneBy(['id' => $id]);
        if ($test != null) {
            $runners = $em->getRepository(Entry::class)->findEntriesWithTendAndOrderedByTemps($id, true);
            $walkers = $em->getRepository(Entry::class)->findEntriesWithTendAndOrderedByTemps($id, false);

            return $this->render('test/detail.html.twig', [
                'test' => $test,
                'runners' => $runners,
                'walkers' => $walkers
            ]);
        }

        return $this->notFound();
    }

    #[Route('/{id}/json', name:'test.detail.json')]
    public function getDetailJson(int $id, TestRepository $repository): Response
    {
        $test = $repository->findOneBy(['id' => $id]);
        if ($test != null) {
            return new JsonResponse(['Tstart' => $test->getTstart()]);
        }

        return new JsonResponse(['error' => 'Entity not found !'], 404);
    }

    #[Route('/{id}/edit', name:'test.edit')]
    public function update(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $test = $em->getRepository(Test::class)->findOneBy(['id' => $id]);
        if ($test == null) {
            return $this->notFound();
        }

        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($test);
            $em->flush();
            $this->addFlash('success', "L'épreuve a bien été modifiée");
            return $this->redirectToRoute('test.index');
        }
        return $this->render('test/edit.html.twig', [
            'form' => $form,
            'test' => $test
        ]);


    }

    #[Route('/{id}/delete', name:'test.delete')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $test = $em->getRepository(Test::class)->findOneBy(['id' => $id]);
        if ($test == null) {
            return $this->notFound();
        }

        if (count($class->getEntries()) > 0) {
            $this->addFlash('danger', "Impossible de supprimer l'épreuve, il y a encore des inscriptions associées !");
            return $this->redirectToRoute('test.index');
        }

        $em->remove($test);
        $em->flush();
        $this->addFlash('success', "L'épreuve a bien été supprimée");
        return $this->redirectToRoute('test.index');
    }

    private function notFound(): Response
    {
        return $this->render('shared/notfound.html.twig', [
            'message' => "Cette épreuve n'existe pas !",
            'redirectLabel' => 'Accèder à la liste des épreuves',
            'redirectLink' => 'test.index'
        ]);
    }
}