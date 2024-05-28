<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Entry;
use App\Entity\SchoolClass;
use App\Entity\Student;
use App\Entity\Test;
use App\Form\EntryType;
use App\Repository\EntryRepository;

#[Route('/inscriptions')]
#[IsGranted('ROLE_USER')]
class EntryController extends AbstractController
{
    #[Route('/', name: 'entry.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $entries = $em->getRepository(Entry::class)->findAll();
        if (count($entries) > 0) {
            return $this->render('entry/index.html.twig', [
                'entries' => $entries,
            ]);
        } else {
            $classes = $em->getRepository(SchoolClass::class)->findAll();
            if ($classes == null)
            {
                return $this->render('shared/empty.html.twig', [
                    "message" => "Il n'y a pas encore de classe enregistrée, veuillez en créer une pour pouvoir enregistrer vos élèves !",
                    'redirectLabel' => 'Créer une classe',
                    'redirectLink' => 'class.create'
                ]);
            }

            $students = $em->getRepository(Student::class)->findAll();
            if ($students == null)
            {
                return $this->render('shared/empty.html.twig', [
                    "message" => "Il n'y a pas encore d'étudiant enregistré, veuillez en créer un pour pouvoir l'inscrire aux épreuves !",
                    'redirectLabel' => 'Créer un étudiant',
                    'redirectLink' => 'student.create'
                ]);
            }

            $tests = $em->getRepository(Test::class)->findAll();
            if ($tests == null)
            {
                return $this->render('shared/empty.html.twig', [
                    "message" => "Il n'y a pas encore d'épreuve enregistrée, veuillez en créer une pour pouvoir inscrire vos élèves !",
                    'redirectLabel' => 'Créer une épreuve',
                    'redirectLink' => 'test.create'
                ]);
            }
            return $this->render('shared/empty.html.twig', [
                "message" => "Il n'y a pas encore d'inscription enregistrée, créez en pour que vos élèves participe aux épreuves !",
                'redirectLabel' => 'Créer une inscription',
                'redirectLink' => 'entry.create'
            ]);
        }
    }

    #[Route('/new', name:'entry.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entry->setNoDos($em->getRepository(Entry::class)->getNextNoDos());
            $em->persist($entry);
            $em->flush();
            $this->addFlash('success', "L'inscription a bien été créée, N° Dossard : " . $entry->getNoDos());
            return $this->redirectToRoute('entry.index');
        }
        return $this->render('entry/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name:'entry.detail')]
    public function getDetail(int $id, EntryRepository $repository): Response
    {
        $entry = $repository->findOneBy(['id' => $id]);
        if ($entry != null) {
            if ($entry->getTemps() != null) 
            {
                $hours = $entry->getTemps()->h + ($entry->getTemps()->d * 24);
                $minutes = $entry->getTemps()->i;

                $entry->setFormattedTemps(sprintf('%02d Heures et %02d minutes', $hours, $minutes));
            }
            return $this->render('entry/detail.html.twig', [
                'entry' => $entry,
            ]);
        }

        return $this->notFound();
    }

    #[Route('/{id}/edit', name:'entry.edit')]
    public function update(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $entry = $em->getRepository(Entry::class)->findOneBy(['id' => $id]);
        if ($entry == null) {
            return $this->notFound();
        }

        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();
            $this->addFlash('success', "L'inscription a bien été modifiée");
            return $this->redirectToRoute('entry.index');
        }
        return $this->render('entry/edit.html.twig', [
            'form' => $form,
            'entry' => $entry
        ]);


    }

    #[Route('/{id}/delete', name:'entry.delete')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $entry = $em->getRepository(entry::class)->findOneBy(['id' => $id]);
        if ($entry == null) {
            return $this->notFound();
        }

        if ($class->getTend() != null) {
            $this->addFlash('danger', "Impossible de supprimer l'inscription qui possède une heure d'arrivée !");
            return $this->redirectToRoute('entry.index');
        }

        $em->remove($entry);
        $em->flush();
        $this->addFlash('success', "L'inscription a bien été supprimée");
        return $this->redirectToRoute('entry.index');
    }

    private function notFound(): Response
    {
        return $this->render('shared/notfound.html.twig', [
            'message' => "Cette inscription n'existe pas !",
            'redirectLabel' => 'Accèder à la liste des inscriptions',
            'redirectLink' => 'entry.index'
        ]);
    }
}