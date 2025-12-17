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

**RELACIONES**

**USUARIOS <--> MENSAJES**
Tenemos 2 relaciones de uno a muchos, ya que en un mensaje tiene que haber 2 usuarios para poder recibir y mandar mensajes, y un usuario puede mandar muchos mensajes pero solo los va a recibir una persona.

**USUARIOS <--> PRODUCTOS**
Tenemos una relación de uno a muchos, ya que un usuario puede tener muchos productos y un producto solo lo puede subir un usuario.

**USUARIOS <--> VENTAS**
Tenemos 2 relaciones de uno a muchos, ya que un vendedor y un comprador pueden estar en varias ventas, pero en una venta solo puede haber un vendedor y un comprador.

**USUARIOS <--> PUNTOS ENTREGA**
Tenemos una relación de uno a muchos, ya que un usuario puede tener muchos puntos de entrega pero un punto de entrega solo puede tener un usuario (hemos pensado que el punto de entrega es el campo propio del vendedor y no un punto en común entre ambos).

**MENSAJES <--> PRODUCTOS**
Tenemos una relación de uno a muchos, ya que de un producto puede haber muchos chats con diferentes compradores, pero en un chat solo puede haber un producto.

**PRODUCTOS <--> VENTAS**
Tenemos una relación de uno a uno, ya que en una venta solo se vende un producto.

**VENTAS <--> PUNTOS ENTREGA**
Tenemos una relación de uno a muchos, ya que en un punto de entrega se pueden hacer varias ventas, pero una venta solo se puede hacer en un punto de entrega.

**VENTAS <--> VALORACIONES**
Tenemos una relación de uno a muchos, ya que una venta puede tener varias valoraciones, pero una valoración no puede estar en varias ventas (puede haber varias ventas con cinco estrellas, pero esa valoración a un usuario no será la misma que en otra venta).
