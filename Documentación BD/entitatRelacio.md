# Esquema E/R
## Index
- [Esquema E/R](#esquema-er)
    + [Index](#index)
    + [Explicació del diagrama E/R](#explicació-del-diagrama-er)
        * [Taules relacionals](#taules-relacionals)
            - [Usuaris](#usuaris)
            - [Productes](#productes)
            - [Missatges](#missatges)
            - [Vendes](#vendes)
            - [Punt de venda](#punt-de-venda)
            - [Ressenyes](#ressenyes)
            - [Categories](#categories)
        * [Relacions](#relacions)
    
## Explicació del diagrama E/R
Aquest document explica el següent esquema `Entitat/Relació (E/R)`: 
<p align= "center">
    <img src="../Documentación BD/entidadRelación.drawio.png" alt="Imatge d'Entitat/Relació" width="1000"/>
 </p>
<p align="center"><em>Fig 1: Entitat/Relació (E/R) de la base de dades</em></p> 

Anem a dividir l'explicació en 2 apartats importants:
- Taules amb els possibles valors en una base de dades
- Relacions de les taules del diagrama
Cada apartat del diagrama és important explicar-lo en diferents apartats perque és el que és pot implementar en el programa (backend/frontend) y en la base de dades.
### Taules Relacionals
#### Usuaris

**TABLE USERS**
Taula `d'usuaris` que conté els següents camps per a la base de dades:

- id_user INT PK
- name VARCHAR(255)
- email VARCHAR(255)
- password VARCHAR(255)
- phone CHAR(12)
- registration_date TIMESTAMP

I en altres llenguatges de programació seria de la següent forma:

- id_user INT PK
- name (String)
- email (String)
- password (String)
- phone (String)
- registration_date TIMESTAMP (Date)

#### Productes

**TABLE PRODUCTS**

Taula de `productes` que conté els següents camps per a la base de dades:

- id_product INT PK
- id_user INT FK
- name VARCHAR(255)
- description TEXT
- price DOUBLE
- stock DOUBLE
- image
- type_stock ENUM (Kg, unidad)
- state ENUM (Agotado, Reservado, Disponible)
- publication_date TIMESTAMP

I en altres llenguatges de programació seria de la següent forma:

- id_product (Int)
- id_user (Int)
- name (String)
- description (String)
- price (Double)
- stock (Double)
- image (String `si es una url`)
- type_stock (String controlat per validació que sols accepta `kg` o `unidad`)
- state (String controlat per validació que sols accepta `Agotado`, `Reservado` o `Disponible`)
- publication_date (Date)

#### Missatges

**TABLE MESSAGES**

Taula de `missatges` que conté els següents camps per a la base de dades:

- id_message INT PK
- id_product INT FK
- id_transmitter INT FK
- id_receiver INT FK
- content TEXT
- shipping_date TIMESTAMP

I en altres llenguatges de programació seria de la següent forma:

- id_message (Int)
- id_product (Int)
- id_transmitter (Int)
- id_receiver (Int)
- content (String)
- shipping_date TIMESTAMP (Date)

#### Vendes

**TABLE SALES**

Taula de `vendes` que conté els següents camps per a la base de dades:

- id_sale INT PK
- id_product INT FK
- id_buyer INT FK
- id_seller INT FK
- id_delivery_point INT FK
- id_review INT FK
- sale_date DATE
- total DOUBLE
- collection_date DATE
- state ENUM(Pendiente, En Curso, Terminado)

I en altres llenguatges de programació seria de la següent forma:

- id_sale (Int)
- id_product (Int)
- id_buyer (Int)
- id_seller (Int)
- id_delivery_point (Int)
- id_review (Int)
- sale_date (Date)
- total (Double)
- collection_date (Date)
- state (String controlat per validació que sols accepta `Pendiente`, `En Curso` o `Terminado`)

#### Punt de venda

**TABLE DELIVERY POINT**

Taula de `punt de venda` que conté els següents camps per a la base de dades:

- id_delivery_point INT PK
- id_user INT FK
- name VARCHAR(255)
- direction VARCHAR(255)
- latitude DOUBLE
- length DOUBLE

I en altres llenguatges de programació seria de la següent forma:

- id_delivery_point (Int)
- id_user (Int)
- name (String)
- direction (String)
- latitude (Double)
- length (Double)

#### Ressenyes

**TABLE REVIEWS**

Taula de `ressenyes` que conté els següents camps per a la base de dades:

- id_review INT PK
- id_sale INT FK
- calification ENUM(1,2,3,4,5)
- comment TEXT
- review_date DATE

I en altres llenguatges de programació seria de la següent forma:

- id_review (Int)
- id_sale (Int)
- calification (String controlat per validació que sols accepta `1`, `2`, `3`, `4` o `5`)
- comment (String)
- review_date (Date)

#### Categories

**TABLE CATEGORIES**

Taula de `categories` que conté els següents camps per a la base de dades:

- id_categorie INT PK
- id_product INT FK
- name VARCHAR(255)

I en altres llenguatges de programació seria de la següent forma:

- id_categorie (Int)
- id_product (Int)
- name (String)

### Relacions
#### Relacons m a m

En este diagrama no existeixen relacions molts a molts (m a m), sols ens tenim relacions:

- 1 a molts(1 a m)/molts a 1(m a 1)
- 1 a 1

#### Relacions (1 a m) o (m a 1)
**USERS 1<-->m MESSAGES**
Tenim 2 relacions d'un a molts, ja que en un missatge ha d'haver-hi 2 usuaris per a poder rebre i enviar missatges, i un usuari pot enviar molts missatges però solo els rebrà una persona.

**USERS 1<-->m PRODUCTS**
Tenim una relació d'un a molts, ja que un usuari pot tindre molts productes i un producte només el pot pujar un usuari.

**USERS 1<-->m SALES**
Tenim 2 relacions d'un a molts, ja que un venedor i un comprador poden estar en diverses vendes, però en una venda només pot haver-hi un venedor i un comprador.

**USERS 1<-->m DELIVERY POINT**
Tenim una relació d'un a molts, ja que un usuari pot tindre molts punts de lliurament però un punt de lliurament només pot tindre un usuari (hem pensat que el punt de lliurament és el camp propi del venedor i no un punt en comú entre els dos).

**MESSAGES m<-->1 PRODUCTS**
Tenim una relació d'un a molts, ja que d'un producte pot haver-hi molts xats amb diferents compradors, però en un xat només pot haver-hi un producte.

**SALES m<-->1 DELIVERY POINT**
Tenim una relació d'un a molts, ja que en un punt de lliurament es poden fer diverses vendes, però una venda només es pot fer en un punt de lliurament.

**SALES 1<-->m REVIEWS**
Tenim una relació d'un a molts, ja que una venda pot tindre diverses valoracions, però una valoració no pot estar en diverses vendes (pot haver-hi diverses vendes amb cinc estreles, però eixa valoració a un usuari no serà la mateixa que en una altra venda).

**PRODUCTS m<-->1 CATEGORIES**
Tenim una relació d'un a molts, ja que una categoría pot tindre diversos productes associats, pero un producte sols pot estar dins d'una categoría.

#### Relacions (1 a 1)

**PRODUCTS 1<-->1 SALES**
Tenim una relació d'un a un, ja que en una venda només s'embene un producte.