# 💻 Documentació del Frontend

## 📋 Taula de Continguts

1. [Requisits Previs](#requisits-previs)
2. [Instal·lació](#installació)
3. [Execució en Desenvolupament](#execució-en-desenvolupament)
4. [Estructura del Projecte](#estructura-del-projecte)

## Requisits Previs

* **Node.js**: Versió `LTS`.
  * *Recomanació:* Utilitzar [NVM](https://github.com/nvm-sh/nvm) (Node Version Manager) per a gestionar la versió.
* **Gestor de Paquets**:
  * npm (ve amb Node)
* **Git**: Per al control de versions.

## Instal·lació

Segueix estos passos per a instal·lar les dependències del projecte:

1. **Clonar el repositori** (si encara no ho has fet):
  ```bash
  git clone https://github.com/sergi1307/ProyectoIntermodular.git
  cd ProyectoIntermodular/Frontend
  ```

2. **Instal·lar dependències**:
  ```bash
  npm install
  ```

## Execució en Desenvolupament 

```bash
npm run dev
```

Una vegada en funcionament, `ctrl` + `click esquerre` en la URL: `http://localhost:5174/`

## Estructura del Projecte

```text
src/
|-- assets/  # Imatges i fitxers estàtics
|-- components/ # Components reutilitzables
|-- routes/ # Enrutament entre les vistes
|-- views/  # Vistes principals de l'aplicació
|-- main.js # Component arrel
```
