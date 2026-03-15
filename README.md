# Agencia de Viajes - Proyecto Web y Modelado

Este proyecto consiste en una aplicación web completa para una **Agencia de Viajes**, desarrollada en PHP nativo, que permite la gestión integral de paquetes turísticos y reservas. Además, incluye la documentación técnica y el modelado UML del sistema (Visual Paradigm).

## 🗂 Estructura del Proyecto

El repositorio se divide en dos secciones principales:

1. **`/Modelado`**:
   - `Documentacion.pdf`: Documentación exhaustiva del diseño del sistema, requerimientos y casos de uso.
   - `Viajes.vpp`: Archivo de proyecto de *Visual Paradigm* que contiene los diagramas UML (casos de uso, clases, secuencias, etc.) que estructuran el desarrollo de la aplicación.

2. **`/PHP/AgenciaPHP`**:
   - Código fuente de la aplicación web.
   - `bd.sql`: Script de la base de datos MySQL con la estructura de tablas y datos de prueba (usuarios, proveedores, paquetes turísticos, viajes y reservas).

## 🚀 Características y Funcionalidades Principales

La aplicación maneja tres tipos de roles (Administrador, Empleado, Usuario) y ofrece funcionalidades como:

*   **Sistema de Usuarios**: Registro, inicio de sesión y gestión del perfil.
*   **Catálogo de Viajes**: Exploración de paquetes turísticos disponibles.
*   **Filtros de Búsqueda Avanzados**: Filtrado de viajes por destino (país o ciudad), fecha de salida, tipo de viaje (cultural, romántico, urbano, etc.), rango de precio y duración en días.
*   **Sistema de Reservas**: Realización de reservas especificando el número de adultos, niños y tipo de servicio (Todo Incluido, Media Pensión, etc.).
*   **Gestión de Reservas del Usuario**: Revisión de reservas activas, historial y cancelación o pago de las mismas (`mis_reservas.php`).
*   **Panel de Administración**: Funciones exclusivas para `admin` y `empleado` orientadas a la gestión de datos maestros (`gestionar_paquetes.php`, `admin_reservas.php`).

## 🛠️ Tecnologías Empleadas

*   **Backend**: PHP (Procedimental) usando sesiones y conexión a base de datos mediante la extensión `mysqli`.
*   **Frontend**: HTML5, CSS3 nativo (`style.css`), tipografía Google Fonts (Poppins).
*   **Base de Datos**: MySQL.
*   **Modelado**: Visual Paradigm.


---
*Este proyecto fue desarrollado como práctica universitaria para la asignatura de Software Web.*

👨‍💻 Autores

Víctor García Crespo
Carlos Fernández Cano
Aarón Goméz Vera
David Sanz Olalla
Santiago Galindo Duran
Pablo Gallego Pérez
David Naranjo Osorio
