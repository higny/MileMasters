import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        ['myChart1', 'myChart2', 'myChart3', 'myChart4', 'myChart5', 'myChart6', 'myChart7', 'myChart8', 'myChart9', 'myChart10'].forEach(ctx => {
            let dataSet = [];

            dataSet.push(Math.floor(Math.random() * 200) + 1);
            dataSet.push(Math.floor(Math.random() * 200) + 1);
            dataSet.push(Math.floor(Math.random() * 200) + 1);

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
        });
    }
}