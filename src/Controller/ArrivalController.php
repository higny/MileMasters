<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Entry;
use App\Entity\Test;

#[Route('/arrivées')]
#[IsGranted('ROLE_USER')]
class ArrivalController extends AbstractController
{
    #[Route('/', name: 'arrival.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $tests = $em->getRepository(Test::class)->findAllWithEmptyTend();
        if ($tests == null)
        {
            return $this->render('arrival/empty.html.twig');
        }
        return $this->render('arrival/index.html.twig', [
            'tests' => $tests,
        ]);
    }

    #[Route('/test/{id}/detail', name: 'arrival.json.detail')]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $entries = $em->getRepository(Entry::class)->findEntriesByTestIdWithNullTendAndStudent($id);
        $runners = $em->getRepository(Entry::class)->findEntriesWithTendAndOrderedByTemps($id, true);
        $walkers = $em->getRepository(Entry::class)->findEntriesWithTendAndOrderedByTemps($id, false);

        return new JsonResponse([
            'entries' => $entries,
            'runners' => $runners,
            'walkers' => $walkers
        ]);
    }


    #[Route('/{id}', name: 'arrival.json.finish')]
    public function finish(int $id, EntityManagerInterface $em): Response
    {
        $entry = $em->getRepository(Entry::class)->findOneBy(['id' => $id]);

        $now = new \DateTime();
        $start = new \DateTime($entry->getTest()->getDate()->format('Y-m-d') . ' ' . $entry->getTstart()->format('H:i'));

        $entry->setTend($now);
        $entry->setTemps($start->diff($now));

        $em->persist($entry);
        $em->flush();

        $testId = $entry->getTest()->getId();

        $entries = $em->getRepository(Entry::class)->findEntriesByTestIdWithNullTendAndStudent($testId);
        $runners = $em->getRepository(Entry::class)->findEntriesWithTendAndOrderedByTemps($testId, true);
        $walkers = $em->getRepository(Entry::class)->findEntriesWithTendAndOrderedByTemps($testId, false);

        return new JsonResponse([
            'entries' => $entries,
            'runners' => $runners,
            'walkers' => $walkers
        ]);
    }
}
