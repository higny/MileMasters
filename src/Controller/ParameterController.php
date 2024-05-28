<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Entry;
use App\Entity\Test;
use App\Entity\Student;
use App\Entity\SchoolClass;
use App\Entity\User;

#[IsGranted('ROLE_USER')]
class ParameterController extends AbstractController
{
    #[Route('/paramètres', name: 'parameter.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $entries = $em->getRepository(Entry::class)->findAll();
        $allowFillData = count($entries) > 10 ? false : true;

        return $this->render('parameter/index.html.twig', [
            'allowFillData' => $allowFillData,
        ]);
    }

    #[Route('/paramètres/reset', name: 'parameter.reset')]
    public function reset(EntityManagerInterface $em): Response
    {
        $entries = $em->getRepository(Entry::class)->findAll();
        foreach($entries as $entry)
        {
            $em->remove($entry);
            $em->flush();
        }

        $tests = $em->getRepository(Test::class)->findAll();
        foreach($tests as $test)
        {
            $em->remove($test);
            $em->flush();
        }

        $students = $em->getRepository(Student::class)->findAll();
        foreach($students as $student)
        {
            $em->remove($student);
            $em->flush();
        }

        $classes = $em->getRepository(SchoolClass::class)->findAll();
        foreach($classes as $classe)
        {
            $em->remove($classe);
            $em->flush();
        }

        $this->addFlash('success', "Les données ont bien été réinitialisée !");
        return $this->redirectToRoute('parameter.index');
    }

    #[Route('/paramètres/fill', name: 'parameter.fill')]
    public function fill(EntityManagerInterface $em): Response
    {
        // Classes
        $classes = $em->getRepository(SchoolClass::class)->findAll();
        if ($classes == null) {
            $class = new SchoolClass();
            $class->setNiv(2)->setIdent('2 ème Bach info');
            $em->persist($class);
            $em->flush();
        }
        $class = $em->getRepository(SchoolClass::class)->findAll()[0];

        // Etudiants
        $studentsToCreate = [
            [ 'nom' => 'Braibant', 'prenom' => 'Nicolas', 'sexe' => true ],
            [ 'nom' => 'Dardenne', 'prenom' => 'Romain', 'sexe' => true ],
            [ 'nom' => 'De Marco', 'prenom' => 'Jonas', 'sexe' => true ],
            [ 'nom' => 'Decker', 'prenom' => 'Guillaume', 'sexe' => true ],
            [ 'nom' => 'Guillaume', 'prenom' => 'Jim', 'sexe' => true ],
            [ 'nom' => 'Hardenne', 'prenom' => 'Guillaume', 'sexe' => true ],
            [ 'nom' => 'Henry', 'prenom' => 'Cyrille', 'sexe' => true ],
            [ 'nom' => 'Higny', 'prenom' => 'Julien', 'sexe' => true ],
            [ 'nom' => 'Kallai', 'prenom' => 'Darius', 'sexe' => true ],
            [ 'nom' => 'Martins Embalo', 'prenom' => 'Umaro', 'sexe' => true ],
            [ 'nom' => 'Neerinck', 'prenom' => 'Maxime', 'sexe' => true ],
            [ 'nom' => 'Pereira Costa', 'prenom' => 'Lara', 'sexe' => false ],
            [ 'nom' => 'Van Rompu', 'prenom' => 'Christopher', 'sexe' => true ]
        ];

        foreach ($studentsToCreate as $student)
        {
            $s = new Student();
            $s->setNom($student['nom'])->setPrenom($student['prenom'])->setSexe($student['sexe'])->setClass($class);
            $em->persist($s);
            $em->flush();
        }

        $students = $em->getRepository(Student::class)->findAll();

        // Epreuves
        $testsToCreate = [ 'Marathon de Bruxelles', "L'arlonaise", "Semi de Visé", "Marathon de Luxembourg", 'La calestienne'];
        foreach($testsToCreate as $test)
        {
            $now = new \DateTime();
            $t = new Test();
            $t->setName($test)->setAnSco(2024)->setDist(mt_rand(20,42))->setDate($now->modify('-' . mt_rand(1,3) . ' day'));

            $hour = mt_rand(1,20);
            if($hour % 2 === 0)
            {
                $t->setTstart(new \DateTime($now->format('Y-m-d') . ' ' . sprintf('%02d', $hour) . ':00'));
            }

            $em->persist($t);
            $em->flush();
        }

        $tests = $em->getRepository(Test::class)->findAll();

        // Inscriptions
        foreach($students as $s)
        {
            foreach($tests as $t)
            {
                if (mt_rand(1,10) % 2 === 0)
                {
                    $tStart = $t->getTstart();
                    if ($tStart == null)
                    {
                        $now = new \DateTime();
                        $hour = mt_rand(1,20);
                        $tStart = new \DateTime($now->format('Y-m-d') . ' ' . sprintf('%02d', $hour) . ':00');
                    }

                    $e = new Entry();
                    $e->setTest($t)->setStudent($s)->setNoDos($em->getRepository(Entry::class)->getNextNoDos())->setRw(mt_rand(1,2) % 2 === 0 ? true : false)->setTstart($tStart);

                    if (mt_rand(1,3) % 2 != 0)
                    {
                        $start = $e->getTstart();
                        $end = new \DateTime($start->format('Y-m-d H:i'));
                        $end->modify('+' . mt_rand(2,6) . ' hours');
                        $end->modify('+' . mt_rand(1,59) .' minutes');

                        $e->setTend($end);
                        $e->setTemps($start->diff($end));
                    }
                    
                    $em->persist($e);
                    $em->flush();
                }
            }
        }

        $this->addFlash('success', "Le jeu de donnée a bien été ajouté !");
        return $this->redirectToRoute('parameter.index');
    }
}
