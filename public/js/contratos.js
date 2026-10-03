document.addEventListener('DOMContentLoaded', () => {


    const provincia = document.getElementById('idProvincia');
    const distrito = document.getElementById('idDistrito')

    if (!provincia || !distrito) {
        return;
    }

    provincia.addEventListener('change', async (e) => {
        const idProvincia = e.target.value;

        distrito.innerHTML = '<option value="">Seleccione un distrito</option>';

        if (idProvincia === 0){
            return;
        }   
            fetch('index.php?opction=obtenerDistritos', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'idProvicincia=' + encodeURIComponent(idProvincia)
            })
            .then(respuesta  => respuesta.json())
            .then(distritos => {
                distritos.forEach(distritoDato => {
                    const option = document.createElement('option');
                    option.value = distritoDato.idDistrito;
                    option.textContent = distritoDato.NombreDistrito;

                    distrito.appendChild(option);
                });
            })
        
            .catch(error => {
                console.error('Error al obtener los distritos:', error);
            });
    });

});