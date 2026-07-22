# Changelog

Todos los cambios notables de este proyecto se documentan en este archivo.
El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/).

---

## [Unreleased]

### Added
- Módulo de performance (`includes/performance/`): carga de scripts con `defer` para archivos internos y Partytown para externos, helpers de imagen y video, preconnects y preload del LCP.
- `analytics.php` para centralizar scripts de tracking del proyecto.
- Flag `CLTVO_PARTYTOWN` en `functions.php` para controlar el uso de Partytown en scripts de analytics.
- Flag `CLTVO_DISABLE_COMMENTS` en `functions.php` para desactivar comentarios globalmente, con soporte para excepciones por post type.
- Flag `title-tag` en `functions.php` para permitir que plugins como Yoast controlen el `<title>`.

## [3.3.1] - 2026-03-025

### Changed

- Gitignore ahora incluye la carpet de plugins

###  Security 

- Actualiza phpmailer a 6.12

## [3.3] - 2024-01-04

### Added

- Herramienta de sincronización manual de ACF: `Tools → ACF JSON Sync`.
- Validación que detecta Field Groups huérfanos (existen en DB pero no tienen JSON correspondiente).
- Extensión de `acf/settings/load_json` para incluir subdirectorios de `acf-json/`, resolviendo la incompatibilidad entre ACF y WPML.
- Carpeta `languages/` agregada al `.gitignore` para proyectos con múltiples idiomas.

### Changed
- Se elimina el sync automático de ACF al cargar el admin. El source of truth pasa a ser `acf-json/`; el sync es ahora manual bajo demanda.

---

## [3.2.2] - 2024-08-29

### Added
- Función `cltvo_role_edit()` en `functions.php` para otorgar al rol de editor permisos de administración de menús.

---

## [3.2.1] - 2024-01-04

### Changed
- La flag `CLTVO_USEMAILGUN` en `functions.php` ahora controla el uso de Mailgun vs. el mailer del servidor (WP Engine). Por defecto se encuentra en `false`.

---

## [3.2] - 2020-10-28

### Added
- `TGM Plugin Activation` para gestionar plugins sugeridos y obligatorios sin subirlos al repositorio.
- Sync automático de ACF mediante carpeta `acf-json/` dentro del tema. Al activar el tema, los campos se sincronizan automáticamente.
- Campo de location `Special Pages` en opciones de ACF para preservar la correspondencia de campos entre pages.
- Plugin Mailgun para el envío de correos; el campo `from` de su configuración determina el correo de contacto.

### Changed
- Compilación migrada de Gulp a `laravel-mix`. Comando: `npm run watch`. Compatible con cualquier versión de Node.

---

## [3.0] - 2020-01-03

### Added
- Estructura base del blank theme.
- Compilación con Gulp 3.*. Comando: `gulp watch`. Requiere Node < 9.
- Campo de correo de contacto en el admin: descomentar la clase `CltvoSocialNet` en `MetaboxServiceProvider`.

### Notes
- Los plugins se incluyen directamente en el repositorio. Cualquier actualización o adición debe hacerse desde local y pushearse.
- Los ACF no se sincronizan automáticamente; deben exportarse e importarse manualmente entre entornos, verificando la correspondencia de campos.