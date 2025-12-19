# Prototip
## Index
- [Prototip](#prototip)
    + [Index](#index)
    + [Tecnologies utilitzades per al prototip](#tecnologies-utilitzades-per-al-prototip)
        * [Figma](#figma)
    + [Resum](#resum)
        * [Pantalla principal](#pantalla-principal)
        * [Pantalla de login](#pantalla-de-login)
        * [Pantalla d'usuaris](#pantalla-dusuaris)
            - [Pantalla del comprador](#pantalla-del-comprador)
            - [Pantalla del venedor](#pantalla-del-venedor)
                + [Control de l'inventari](#control-dinventari)
                + [Ordres](#ordres)
    + [Conclusio](#conclusió)
    
## Tecnologies utilitzades per al prototip
### Figma
<p align= "center">
    <img src="./img/Logos/Figma.png" alt="Figma" width="300"/>
 </p>
<p align="center"><em>Fig 1: Logo de Figma</em></p> 
Figma, desenvolupat per Dylan Field i Evan Wallace començaren a treballar en Figma en 2012 i el van llançar el 27 de setembre de 2016 és un editor de gràfics vectorial i una ferramenta de generació de prototips, principalment basada en la web, amb característiques “off-line” addicionals habilitades per aplicacions d’escriptori en macOS i Windows.

Hem utilitzat Figma únicament per a fer el prototip de l'aplicació. Ens va servir per a planificar el disseny visual abans de començar a programar l’aplicació, i assegurar-mon que l’estructura i els estils quedaren clars.

## Resum
Este document es tracta de documentar sobre el prototip de l'aplicació de `ProxiMarkt`, la qual consta dels següents apartats:
- Pantalla principal
- Pantalla de login
- Pantalla d'usuaris

### Pantalla Principal
La pantalla principal té com a objectiu introduir a l'aplicació, proporcionar informació de la empresa i dur cap a la pantalla de login, en ella pot veure informació sobre `ProxiMarkt` i que és, consta d'una capçalera per a introduir l'aplicació:

<p align= "center">
    <img src="./img/Cabecera_Index.png" alt="Capçalera" width="500"/>
</p>
<p align="center"><em>Fig 2: Capçalera de la pantalla principal</em></p> 

El cos de l'aplicació, que conté informació sobre `ProxiMarkt`:

<p align="center">
    <img src="./img/Section1_Index.png" alt="Secció 1" width="500"/>
</p>
<p align="center"><em>Fig 3: Primera secció de la pantalla principal</em></p> 

<p align="center">
    <img src="./img/Section2_Index.png" alt="Secció 2" width="500"/>
</p>
<p align="center"><em>Fig 4: Segunda secció de la pantalla principal</em></p> 

<p align="center">
    <img src="./img/Section3_Index.png" alt="Secció 3" width="600"/>
</p>
<p align="center"><em>Fig 5: Tercera secció de la pantalla principal</em></p> 

I per últim el peu de pàgina que conté informació per a la companyia i informació útil per als usuaris sobre com funciona l'aplicació.

<p align="center">
    <img src="./img/Footer_Index.png" alt="Footer" width="500" height="200"/>
</p>
<p align="center"><em>Fig 6: Peu de pàgina de la pantalla principal</em></p> 

### Pantalla de login

La pàgina de login és una pantalla en la qual l'usuari es pot registrar o iniciar sessió, per a iniciar sessió conté 2 camps:
- Correu electònic
- Contrasenya

<p align="center">
    <img src="./img/Login_Screen.png" alt="Login" width="500"/>
</p>
<p align="center"><em>Fig 7: Pantalla de login</em></p> 

I si li apretes al boto de `Registrar-se` s'obrirà una nova finestra en la qual s'afegira un nou camp:
- Nom complet
- Correu electònic
- Contrasenya 

<p align="center">
    <img src="./img/Register_Screen.png" alt="Pantalla de registre" width="500"/>
</p>
<p align="center"><em>Fig 8: Pantalla de registre</em></p> 

I quan et registres tornara a la pàgina de login i quan inicies sessió arribaràs a la següent i última pàgina, la pantalla d'usuaris.

### Pantalla d'usuaris
Per últim tenim la pantalla dels usuaris, que estan dividits en 2 rols importants:
- Comprador
- Venedor
Que cadasc
#### Pantalla del comprador

En la pantalla d'usuaris ja siga com a venedor com a comprador podran mirar/comprar productes tant posar-los en venda, també tens l'opció de filtrar els productes per:
- Preu
- Quantitat
- Categoria
- Distància màxima

<p align= "center">
    <img src="./img/Comprador_Prototip.png" alt="Pantalla del comprador" width="500"/>
 </p>
<p align="center"><em>Fig 9: Pantalla del comprador</em></p> 

Per últim quan selecciones un producte s'obrira una nova finestra en la qual es mostrara:
- Nom del producte
- Descripció del producte
- Valoracions del producte (Tant en estrelles com en un valor decimal fins a 5)

<p align= "center">
    <img src="./img/Buy_Screen.png" alt="Pantalla del producte" width="500"/>
 </p>
<p align="center"><em>Fig 10: Pantalla del producte</em></p>

I baix del producte tindràs 2 botons que permetran:  
- Comprar el producte
- Obrir el xat amb el venedor

<p align= "center">
    <img src="./img/Buy-Chat_Buttons.png" alt="Comprar/obrir el xat" width="500"/>
 </p>
<p align="center"><em>Fig 11: Botons per a comprar/obrir el xat</em></p>

#### Pantalla del venedor 
En la pantalla del venedor es mostraran:
- Els diners generats
- Les ordres totals
- Productes actius en eixe moment
- Clients totals
I per la part inferior es mostraran 2 opcions per a veure:
- Control d'inventari
- Ordres
##### Control d'inventari
Primerament pots afegir un producte a l'apretar-li a `Afegir producte` s'obrira una finestra en un formulari que conté els següents camps:
- Nom del producte
- Preu
- Stock disponible
- Descripció
- Imatge del producte
- Categoria
- Quantitat
- Localització per a la venda

Després es mostraran la llista de tots els productes que té disponibles el venedor junt amb l'opció de buscar productes i filtrar-los, que té el següent:
- Nom del producte
- Categoria
- Preu
- Stock disponible
- Estat
- Vendes

També tindrà altres botons per a:
- Veure el producte
- Modificar el producte
- Eliminar el producte

<p align= "center">
    <img src="./img/Inventory.png" alt="Gestió del inventari" width="500"/>
 </p>
<p align="center"><em>Fig 12: Menú Gestió del inventari</em></p>

##### Ordres
En les ordres es veuran les ordres que té el venedor amb els usuaris, les ordres apareixen de la següent forma:
- ID del ordre
- Nom del comprador
- Nom del producte
- Quantitat (kg, unitats)
- Preu total
- Estat (Pendent, Confirmat, Preparat i Completat)
- Botó que obri el xat amb el comprador

La qual també pots buscar una ordre en específica o pots filtrar les ordres pel seu estat:
- Pendent
- Confirmat
- Preparat
- Completat

<p align= "center">
    <img src="./img/Orders.png" alt="Ordres" width="500"/>
 </p>
<p align="center"><em>Fig 13: Menú Ordres</em></p>

## Conclusió
Com bé diu el seu nom, el prototip no serà la versió final de l'aplicació, però representa com es podria vore en un futur i/o ajudar en el desenvolupament de les vistes en aquest, i es podria afegir/eliminar contingut d'aquest prototip en l'aplicació final.
