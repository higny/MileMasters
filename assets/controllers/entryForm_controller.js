import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const fetchValue = (id) => {
            fetch(`/épreuves/${id}/json`, {
                method: 'GET',
                headers: {
                    'Content-type': 'application/json',
                    'X-Requested-With' : 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const TstartElement = document.getElementById('entry_Tstart');
    
                if (data.Tstart != null) {
                    const date= new Date(data.Tstart.date)
    
                    let formattedHours = String(date.getHours()).padStart(2, '0');
                    let formattedMinutes = String(date.getMinutes()).padStart(2, '0');
                    TstartElement.value = formattedHours + ':' + formattedMinutes;
                    TstartElement.readOnly = true;
                } else {
                    TstartElement.readOnly = false;
                }
            })
        };
    
        const selectElement = document.getElementById('entry_Test');

        fetchValue(selectElement.value);

        selectElement.addEventListener('change', (event) => {
            const selectedId = event.target.value;

            fetchValue(selectedId);
        });
    }
}
