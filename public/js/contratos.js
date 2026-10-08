document.addEventListener('DOMContentLoaded', () => {


    const provincia = document.getElementById('idProvincia');
    const distrito = document.getElementById('idDistrito');

    if (!provincia || !distrito) {
        return;
    }

    provincia.addEventListener('change', async (e) => {
        const idProvincia = e.target.value;

        distrito.innerHTML = '<option value="">Seleccione un distrito</option>';

        if (idProvincia === ''){
            return;
        }   
            fetch('index.php?opcion=obtenerDistritos', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'idProvincia=' + encodeURIComponent(idProvincia)
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