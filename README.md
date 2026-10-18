# Sistema de Gestión - Clínica Dental (UnionDental)

Sistema web integral para la gestión de citas odontológicas, expedientes clínicos, control de horarios y catálogo de procedimientos médicos bajo una arquitectura MVC estricta sobre contenedores Docker.

---

## Stack Tecnológico

* **Backend:** PHP 8.2 (POO + Patrón MVC).
* **Servidor Web:** Apache con módulo `mod_rewrite` habilitado.
* **Persistencia:** MySQL 8.0 (Motor InnoDB con integridad referencial y sentencias preparadas nativas vía PDO).
* **Gestión de BD:** phpMyAdmin en entorno contenido.
* **Orquestación:** Docker & Docker Compose.
* **Exposición Segura:** Cloudflare Zero Trust Tunnels (`cloudflared`).

---

## Requisitos Previos

* [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y en ejecución.
* [Git](https://git-scm.com/) instalado.

---

## Puesta en Marcha Rápida (Entorno Local)

### 1. Clonar el repositorio y posicionarse en la rama de integración

```bash
git clone <https://github.com/leviwpalma/DWSProyectoV1.git>
cd proyecto-clinica
git checkout develop


## Construir e inicializar los contenedores
docker compose up -d --build


