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
        fecth('index.php?opction=obtenerDistritos'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-'
            }           
        }
    });

});