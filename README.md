# Intranet_TQMD

Plataforma interna desarrollada para TEQMED que centraliza la operación técnica y administrativa: catálogos de clientes, centros médicos, inventario de repuestos, solicitudes, informes técnicos y canales de soporte. Construida sobre Laravel 12 y Livewire, ofrece un front-end reactivo, flujos seguros con passkeys y un control granular de roles y privilegios.

## 🌐 Stack tecnológico

| Capa             | Tecnologías                                                                                |
| ---------------- | ------------------------------------------------------------------------------------------ |
| Backend          | PHP 8.2, Laravel 12, Livewire (Flux & Volt), autenticación tradicional + Passkeys/WebAuthn |
| Frontend         | Vite 6, Tailwind CSS 4, componentes Livewire server-driven                                 |
| Herramientas Dev | Composer scripts, Artisan, npm scripts (`vite`, `concurrently`, build)                     |

> Requisitos principales: ver `composer.json` y `package.json` para dependencias completas.

## 🧱 Arquitectura y organización

- **MVC de Laravel** para controladores, modelos Eloquent y vistas Blade/Livewire.
- **Middleware `role` y `privilege`** para aplicar control de acceso a nivel de ruta.
- **Módulos especializados** (clientes, centros, equipos, inventario, informes) con rutas segmentadas por permisos.
- **Base de datos** modelada mediante migración única `000000_full_schema_migration.php`, que crea catálogos, relaciones y tablas auxiliares (auditoría, passkeys, movimientos de stock, etc.).

## 📦 Módulos principales

1. **Autenticación y seguridad**: login estándar, passkeys, verificación en dos pasos y panel "Settings" (perfil, contraseña, apariencia, seguridad).
2. **Usuarios, roles y privilegios**: CRUD completo con asignación de privilegios por módulo y helpers (`HasRolesAndPrivileges`) para revisiones rápidas.
3. **Clientes y centros médicos**: administración separada por permisos de lectura/escritura y endpoints JSON para selects dependientes.
4. **Inventario de repuestos**: categorías, repuestos, estados, movimientos de stock y procesos de baja.
5. **Solicitudes y salidas**: flujo desde la creación de solicitud, aprobación, gestión de entregas y registro de salidas.
6. **Inventario técnico**: asignación/devolución de piezas a técnicos con seguimiento del estado.
7. **Equipos y tickets**: catálogo de equipos con CRUD segmentado y módulo de tickets para seguimientos.
8. **Llamados y categorías**: registro de llamados técnicos con clasificación.
9. **Informes técnicos**: generación de informes correctivos y preventivos, exportación/impresión en PDF.
10. **APIs auxiliares**: endpoints para poblar selects dinámicos (clientes → centros → equipos).

## 👤 Roles y perfiles

Roles cargados por defecto (ver `database/seeders/RoleSeeder.php`):

- **Administrador/Admin**: acceso completo, puede crear usuarios, roles, equipos y gestionar configuraciones globales.
- **Usuario**: rol base para operaciones diarias sobre catálogos, solicitudes y consultas.
- **Supervisor**: perfil avanzado para seguimiento y aprobaciones.
- **Auditor**: permisos de revisión en módulos de equipos y reportes.
- **Técnico**: enfocado en inventario técnico, gestión de solicitudes propias y carga de informes.

La relación `User -> Role -> Privilegios` permite extender fácilmente nuevos perfiles sin modificar la lógica de negocio.

## ⚙️ Funcionalidades destacadas

- **Control de acceso granular** con middleware `role` y `privilege` aplicado a cada recurso.
- **Gestión integral de activos**: clientes, centros médicos, equipos y repuestos conectados mediante selects dependientes y endpoints JSON.
- **Workflows de solicitudes** con aprobación, rechazo y entrega vinculada a salidas e inventario técnico.
- **Informes correctivos/preventivos en PDF** con rutas unificadas para descarga e impresión.
- **Soporte multicanal** (tickets y llamados) con categorización y asignación de técnicos.
- **Panel personalizable** para cada usuario (perfil, contraseñas, apariencia, passkeys) sin salir de la intranet.

## 🚀 Puesta en marcha

1. Clonar el repositorio y copiar `.env` desde `.env.example`.
2. Instalar dependencias PHP: `composer install`.
3. Instalar dependencias front: `npm install`.
4. Generar clave de aplicación y migrar base de datos:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
5. Ejecutar el entorno local:
   ```bash
   composer run dev
   ```
   Este script levanta el servidor Laravel, el listener de colas y Vite en paralelo.

> Para ambientes compartidos (hosting), puede subirse un script helper que ejecute `composer install`, `php artisan migrate --force` y `php artisan config:cache` según las políticas del proveedor.

## 📚 Recursos útiles

- `routes/web.php`: catálogo completo de rutas y middlewares aplicados.
- `database/migrations/000000_full_schema_migration.php`: definición íntegra del esquema.
- `database/seeders/*`: datos base para roles, usuarios e inventarios iniciales.
- `app/Models/Concerns/HasRolesAndPrivileges.php`: helpers para comprobación de roles/privilegios en modelos.

## 🧭 Arquitectura funcional

- **Capa de experiencia**: vistas Blade + componentes Livewire que aprovechan Tailwind para estilos. Flux/Volt administran formularios reactivos (p. ej. settings, inventario) sin recargar la página.
- **Capa de aplicación**: controladores RESTful en `app/Http/Controllers` segmentados por dominio (clientes, inventario, informes). Middleware `auth`, `twofactor`, `role` y `privilege` definen los flujos autorizados.
- **Capa de dominio/datos**: modelos Eloquent encapsulan relaciones (clientes-centros-equipos, roles-privilegios, solicitudes-salidas). La migración unificada crea catálogos auxiliares, tablas pivote y soporta auditoría.
- **Servicios auxiliares**: Passkeys, cola (`queue:listen`) y generación de PDF (`barryvdh/laravel-dompdf`) corren en procesos dedicados, coordinados por los scripts de Composer.

## 🤝 Contribución & estilo

1. Crear rama desde `main` siguiendo el formato `feature/tema` o `fix/bug`.
2. Ejecutar `composer test` y `npm run build` antes de abrir un PR.
3. Respetar estándares Laravel (PSR-12, inyección de dependencias, Form Requests). Para Livewire, mantener componentes pequeños y reutilizables.
4. Estilos en Tailwind: preferir utilidades existentes y evitar CSS ad-hoc salvo casos necesarios.
5. Documentar rutas o comandos nuevos en este README o en comentarios de código breves cuando el flujo no sea evidente.

## 🗺️ Roadmap

- [ ] **Automatizar despliegues**: agregar pipeline CI/CD con tests, migraciones y build front automatizados.
- [ ] **Reportes avanzados**: tablero de métricas (SLA, inventario crítico) integrado en el dashboard.
- [ ] **Notificaciones en tiempo real**: integrar WebSockets para avisos de solicitudes/tickets.
- [ ] **Modo offline técnico**: permitir registros de informes desde dispositivos móviles con sincronización posterior.
- [ ] **Internacionalización**: preparar switching ES/EN para clientes externos.

---

¿Necesitas un resumen ejecutivo, diagramas o instrucciones de despliegue específicas para un entorno? Abre un issue o contáctanos.
