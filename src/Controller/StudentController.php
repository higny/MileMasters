<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/étudiants')]
#[IsGranted('ROLE_USER')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'student.index')]
    public function index(StudentRepository $repository): Response
    {
        $students = $repository->findAll();
        if (count($students) > 0) {
            return $this->render('student/index.html.twig', [
                'students' => $students,
            ]);
        } else {
            return $this->render('shared/empty.html.twig', [
                "message" => "Il n'y a pas encore d'étudiant enregistré, veuillez en créer un pour pouvoir l'inscrire aux épreuves !",
                'redirectLabel' => 'Créer un étudiant',
                'redirectLink' => 'student.create'
            ]);
        }
    }

    #[Route('/new', name:'student.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($student);
            $em->flush();
            $this->addFlash('success', "L'étudiant a bien été créé");
            return $this->redirectToRoute('student.index');
        }
        return $this->render('student/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name:'student.detail')]
    public function getDetail(int $id, StudentRepository $repository): Response
    {
        $student = $repository->findOneBy(['id' => $id]);
        if ($student != null) {
            return $this->render('student/detail.html.twig', [
                'student' => $student,
            ]);
        }

        return $this->notFound();
    }

    #[Route('/{id}/edit', name:'student.edit')]
    public function update(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $student = $em->getRepository(Student::class)->findOneBy(['id' => $id]);
        if ($student == null) {
            return $this->notFound();
        }

        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($student);
            $em->flush();
            $this->addFlash('success', "L'étudiant a bien été modifié");
            return $this->redirectToRoute('student.index');
        }
        return $this->render('student/edit.html.twig', [
            'form' => $form,
            'student' => $student
        ]);


    }

    #[Route('/{id}/delete', name:'student.delete')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $student = $em->getRepository(Student::class)->findOneBy(['id' => $id]);
        if ($student == null) {
            return $this->notFound();
        }

        if (count($class->getEntries()) > 0) {
            $this->addFlash('danger', "Impossible de supprimer l'étudiant, il y a encore des inscriptions associées !");
            return $this->redirectToRoute('student.index');
        }

        $em->remove($student);
        $em->flush();
        $this->addFlash('success', "L'étudiant a bien été supprimé");
        return $this->redirectToRoute('student.index');
    }

    private function notFound(): Response
    {
        return $this->render('shared/notfound.html.twig', [
            'message' => "Cet étudiant n'existe pas !",
            'redirectLabel' => 'Accèder à la liste des étudiants',
            'redirectLink' => 'student.index'
        ]);
    }
}