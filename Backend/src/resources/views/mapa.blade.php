<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Tiendas</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        body { font-family: sans-serif; padding: 20px; }
        #map { 
            height: 500px; 
            width: 100%; 
            border: 2px solid #333;
            border-radius: 5px;
        }
        .lista-productos {
            padding-left: 20px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <h1>Mapa de Stock</h1>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"> </script>
        <script>
        var map = L.map('map').setView([40.4167, -3.70325], 6);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Recibimos los datos limpios desde el controlador
        const puntos = @json($puntos);

        puntos.forEach(punto => {
            // Verificamos que las coordenadas existan y no sean nulas
            if (punto.latitude != null && punto.length != null) {

                let htmlProductos = "";

                if (punto.products && punto.products.length > 0) {
                    punto.products.forEach(producto => {
                        htmlProductos += `<li>${producto.name} - ${producto.price}€</li>`;
                    });
                } else {
                    htmlProductos = "<li>No hay stock</li>";
                }

                const contenido = `
                    <h3>${punto.name}</h3>
                    <p>${punto.direction}</p>
                    <hr>
                    <b>Productos:</b>
                    <ul class="lista-productos">
                        ${htmlProductos}
                    </ul>
                `;

                L.marker([punto.latitude, punto.length])
                    .addTo(map)
                    .bindPopup(contenido);
            }
        });
    </script>

</body>
</html>