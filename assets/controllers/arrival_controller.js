import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
    }

    refresh() {
        const selectedTest = document.getElementById('testId');

        this.fetchValue(selectedTest.value);
    }

    arrival() {
        const entrySelect = document.getElementById('entryId');

        this.finish(entrySelect.value);
    }

    finish = (id) => {
        fetch(`/arrivées/${id}`, {
            method: 'GET',
            headers: {
                'Content-type': 'application/json',
                'X-Requested-With' : 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => this.handleResponse(data));
    }

    fetchValue = (id) => {
        document.getElementById('arrival-container').classList.add('d-none');

        fetch(`/arrivées/test/${id}/detail`, {
            method: 'GET',
            headers: {
                'Content-type': 'application/json',
                'X-Requested-With' : 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => this.handleResponse(data));
    }

    handleResponse(data) {
        const entrySelect = document.getElementById('entryId');

        entrySelect.innerHTML = '';

        data.entries.forEach(entry => {
            var opt = document.createElement('option');
            opt.value = entry.id;
            opt.innerHTML = entry.displayValue;
            entrySelect.appendChild(opt);
        });

        this.handleRanking('row-runners', data.runners);
        this.handleRanking('row-walkers', data.walkers);
        
        document.getElementById('arrival-container').classList.remove('d-none');
    }

    handleRanking(elementName, list) {
        const container = document.getElementById(elementName)
        const table = document.querySelector(`#${elementName} tbody`);

        if (list.length > 0) {
            const trophy = ['trophy-gold', 'trophy-silver', 'trophy-bronze']

            list.forEach((data, idx) => {
                var tr = document.createElement('tr');
                var td = document.createElement('td');

                if (idx < 3) {
                    var i = document.createElement('i');
                    i.classList.add('bi', 'bi-trophy-fill', trophy[idx]);
                    td.appendChild(i);
                }

                tr.appendChild(td);

                td = document.createElement('td');
                td.innerHTML = data.displayValue
                tr.appendChild(td);

                td = document.createElement('td');
                td.innerHTML = data.formattedTemps
                tr.appendChild(td);

                table.appendChild(tr);
            });

            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    /*
    
            const runners = document.getElementById('row-runners');
            if (data.runners.length > 0) {
                const table = document.getElementById('row-runners > tbody');

                const trophy = ['trophy-gold', 'trophy-silver', 'trophy-bronze']

                data.runners.forEach((runner, idx) => {
                    var tr = document.createElement('tr');
                    var td = document.createElement('td');

                    if (idx > 3) {
                        var i = document.createElement('i');
                        i.classList.add(`bi bi-trophy-fill ${trophy[idx-1]}`);
                        td.appendChild(i);
                    }

                    tr.appendChild(td);

                    td = document.createElement('td');
                    td.innerHTML = runner.displayValue
                    tr.appendChild(td);

                    td = document.createElement('td');
                    td.innerHTML = runner.Temps
                    tr.appendChild(td);

                    table.appendChild(tr);
                });

                runners.classList.remove('d-none');
            } else {
                runners.classList.add('d-none');
            }
    */
}
