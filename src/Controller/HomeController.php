<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use App\Entity\Test;
use App\Repository\TestRepository;

#[IsGranted('ROLE_USER')]
class HomeController extends AbstractController
{

    #[Route('/', name: 'home.index')]
    public function index(ChartBuilderInterface $chartBuilder, TestRepository $repository): Response
    {
        $summary = $repository->getSummary();
        if ($summary == null) {
            return $this->render('home/empty.html.twig');
        }

        $labels = [];
        $runners = [];
        $walkers = [];
        $abstention = [];

        
        foreach($summary as $sum)
        {
            $max = intval($sum['totalEntries']);
            $r = intval($sum['runners']);
            $w = intval($sum['walkers']);
            $a = intval($sum['abstentions']);

            array_push($labels, $sum['Name']);
            array_push($runners, $max == 0 ? 0 : round(($r / $max) * 100));
            array_push($walkers, $max == 0 ? 0 : round(($w / $max) * 100));
            array_push($abstention, $max == 0 ? 0 : round(($a / $max) * 100));
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => '% Coureurs',
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => '#9BD0F5',
                    'data' => $runners,
                ],
                [
                    'label' => '% Marcheurs',
                    'borderColor'=> '#FF6384',
                    'backgroundColor'=> '#FFB1C1',
                    'data' => $walkers,
                ],
                [
                    'label' => '% Abstention',
                    'backgroundColor' => '#537bc4',
                    'borderColor' => '#537bc4',
                    'data' => $abstention,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        /*
        new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Marcheur', 'Coureur', 'Absention'],
                    datasets: [{
                        label: `Nombre d'étudiants`,
                        data: dataSet,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        */

        return $this->render('home/index.html.twig', [
            'chart' => $chart,
        ]);
    }
}
