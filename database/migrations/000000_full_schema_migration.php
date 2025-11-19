<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $statements = [
            // =========================
            // CREATE TABLES
            // =========================

            <<<'SQL'
CREATE TABLE `adjuntos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `ruta` varchar(500) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `mime` varchar(120) DEFAULT NULL,
  `tamano` bigint(20) DEFAULT NULL,
  `subido_por` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `audits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(30) NOT NULL,
  `before_changes` longtext DEFAULT NULL,
  `after_changes` longtext DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `categorias_llamados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `categorias_repuestos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `subcategoria` varchar(150) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `centros_medicos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `cod_centro_medico` int(11) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `centro_dialisis` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(120) DEFAULT NULL,
  `region` varchar(120) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `email` varchar(255) DEFAULT NULL,
  `rut` varchar(50) NOT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `equipos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `id_maquina` varchar(100) DEFAULT NULL,
  `numero_serie` varchar(120) DEFAULT NULL,
  `horas_uso` int(10) UNSIGNED DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `estado` enum('Operativo','En observacion','Fuera de servicio','Baja') DEFAULT NULL,
  `cant_dias_fuera_serv` int(3) DEFAULT NULL,
  `codigo` varchar(80) NOT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `serie` varchar(120) DEFAULT NULL,
  `centro_medico_id` bigint(20) UNSIGNED DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `ultima_mantencion` date DEFAULT NULL,
  `proxima_mantencion` date DEFAULT NULL,
  `tipo_mantencion` enum('T1','T2','T3') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `estados_llamados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `estados_repuestos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `estados_solicitudes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `informes_correctivos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_folio` varchar(50) NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `centro_medico_id` bigint(20) UNSIGNED NOT NULL,
  `equipo_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_notificacion` date NOT NULL,
  `fecha_servicio` date NOT NULL,
  `problema_informado` text NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_cierre` time NOT NULL,
  `trabajo_realizado` text NOT NULL,
  `condicion_equipo` enum('operativo','en_observacion','fuera_de_servicio') NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `firma` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `firma_cliente` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL,
            <<<'SQL'
CREATE TABLE `informes_preventivos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_reporte_servicio` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `centro_medico_id` bigint(20) UNSIGNED NOT NULL,
  `equipo_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `numero_inventario` varchar(100) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `fecha_proximo_control` date DEFAULT NULL,
  `firma_tecnico` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `firma_cliente` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL,
            <<<'SQL'
CREATE TABLE `informe_correctivo_repuesto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `informe_correctivo_id` bigint(20) UNSIGNED NOT NULL,
  `repuesto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad_usada` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL,
            <<<'SQL'
CREATE TABLE `informe_preventivo_inspecciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `informe_preventivo_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `respuesta` enum('SI','NO','N/A') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL,
            <<<'SQL'
CREATE TABLE `inventario_tecnico` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tecnico_id` bigint(20) UNSIGNED NOT NULL,
  `repuesto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL,
  `solicitud_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('asignado','devuelto') NOT NULL DEFAULT 'asignado',
  `observacion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(11) DEFAULT NULL,
  `available_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `llamados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_llamado` varchar(80) NOT NULL,
  `fecha_hora_llamado` datetime NOT NULL,
  `centro_medico_id` bigint(20) UNSIGNED NOT NULL,
  `nombre_informante` varchar(255) NOT NULL,
  `id_equipo` varchar(120) NOT NULL,
  `equipo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `desperfecto` text NOT NULL,
  `tecnico_asignado_id` bigint(20) UNSIGNED NOT NULL,
  `categoria_llamado_id` bigint(20) UNSIGNED NOT NULL,
  `estado_id` bigint(20) UNSIGNED DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `movimientos_stock` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `repuesto_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `referencia_tipo` varchar(40) DEFAULT NULL,
  `referencia_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);
SQL,
            <<<'SQL'
CREATE TABLE `passkeys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `credential_id` varbinary(255) NOT NULL,
  `public_key` text DEFAULT NULL,
  `counter` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `transports` varchar(255) DEFAULT NULL,
  `attestation_type` varchar(255) DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `privilegios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `repuestos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serie` varchar(100) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `estado_id` bigint(20) UNSIGNED NOT NULL,
  `ubicacion` varchar(120) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL,
  `foto` varchar(500) DEFAULT NULL,
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `rol_privilegios` (
  `rol_id` bigint(20) UNSIGNED NOT NULL,
  `privilegio_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `salidas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `solicitud_id` bigint(20) UNSIGNED DEFAULT NULL,
  `repuesto_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_pedido_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_requiere_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `centro_medico_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `solicitudes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_solicitud` varchar(80) NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `tecnico_id` bigint(20) UNSIGNED NOT NULL,
  `clinica_id` bigint(20) UNSIGNED NOT NULL,
  `equipo_id` bigint(20) UNSIGNED NOT NULL,
  `razon` text NOT NULL,
  `estado_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `solicitud_repuesto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `solicitud_id` bigint(20) UNSIGNED NOT NULL,
  `repuesto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `orden` int(11) DEFAULT NULL,
  `usado` tinyint(1) DEFAULT NULL,
  `destino_devolucion` enum('bodega','laboratorio','cliente','tecnico') DEFAULT NULL,
  `fecha_uso` datetime DEFAULT NULL,
  `observacion` varchar(250) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_ticket` varchar(10) NOT NULL,
  `cliente` varchar(255) NOT NULL,
  `nombre_apellido` varchar(255) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `cargo` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id_numero_equipo` varchar(120) DEFAULT NULL,
  `modelo_maquina` varchar(255) DEFAULT NULL,
  `falla_presentada` text NOT NULL,
  `momento_falla` enum('En preparación','En diálisis','En desinfección','Otras') NOT NULL,
  `momento_falla_otras` varchar(255) DEFAULT NULL,
  `acciones_realizadas` text DEFAULT NULL,
  `estado` enum('pendiente','en_proceso','completado') DEFAULT 'pendiente',
  `tecnico_asignado_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_visita` datetime DEFAULT NULL,
  `llamado_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `ticket_historial` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) DEFAULT NULL,
  `tecnico_anterior` varchar(100) DEFAULT NULL,
  `tecnico_nuevo` varchar(100) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
            <<<'SQL'
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `rol_id` bigint(20) UNSIGNED DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,

            // =========================
            // INDEXES
            // =========================

            <<<'SQL'
ALTER TABLE `adjuntos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adjuntos_entity_idx` (`entity_type`,`entity_id`),
  ADD KEY `adjuntos_user_idx` (`subido_por`);
SQL,
            <<<'SQL'
ALTER TABLE `audits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aud_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_aud_user_action` (`user_id`,`action`),
  ADD KEY `idx_audits_created_at` (`created_at`);
SQL,
            <<<'SQL'
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);
SQL,
            <<<'SQL'
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);
SQL,
            <<<'SQL'
ALTER TABLE `categorias_llamados`
  ADD PRIMARY KEY (`id`);
SQL,
            <<<'SQL'
ALTER TABLE `categorias_repuestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categoria_nombre` (`nombre`);
SQL,
            <<<'SQL'
ALTER TABLE `centros_medicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `centros_cod_unico` (`cliente_id`,`cod_centro_medico`),
  ADD KEY `centros_medicos_cliente_id_foreign` (`cliente_id`);
SQL,
            <<<'SQL'
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clientes_rut_unique` (`rut`);
SQL,
            <<<'SQL'
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_equipos_codigo` (`codigo`),
  ADD KEY `equipos_centro_idx` (`centro_medico_id`),
  ADD KEY `idx_equipos_estado` (`estado`),
  ADD KEY `idx_equipos_centro` (`centro_medico_id`);
SQL,
            <<<'SQL'
ALTER TABLE `estados_llamados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `estados_llamados_nombre_unique` (`nombre`);
SQL,
            <<<'SQL'
ALTER TABLE `estados_repuestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `er_nombre_unique` (`nombre`),
  ADD UNIQUE KEY `uq_estado_nombre` (`nombre`);
SQL,
            <<<'SQL'
ALTER TABLE `estados_solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `estados_solicitudes_nombre_unique` (`nombre`);
SQL,
            <<<'SQL'
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);
SQL,
            <<<'SQL'
ALTER TABLE `informes_correctivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `correctivo_cliente_id` (`cliente_id`),
  ADD KEY `correctivo_centro_medico_id` (`centro_medico_id`),
  ADD KEY `correctivo_equipo_id` (`equipo_id`),
  ADD KEY `correctivo_usuario_id` (`usuario_id`);
SQL,
            <<<'SQL'
ALTER TABLE `informes_preventivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `preventivo_centro_medico_id` (`centro_medico_id`),
  ADD KEY `preventivo_equipo_id` (`equipo_id`),
  ADD KEY `preventivo_usuario_id` (`usuario_id`);
SQL,
            <<<'SQL'
ALTER TABLE `informe_correctivo_repuesto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `icr_informe_id` (`informe_correctivo_id`),
  ADD KEY `icr_repuesto_id` (`repuesto_id`);
SQL,
            <<<'SQL'
ALTER TABLE `informe_preventivo_inspecciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipi_informe_id` (`informe_preventivo_id`);
SQL,
            <<<'SQL'
ALTER TABLE `inventario_tecnico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tecnico_repuesto_estado` (`tecnico_id`,`repuesto_id`,`estado`),
  ADD KEY `fk_invtec_repuesto` (`repuesto_id`);
SQL,
            <<<'SQL'
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);
SQL,
            <<<'SQL'
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);
SQL,
            <<<'SQL'
ALTER TABLE `llamados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `llamados_numero_llamado_unique` (`numero_llamado`),
  ADD KEY `llamados_centro_medico_id_foreign` (`centro_medico_id`),
  ADD KEY `llamados_tecnico_asignado_id_foreign` (`tecnico_asignado_id`),
  ADD KEY `llamados_categoria_llamado_id_foreign` (`categoria_llamado_id`),
  ADD KEY `idx_llamados_centro_fecha` (`centro_medico_id`,`fecha_hora_llamado`),
  ADD KEY `llamados_equipo_id_idx` (`equipo_id`),
  ADD KEY `llamados_estado_id_idx` (`estado_id`);
SQL,
            <<<'SQL'
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);
SQL,
            <<<'SQL'
ALTER TABLE `movimientos_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ms_repuesto_fecha` (`repuesto_id`,`created_at`),
  ADD KEY `idx_ms_usuario` (`usuario_id`),
  ADD KEY `idx_ms_usuario_fecha` (`usuario_id`,`created_at`),
  ADD KEY `idx_ms_created_at` (`created_at`);
SQL,
            <<<'SQL'
ALTER TABLE `passkeys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `passkeys_user_id_index` (`user_id`);
SQL,
            <<<'SQL'
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);
SQL,
            <<<'SQL'
ALTER TABLE `privilegios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `privilegios_nombre_unique` (`nombre`);
SQL,
            <<<'SQL'
ALTER TABLE `repuestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `repuestos_serie_unique` (`serie`),
  ADD KEY `idx_repuestos_nombre` (`nombre`),
  ADD KEY `idx_repuestos_serie` (`serie`),
  ADD KEY `repuestos_categoria_id_foreign` (`categoria_id`),
  ADD KEY `repuestos_usuario_id_foreign` (`usuario_id`),
  ADD KEY `repuestos_estado_id_foreign` (`estado_id`),
  ADD KEY `idx_repuestos_categoria_estado` (`categoria_id`,`estado_id`);
SQL,
            <<<'SQL'
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_nombre_unique` (`nombre`);
SQL,
            <<<'SQL'
ALTER TABLE `rol_privilegios`
  ADD PRIMARY KEY (`rol_id`,`privilegio_id`),
  ADD KEY `rol_privilegios_privilegio_id_foreign` (`privilegio_id`),
  ADD KEY `rol_privilegios_rol_id_foreign` (`rol_id`);
SQL,
            <<<'SQL'
ALTER TABLE `salidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salidas_solicitud_id_foreign` (`solicitud_id`),
  ADD KEY `salidas_repuesto_id_foreign` (`repuesto_id`),
  ADD KEY `salidas_usuario_pedido_id_foreign` (`usuario_pedido_id`),
  ADD KEY `salidas_usuario_requiere_id_foreign` (`usuario_requiere_id`),
  ADD KEY `salidas_centro_medico_id_foreign` (`centro_medico_id`),
  ADD KEY `idx_salidas_centro_fecha` (`centro_medico_id`,`fecha_hora`);
SQL,
            <<>'SQL'
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);
SQL,
            <<>'SQL'
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `solicitudes_numero_solicitud_unique` (`numero_solicitud`),
  ADD KEY `solicitudes_tecnico_id_foreign` (`tecnico_id`),
  ADD KEY `solicitudes_clinica_id_foreign` (`clinica_id`),
  ADD KEY `solicitudes_estado_id_foreign` (`estado_id`),
  ADD KEY `idx_solicitudes_tecnico_estado` (`tecnico_id`,`estado_id`),
  ADD KEY `idx_solicitudes_clinica_fecha` (`clinica_id`,`fecha_solicitud`),
  ADD KEY `idx_solicitudes_clinica_equipo` (`clinica_id`,`equipo_id`),
  ADD KEY `fk_solicitudes_equipo` (`equipo_id`);
SQL,
            <<>'SQL'
ALTER TABLE `solicitud_repuesto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_solicitud_repuesto` (`solicitud_id`,`repuesto_id`),
  ADD KEY `solicitud_repuesto_solicitud_id_foreign` (`solicitud_id`),
  ADD KEY `solicitud_repuesto_repuesto_id_foreign` (`repuesto_id`),
  ADD KEY `idx_sr_usado` (`usado`);
SQL,
            <<>'SQL'
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_ticket` (`numero_ticket`),
  ADD KEY `idx_tickets_numero` (`numero_ticket`),
  ADD KEY `idx_tickets_estado` (`estado`),
  ADD KEY `idx_tickets_created` (`created_at`),
  ADD KEY `fk_tickets_llamado` (`llamado_id`),
  ADD KEY `idx_tickets_tecnico` (`tecnico_asignado_id`),
  ADD KEY `idx_tickets_fecha_visita` (`fecha_visita`);
SQL,
            <<>'SQL'
ALTER TABLE `ticket_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);
SQL,
            <<>'SQL'
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_rol_id_foreign` (`rol_id`);
SQL,

            // =========================
            // AUTO_INCREMENT
            // =========================

            <<<'SQL'
ALTER TABLE `adjuntos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `audits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `categorias_llamados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `categorias_repuestos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `centros_medicos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `equipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `estados_llamados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `estados_repuestos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `estados_solicitudes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `informes_correctivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `informes_preventivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `informe_correctivo_repuesto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `informe_preventivo_inspecciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `inventario_tecnico`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `llamados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `movimientos_stock`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `passkeys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `privilegios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `repuestos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `salidas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `solicitudes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `solicitud_repuesto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `ticket_historial`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,
            <<>'SQL'
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL,

            // =========================
            // VIEWS (sin DEFINER)
            // =========================

            <<>'SQL'
DROP VIEW IF EXISTS `vw_kardex_repuesto`;
SQL,
            <<>'SQL'
CREATE VIEW `vw_kardex_repuesto` AS
SELECT
  `ms`.`id` AS `id`,
  `ms`.`repuesto_id` AS `repuesto_id`,
  `r`.`serie` AS `serie`,
  `r`.`nombre` AS `nombre`,
  `ms`.`tipo` AS `tipo`,
  `ms`.`cantidad` AS `cantidad`,
  `ms`.`stock_anterior` AS `stock_anterior`,
  `ms`.`stock_nuevo` AS `stock_nuevo`,
  `ms`.`usuario_id` AS `usuario_id`,
  `ms`.`referencia_tipo` AS `referencia_tipo`,
  `ms`.`referencia_id` AS `referencia_id`,
  `ms`.`created_at` AS `created_at`,
  sum(
    case when `ms`.`tipo` = 'entrada' then `ms`.`cantidad`
         else -`ms`.`cantidad`
    end
  ) over (
    partition by `ms`.`repuesto_id`
    order by `ms`.`created_at`, `ms`.`id`
    rows between unbounded preceding and current row
  ) AS `saldo_acumulado`
FROM `movimientos_stock` `ms`
JOIN `repuestos` `r` ON (`r`.`id` = `ms`.`repuesto_id`);
SQL,
            <<>'SQL'
DROP VIEW IF EXISTS `vw_ultimo_movimiento_repuesto`;
SQL,
            <<>'SQL'
CREATE VIEW `vw_ultimo_movimiento_repuesto` AS
SELECT
  `t`.`id` AS `id`,
  `t`.`repuesto_id` AS `repuesto_id`,
  `t`.`tipo` AS `tipo`,
  `t`.`cantidad` AS `cantidad`,
  `t`.`stock_anterior` AS `stock_anterior`,
  `t`.`stock_nuevo` AS `stock_nuevo`,
  `t`.`usuario_id` AS `usuario_id`,
  `t`.`referencia_tipo` AS `referencia_tipo`,
  `t`.`referencia_id` AS `referencia_id`,
  `t`.`created_at` AS `created_at`,
  `t`.`rn` AS `rn`
FROM (
  SELECT
    `ms`.`id` AS `id`,
    `ms`.`repuesto_id` AS `repuesto_id`,
    `ms`.`tipo` AS `tipo`,
    `ms`.`cantidad` AS `cantidad`,
    `ms`.`stock_anterior` AS `stock_anterior`,
    `ms`.`stock_nuevo` AS `stock_nuevo`,
    `ms`.`usuario_id` AS `usuario_id`,
    `ms`.`referencia_tipo` AS `referencia_tipo`,
    `ms`.`referencia_id` AS `referencia_id`,
    `ms`.`created_at` AS `created_at`,
    row_number() over (
      partition by `ms`.`repuesto_id`
      order by `ms`.`created_at` desc, `ms`.`id` desc
    ) AS `rn`
  FROM `movimientos_stock` `ms`
) AS `t`
WHERE `t`.`rn` = 1;
SQL,

            // =========================
            // FOREIGN KEYS
            // =========================

            <<>'SQL'
ALTER TABLE `adjuntos`
  ADD CONSTRAINT `fk_adjuntos_user` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `audits`
  ADD CONSTRAINT `fk_audits_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `centros_medicos`
  ADD CONSTRAINT `fk_centros_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `equipos`
  ADD CONSTRAINT `fk_equipos_centro` FOREIGN KEY (`centro_medico_id`) REFERENCES `centros_medicos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `informes_correctivos`
  ADD CONSTRAINT `fk_correctivo_centro` FOREIGN KEY (`centro_medico_id`) REFERENCES `centros_medicos` (`id`),
  ADD CONSTRAINT `fk_correctivo_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `fk_correctivo_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `fk_correctivo_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);
SQL,
            <<>'SQL'
ALTER TABLE `informes_preventivos`
  ADD CONSTRAINT `fk_preventivo_centro` FOREIGN KEY (`centro_medico_id`) REFERENCES `centros_medicos` (`id`),
  ADD CONSTRAINT `fk_preventivo_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `fk_preventivo_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);
SQL,
            <<>'SQL'
ALTER TABLE `informe_correctivo_repuesto`
  ADD CONSTRAINT `fk_icr_informe` FOREIGN KEY (`informe_correctivo_id`) REFERENCES `informes_correctivos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_icr_repuesto` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`);
SQL,
            <<>'SQL'
ALTER TABLE `informe_preventivo_inspecciones`
  ADD CONSTRAINT `fk_ipi_informe` FOREIGN KEY (`informe_preventivo_id`) REFERENCES `informes_preventivos` (`id`) ON DELETE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `inventario_tecnico`
  ADD CONSTRAINT `fk_invtec_repuesto` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invtec_tecnico` FOREIGN KEY (`tecnico_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `llamados`
  ADD CONSTRAINT `fk_llamados_categoria` FOREIGN KEY (`categoria_llamado_id`) REFERENCES `categorias_llamados` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_llamados_centro` FOREIGN KEY (`centro_medico_id`) REFERENCES `centros_medicos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_llamados_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_llamados_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados_llamados` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_llamados_tecnico` FOREIGN KEY (`tecnico_asignado_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `movimientos_stock`
  ADD CONSTRAINT `fk_mov_stock_repuesto` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mov_stock_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `passkeys`
  ADD CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `repuestos`
  ADD CONSTRAINT `fk_repuestos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_repuestos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repuestos_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados_repuestos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repuestos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `rol_privilegios`
  ADD CONSTRAINT `fk_rp_privilegios` FOREIGN KEY (`privilegio_id`) REFERENCES `privilegios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rp_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `salidas`
  ADD CONSTRAINT `fk_salidas_centro` FOREIGN KEY (`centro_medico_id`) REFERENCES `centros_medicos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_salidas_repuesto` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_salidas_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_salidas_usuario_pedido` FOREIGN KEY (`usuario_pedido_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_salidas_usuario_requiere` FOREIGN KEY (`usuario_requiere_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_solicitudes_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `centros_medicos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitudes_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitudes_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados_solicitudes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitudes_tecnico` FOREIGN KEY (`tecnico_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `solicitud_repuesto`
  ADD CONSTRAINT `fk_solicitud_repuesto_repuesto` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_repuesto_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_tecnico` FOREIGN KEY (`tecnico_asignado_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
SQL,
            <<>'SQL'
ALTER TABLE `ticket_historial`
  ADD CONSTRAINT `ticket_historial_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`);
SQL,
            <<>'SQL'
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
SQL,
        ];

        foreach ($statements as $sql) {
            DB::unprepared($sql);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::statement('DROP VIEW IF EXISTS `vw_kardex_repuesto`');
        DB::statement('DROP VIEW IF EXISTS `vw_ultimo_movimiento_repuesto`');

        Schema::dropIfExists('users');
        Schema::dropIfExists('ticket_historial');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('solicitud_repuesto');
        Schema::dropIfExists('solicitudes');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('salidas');
        Schema::dropIfExists('rol_privilegios');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('repuestos');
        Schema::dropIfExists('privilegios');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('passkeys');
        Schema::dropIfExists('movimientos_stock');
        Schema::dropIfExists('migrations');
        Schema::dropIfExists('llamados');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('inventario_tecnico');
        Schema::dropIfExists('informe_preventivo_inspecciones');
        Schema::dropIfExists('informe_correctivo_repuesto');
        Schema::dropIfExists('informes_preventivos');
        Schema::dropIfExists('informes_correctivos');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('estados_solicitudes');
        Schema::dropIfExists('estados_repuestos');
        Schema::dropIfExists('estados_llamados');
        Schema::dropIfExists('equipos');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('centros_medicos');
        Schema::dropIfExists('categorias_repuestos');
        Schema::dropIfExists('categorias_llamados');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('audits');
        Schema::dropIfExists('adjuntos');

        Schema::enableForeignKeyConstraints();
    }
};
