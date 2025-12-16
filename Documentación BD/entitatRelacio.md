![Imagen de Entidad Relación](entidadRelación.drawio.png)

**TABLA USUARIOS**
- id_usuario
- nombre
- email
- contraseña
- telefono
- fecha_registro

**TABLA PRODUCTOS**
- id_producto
- id_usuario
- nombre
- descripción
- precio
- stock

**TABLA MENSAJES**
- id_mensaje
- id_producto
- id_emisor
- id_receptor
- contenido
- fecha_envio

**TABLA VENTAS**
- id_venta
- id_comprador
- id_vendedor
- id_punto_entrega
- id_valoraciones
- fecha_venta
- total
- estado

**TABLA PUNTOS DE ENTREGA**
- id_punto_entrega
- id_usuario
- nombre
- dirección
- latitud
- longitud

**VALORACIONES**
- id_valoracion
- id_venta
- id_usuario
- calificacion
- comentario
- fecha_valoracion