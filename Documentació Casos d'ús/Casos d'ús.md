# Casos d'ús
## Index
- [Casos d'ús](#casos-dús)
    + [Index](#index)
    + [Introducció](#introducció)
    + [Descripció general](#descripció-general)
    + [Actors del sistema](#actors-del-sistema)
    + [Funcionalitats dels casos d'ús](#casos-dús-funcionalitats)
        * [Funcionalitats del usuari](#funcionalitats-de-lusuari)
        * [Funcionalitats del venedor](#funcionalitats-del-venedor)
        * [Funcionalitats del comprador](#funcionalitats-del-comprador)
        * [Funcionalitats del sistema](#funcionalitats-del-sistema)
    + [Relacions Identificades](#relacions-identificades)
## Introducció
Este document descriu els actors, les funcionalitats (casos d'ús) i les relacions representades en l'esquema visual dels casos d'ús en l'aplicació web `ProxiMarkt`.

## Diagrama

## Descripció general
El diagrama representa les interaccions amb l'aplicació `ProxiMarkt`, una plataforma de comerç de proximitat. Defineix quines accions poden realitzar els diferents tipus d'usuaris dins del sistema (el requadre `Sistema ProxiMarkt`).

## Actors del sistema
El diagrama identifica tres actors principals organitzats jeràrquicament:

- Usuari: Representa a qualsevol persona registrada en la plataforma. És l'actor pare del qual hereten els altres rols.
- Comprador (Rol Comprador): És l'usuari especialitzat que busca adquirir productes locals. Hereta totes les funcions del rol `Usuari` i afig funcions de compra.
- Venedor (Rol Venedor/Agricultor): És l'usuari especialitzat que oferix productes. Hereta totes les funcions de `Usuari` i afig funcions de gestió i venda.

## Casos d'ús (Funcionalitats)
A continuació es detallen els casos d'ús agrupats per l'actor que els inicia en el diagrama:
### Funcionalitats de l'usuari
Estes accions estan disponibles tant per a compradors com per a venedors:
> Indicat per les línies sòlides que connecten `Usuari` amb els casos d'ús inferiors
- Registrar-se/Login: Permet a l'usuari crear un compte o autenticar-se per a accedir al sistema.
> Nota
    > És un requisit previ (`<include>`) per a realitzar transaccions com reservar.
- Gestionar Perfil: Permet actualitzar la informació personal, les dades de contacte o la ubicació.
- Xatejar/Acordar: Ferramenta de comunicació interna per a coordinar detalls del lliurament o resoldre dubtes entre les parts.
### Funcionalitats del Venedor
- Publicar Productes (Foto, Preu, ...): El venedor puja nous articles al sistema detallant les seues característiques.
- Gestionar Inventari: Actualització del `stock` disponible dels productes publicats.
- Establir Punts de Lliurament: El venedor defineix i gestiona les ubicacions físiques (granges, mercats) on es realitzaran les entregues.
- Gestionar Comanda (Acceptar/Rebutjar): El venedor revisa les sol·licituds de reserva i decideix si acceptar-les o rebutjar-les segons la disponibilitat.
- Relació: Esta acció desencadena una notificació al sistema ("Rebre").
- Valorar: El venedor emet una qualificació sobre el comprador després de la transacció per a fomentar la confiança.
### Funcionalitats del Comprador
- Buscar Productes: El comprador localitza articles usant filtres (preu, categoria, geolocalització).
- Reservar Producte: Acció principal on el comprador sol·licita un article.
- Relació: Inclou obligatòriament el "Login" i està vinculat a la busca prèvia.
- Consultar Estat: El comprador verifica en quina fase es troba la seua comanda (Pendent, Acceptat, Rebutjat).
- Valorar Venedor/Producte: El comprador deixa una ressenya sobre la qualitat del producte i el servici rebut.

### Funcionalitats del Sistema
- Rebre (Notificacions): Encara que apareix connectat a accions, representa la recepció d'alertes.
- S'activa quan es "Gestiona una Comanda" (avisa al comprador).
- S'activa quan es "Reserva un Producte" (avisa al venedor).

## Relacions Identificades
- Herència (Generalització): Les fletxes puntejades amb cap triangular buit que van de Vendre i Comprar cap a Usuari indiquen que els dos rols "són" usuaris i compartixen les funcions bàsiques (Login, Perfil, Xat).
- Associació: Les línies sòlides indiquen qui executa quina acció.
- Dependències (Línies puntejades dins del sistema):
    - Reservar Producte -> Registrar-se/Login: Indica una relació d'inclusió (No es pot reservar sense estar loguejat).
    - Reservar Producte -> Buscar Productes: Indica una relació d'extensió o flux (La reserva sol vindre derivada d'una busca).
    - Gestionar Comanda/Reservar -> Rebre: Indica un trigger (disparador) de notificacions.