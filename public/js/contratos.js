document.addEventListener('DOMContentLoaded', () => {

    const provincia = document.getElementById('idProvincia');
    const distrito = document.getElementById('idDistrito');
    
    // rESTRINCCIONES
    const nombre = document.getElementById('Nombre');
    const apellidos = document.getElementById('Apellidos');
    const testigo = document.getElementById('Testigo');
    const tipoDOI = document.getElementById('TipoDOI');
    const numDOI = document.getElementById('NumDOI');
    const telefono = document.getElementById('Telefono');

    


















    //condicional de provicinia con distritos
    if (!provincia || !distrito) {
        return;
    }
    // Cuando seleccione una provincia
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