<?php

namespace App\Controller;

use App\Entity\SchoolClass;
use App\Form\SchoolClassType;
use App\Repository\SchoolClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/classes')]
#[IsGranted('ROLE_USER')]
class ClassController extends AbstractController
{
    #[Route('/', name: 'class.index')]
    public function index(SchoolClassRepository $repository): Response
    {
        $classes = $repository->findAll();
        if (count($classes) > 0) {
            return $this->render('class/index.html.twig', [
                'classes' => $classes,
            ]);
        } else {
            return $this->render('shared/empty.html.twig', [
                "message" => "Il n'y a pas encore de classe enregistrée, veuillez en créer une pour pouvoir enregistrer vos élèves !",
                'redirectLabel' => 'Créer une classe',
                'redirectLink' => 'class.create'
            ]);
        }
    }

    #[Route('/new', name:'class.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if ($em->getRepository(SchoolClass::class)->count() == 1)
        {
            return $this->redirectToRoute('class.index');
        }

        $class = new SchoolClass();
        $form = $this->createForm(SchoolClassType::class, $class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($class);
            $em->flush();
            $this->addFlash('success', 'La classe a bien été créée');
            return $this->redirectToRoute('class.index');
        }
        return $this->render('class/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name:'class.detail')]
    public function getDetail(int $id, SchoolClassRepository $repository): Response
    {
        $class = $repository->findOneBy(['id' => $id]);
        if ($class != null) {
            return $this->render('class/detail.html.twig', [
                'class' => $class,
            ]);
        }

        return $this->notFound();
    }

    #[Route('/{id}/edit', name:'class.edit')]
    public function update(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $class = $em->getRepository(SchoolClass::class)->findOneBy(['id' => $id]);
        if ($class == null) {
            return $this->notFound();
        }

        $form = $this->createForm(SchoolClassType::class, $class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($class);
            $em->flush();
            $this->addFlash('success', 'La classe a bien été modifiée');
            return $this->redirectToRoute('class.index');
        }
        return $this->render('class/edit.html.twig', [
            'form' => $form,
            'class' => $class
        ]);


    }

    #[Route('/{id}/delete', name:'class.delete')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $class = $em->getRepository(SchoolClass::class)->findOneBy(['id' => $id]);
        if ($class == null) {
            return $this->notFound();
        }

        if (count($class->getStudents()) > 0) {
            $this->addFlash('danger', 'Impossible de supprimer la classe, il y a encore des étudiants associés !');
            return $this->redirectToRoute('class.index');
        }

        $em->remove($class);
        $em->flush();
        $this->addFlash('success', 'La classe a bien été supprimée');
        return $this->redirectToRoute('class.index');
    }

    private function notFound(): Response
    {
        return $this->render('shared/notfound.html.twig', [
            'message' => "Cette classe n'existe pas !",
            'redirectLabel' => 'Accèder à la liste des classes',
            'redirectLink' => 'class.index'
        ]);
    }
}
