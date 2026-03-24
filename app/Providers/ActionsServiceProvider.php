<?php

namespace App\Providers;

class ActionsServiceProvider
{
    public function boot()
    {
        add_action('after_setup_theme', [$this, 'afterSetupTheme']);
        add_action('admin_init',        [$this, 'adminInit']);
        add_action('admin_menu',        [$this, 'adminMenu']);
        add_action('init',             [$this, 'init']);
        add_action('tgmpa_register',   [$this, 'registerRequiredPlugins']);

        add_action('after_setup_theme', function () {
            if ( current_theme_supports('CLTVO_DISABLE_COMMENTS') ) {
                $this->wireDisableComments();
            }
        }, 20);
    }

    public function init()
    {
        update_option('page_on_front', specialPage('splash'));
        update_option('show_on_front', 'page');

        global $wp_post_types;
        $wp_post_types['page']->exclude_from_search = true;
    }

    public function afterSetupTheme()
    {
        load_theme_textdomain(TRANSDOMAIN, get_template_directory() . '/languages');
    }

    public function adminInit()
    {
        if (isSpecialPage('contacto')) {
            remove_post_type_support('page', 'editor');
        }

        if ( $this->isAcfActive() ) {
            // IMPORTANTÍSIMO: WPML + ACF Local JSON separa por idioma (acf-json/en, acf-json/es)
            // Esto asegura que ACF cargue TODOS los subfolders, sin cambiar cómo se guardan.
            $this->acfForceLoadAllJsonLanguageDirs();

            $this->specialPageLocation();
        }
    }

    public function adminMenu()
    {
        if ( current_user_can('manage_options') ) {
            add_management_page(
                'ACF JSON Sync',
                'ACF JSON Sync',
                'manage_options',
                'cltvo-acf-json-sync',
                [$this, 'acfJsonSyncToolPage']
            );
        }
    }

    public function register()
    {
        //
    }

    public function registerRequiredPlugins()
    {
        $plugins = require_once __DIR__ . '/../../config/required_plugins.php';
        $config  = require_once __DIR__ . '/../../config/conf_plugins.php';

        tgmpa($plugins, $config);
    }

    public function isAcfActive(): bool
    {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return (
            is_plugin_active('advanced-custom-fields-pro/acf.php') ||
            is_plugin_active('advanced-custom-fields/acf.php')
        );
    }

    /**
     * ===========================
     * ACF JSON SYNC (DB <- JSON)
     * ===========================
     *
     * Nota sobre "deletes":
     * - Este sync SOLO importa/actualiza desde acf-json hacia la DB.
     * - Si un Field Group se elimina del repo (se borra su JSON), NO lo borramos de la DB automáticamente.
     *   Motivo: es inseguro sin backup y ACF/Local JSON tampoco elimina field groups de la DB por ausencia de JSON.
     * - Resultado: pueden quedar "huérfanos" (existen en DB, pero ya no existen en acf-json).
     *   Esos deben revisarse y, si procede, moverse a la papelera o borrarse manualmente con certeza.
     */
    public function syncAcfFields(): array
    {
        $report = [
            'found'         => 0,
            'imported'      => 0,
            'imported_keys' => [],
            'skipped'       => 0,
            'skipped_items' => [], // [ ['key'=>..., 'reason'=>...], ... ]
        ];

        if ( ! function_exists('acf_get_field_groups') ) {
            $report['skipped_items'][] = ['key' => '-', 'reason' => 'ACF no está activo o no se cargó.'];
            return $report;
        }

        $groups = acf_get_field_groups();
        if ( empty($groups) ) {
            return $report;
        }

        // Detectar qué grupos necesitan sync (igual que ACF):
        // - no existen en DB (ID vacío) o
        // - JSON tiene modified > post_modified_time del DB
        $sync = [];

        foreach ($groups as $group) {
            $local    = acf_maybe_get($group, 'local', false);
            $modified = acf_maybe_get($group, 'modified', 0);
            $private  = acf_maybe_get($group, 'private', false);

            if ($local !== 'json' || $private) {
                continue;
            }

            if ( empty($group['ID']) ) {
                $sync[$group['key']] = $group;
                continue;
            }

            $db_modified = get_post_modified_time('U', true, $group['ID'], true);
            if ($modified && $modified > $db_modified) {
                $sync[$group['key']] = $group;
            }
        }

        $report['found'] = count($sync);

        if ( empty($sync) ) {
            return $report;
        }

        // Import desde local json sin re-escribir archivos
        acf_disable_filters();
        acf_enable_filter('local');
        acf_update_setting('json', false);

        foreach ($sync as $group_key => $group) {

            // Importamos el array del group que nos dio ACF (trae location, position, style, etc.)
            $field_group = $group;

            // Guardrail 1: location debe existir y ser array (evita array_values(null) dentro de ACF)
            if ( ! isset($field_group['location']) || ! is_array($field_group['location']) ) {
                $report['skipped']++;
                $report['skipped_items'][] = ['key' => $group_key, 'reason' => 'Saltado: location faltante o inválida (evita crash / import corrupto).'];
                continue;
            }

            // Guardrail 2: hide_on_screen debe ser array
            if ( ! isset($field_group['hide_on_screen']) || ! is_array($field_group['hide_on_screen']) ) {
                $field_group['hide_on_screen'] = [];
            }

            /**
             * IMPORTANTE:
             * Usamos acf_get_fields($group_key) (como tu función original) para evitar problemas con subfields.
             * Esto funciona bien siempre y cuando ACF esté cargando el JSON correcto (por eso el load_json "all dirs").
             */
            if ( function_exists('acf_have_local_fields') && acf_have_local_fields($group_key) ) {
                $fields = acf_get_fields($group_key); // <- la receta “safe” del snippet original
                if (!is_array($fields)) {
                    $fields = [];
                }
                $field_group['fields'] = $fields;
            }

            // Guardrail 3: si no hay fields, no importes (evita duplicar “grupos vacíos”)
            if ( empty($field_group['fields']) || ! is_array($field_group['fields']) ) {
                $report['skipped']++;
                $report['skipped_items'][] = ['key' => $group_key, 'reason' => 'Saltado: el grupo no tiene fields (o no se pudieron cargar).'];
                continue;
            }

            try {
                acf_import_field_group($field_group);
                $report['imported']++;
                $report['imported_keys'][] = $group_key;
            } catch (\Throwable $e) {
                $report['skipped']++;
                $report['skipped_items'][] = ['key' => $group_key, 'reason' => 'Error al importar: ' . $e->getMessage()];
            }
        }

        return $report;
    }

    public function specialPageLocation()
    {
        if ( function_exists('acf_register_location_type') ) {
            include_once(__DIR__ . '/../../includes/custom/CustomLocation.php');
            acf_register_location_type('CustomLocation');
        }
    }

    /**
     * ===============
     * WPML HELPERS
     * ===============
     */
    private function hasWpml(): bool
    {
        return defined('ICL_SITEPRESS_VERSION') && isset($GLOBALS['sitepress']);
    }

    private function wpmlGetLanguages(): array
    {
        if (!$this->hasWpml()) {
            return [];
        }

        $langs = apply_filters('wpml_active_languages', null, ['skip_missing' => 0]);

        $out = [];
        if (is_array($langs)) {
            foreach ($langs as $code => $data) {
                $out[$code] = [
                    'code'            => $code,
                    'native_name'     => $data['native_name'] ?? strtoupper($code),
                    'translated_name' => $data['translated_name'] ?? strtoupper($code),
                ];
            }
        }
        return $out;
    }

    private function wpmlSwitchTo(string $lang): array
    {
        if (!$this->hasWpml()) {
            return [null, false];
        }

        $sitepress = $GLOBALS['sitepress'];
        $restore   = $sitepress->get_current_language();

        if ($restore !== $lang) {
            $sitepress->switch_lang($lang, true);
            return [$restore, true];
        }

        return [$restore, false];
    }

    private function wpmlRestore(?string $restore_lang, bool $did_switch): void
    {
        if (!$this->hasWpml()) {
            return;
        }

        if ($restore_lang && $did_switch) {
            $GLOBALS['sitepress']->switch_lang($restore_lang, true);
        }
    }

    /**
     * ACF Local JSON + WPML:
     * ACF Pro (includes/wpml.php) cambia save/load_json por idioma (acf-json/en, acf-json/es).
     * Esto provoca que si un JSON solo existe en /en, en admin ES no se cargue.
     *
     * Solución: asegurar que ACF siempre cargue TODOS los subfolders dentro de acf-json/
     * (sin cambiar cómo se guardan los archivos).
     */
    private function acfForceLoadAllJsonLanguageDirs(): void
    {
        add_filter('acf/settings/load_json', function(array $paths) {

            $base = get_stylesheet_directory() . '/acf-json';

            if (is_dir($base)) {
                $paths[] = $base;
            }

            // Subcarpetas: en, es, all, fr...
            if (is_dir($base)) {
                $dirs = glob($base . '/*', GLOB_ONLYDIR);
                if (is_array($dirs)) {
                    foreach ($dirs as $dir) {
                        $paths[] = $dir;
                    }
                }
            }

            // Limpieza: sin duplicados + sin nulls
            $paths = array_filter($paths, function($p){
                return is_string($p) && $p !== '';
            });
            $paths = array_values(array_unique($paths));

            return $paths;
        }, 999);
    }

    /**
     * ===========================
     * HUÉRFANOS (DB vs FILESYSTEM)
     * ===========================
     *
     * Esta versión NO depende de acf_get_field_groups() (porque puede ocultar trash, etc.)
     * - JSON keys: se sacan del filesystem (acf-json/--/group_*.json)
     * - DB groups: se sacan con WP_Query (post_type=acf-field-group, post_status=any)
     *
     * Resultado:
     * - Si borras un JSON pero el grupo sigue en DB -> SIEMPRE aparece huérfano.
     */
    private function acfGetOrphansReport(): array
    {
        $out = [
            'count' => 0,
            'items' => [],  // ['key','title','id','status','edit_url']
            'json_keys_count' => 0,
        ];

        // 1) Keys existentes en JSON (filesystem)
        $json_keys = $this->acfGetJsonKeysFromFilesystem();
        $out['json_keys_count'] = count($json_keys);

        // 2) Field groups existentes en DB (incluye trash)
        $db_groups = $this->acfGetDbFieldGroupsAnyStatus();

        foreach ($db_groups as $g) {
            $key = $g['key'];

            // Huérfano = existe en DB pero NO existe su JSON en ninguna carpeta
            if ( ! isset($json_keys[$key]) ) {
                $out['items'][] = $g;
            }
        }

        $out['count'] = count($out['items']);

        usort($out['items'], function($a, $b){
            return strcasecmp($a['title'], $b['title']);
        });

        return $out;
    }

    /**
     * Devuelve un set de keys que existen en filesystem:
     * [ 'group_xxx' => true, ... ]
     */
    private function acfGetJsonKeysFromFilesystem(): array
    {
        $keys = [];

        $base = get_stylesheet_directory() . '/acf-json';
        if ( ! is_dir($base) ) {
            return $keys;
        }

        $files = glob($base . '/**/group_*.json', GLOB_BRACE);
        // Si glob no soporta ** o no encontró nada, usamos fallback recursivo real
        if (!is_array($files) || empty($files)) {
            $files = $this->acfFindJsonFilesRecursive($base);
        }

        foreach ($files as $file) {
            $bn = basename($file);
            // group_xxxxx.json -> group_xxxxx
            if (preg_match('/^(group_[a-zA-Z0-9]+)\.json$/', $bn, $m)) {
                $keys[$m[1]] = true;
                continue;
            }

            // Fallback leyendo contenido (por si se cambia filename)
            $raw = @file_get_contents($file);
            if ($raw) {
                $data = json_decode($raw, true);
                if (is_array($data) && !empty($data['key']) && is_string($data['key'])) {
                    $keys[$data['key']] = true;
                }
            }
        }

        return $keys;
    }

    /**
     * Fallback recursivo si glob ** no funciona.
     */
    private function acfFindJsonFilesRecursive(string $base): array
    {
        $out = [];

        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($it as $fileinfo) {
            /** @var \SplFileInfo $fileinfo */
            if (!$fileinfo->isFile()) continue;

            $name = $fileinfo->getFilename();
            if (preg_match('/^group_[a-zA-Z0-9]+\.json$/', $name)) {
                $out[] = $fileinfo->getPathname();
            }
        }

        return $out;
    }

    /**
     * Devuelve field groups reales en DB (incluye trash).
     * Importante: usamos post_name como key (ACF lo guarda como group_xxx).
     */
    private function acfGetDbFieldGroupsAnyStatus(): array
    {
        $items = [];

        $q = new \WP_Query([
            'post_type'      => 'acf-field-group',
            'post_status'    => 'any',   // incluye trash
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);

        if (!empty($q->posts)) {
            foreach ($q->posts as $post_id) {
                $p = get_post($post_id);
                if (!$p) continue;

                $key = $p->post_name ?: '';
                if (!$key || strpos($key, 'group_') !== 0) {
                    continue;
                }

                $items[] = [
                    'key'      => $key,
                    'title'    => $p->post_title ?: $key,
                    'id'       => (int) $post_id,
                    'status'   => $p->post_status ?: '',
                    'edit_url' => admin_url('post.php?post=' . (int)$post_id . '&action=edit'),
                ];
            }
        }

        return $items;
    }

    /**
     * ===========================
     * TOOL PAGE
     * ===========================
     */
    public function acfJsonSyncToolPage()
    {
        if ( ! current_user_can('manage_options') ) {
            wp_die('No autorizado.');
        }

        $message = '';
        $error   = '';
        $report  = null;

        $has_wpml = $this->hasWpml();
        $langs    = $has_wpml ? $this->wpmlGetLanguages() : [];

        // Orphans siempre visible
        $orphans = $this->acfGetOrphansReport();

        // default mode
        $mode = $has_wpml ? 'default' : 'single';

        // Si hay POST, tomamos mode (si no existe el select, conserva default)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cltvo_acf_sync_nonce'])) {
            $mode = isset($_POST['acf_sync_mode']) ? sanitize_text_field($_POST['acf_sync_mode']) : $mode;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cltvo_acf_sync_nonce'])) {

            if ( ! wp_verify_nonce($_POST['cltvo_acf_sync_nonce'], 'cltvo_acf_sync') ) {
                $error = 'Nonce inválido. Recarga e intenta de nuevo.';
            } elseif ( wp_doing_ajax() || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_doing_cron() ) {
                $error = 'No se puede correr durante AJAX/autosave/cron.';
            } elseif ( ! function_exists('acf_get_field_groups') ) {
                $error = 'ACF no está activo o no se cargó.';
            } else {
                try {
                    if (!$has_wpml) {
                        $report = $this->syncAcfFields();
                        $message = 'Sync terminado. Encontrados: ' . intval($report['found']) .
                            ' | Importados: ' . intval($report['imported']) .
                            ' | Saltados: ' . intval($report['skipped']) . '.';
                    } else {
                        $sitepress    = $GLOBALS['sitepress'];
                        $default_lang = $sitepress->get_default_language();
                        $current_lang = $sitepress->get_current_language();

                        $acc = [
                            'found' => 0, 'imported' => 0, 'skipped' => 0,
                            'imported_keys' => [], 'skipped_items' => [],
                            'by_lang' => [],
                        ];

                        if ($mode === 'all') {
                            $codes = array_keys($langs);
                            if (empty($codes)) $codes = [$default_lang];

                            foreach ($codes as $code) {
                                [$restore_lang, $did_switch] = $this->wpmlSwitchTo($code);
                                $r = $this->syncAcfFields();
                                $this->wpmlRestore($restore_lang, $did_switch);

                                $acc['found']    += (int)($r['found'] ?? 0);
                                $acc['imported'] += (int)($r['imported'] ?? 0);
                                $acc['skipped']  += (int)($r['skipped'] ?? 0);

                                foreach (($r['imported_keys'] ?? []) as $k) {
                                    $acc['imported_keys'][] = $code . ': ' . $k;
                                }
                                foreach (($r['skipped_items'] ?? []) as $it) {
                                    $acc['skipped_items'][] = [
                                        'key' => $code . ': ' . ($it['key'] ?? '-'),
                                        'reason' => $it['reason'] ?? 'Saltado',
                                    ];
                                }

                                $acc['by_lang'][$code] = [
                                    'found'    => (int)($r['found'] ?? 0),
                                    'imported' => (int)($r['imported'] ?? 0),
                                    'skipped'  => (int)($r['skipped'] ?? 0),
                                ];
                            }

                            $report = $acc;

                            $message = 'Sync terminado (Todos los idiomas). Total Encontrados: ' . intval($report['found']) .
                                ' | Importados: ' . intval($report['imported']) .
                                ' | Saltados: ' . intval($report['skipped']) . '.';

                        } elseif ($mode === 'current') {
                            $report = $this->syncAcfFields();

                            $message = 'Sync terminado (Idioma actual: ' . esc_html($current_lang) . '). Encontrados: ' . intval($report['found']) .
                                ' | Importados: ' . intval($report['imported']) .
                                ' | Saltados: ' . intval($report['skipped']) . '.';
                        } else {
                            [$restore_lang, $did_switch] = $this->wpmlSwitchTo($default_lang);
                            $report = $this->syncAcfFields();
                            $this->wpmlRestore($restore_lang, $did_switch);

                            $message = 'Sync terminado (Idioma default: ' . esc_html($default_lang) . '). Encontrados: ' . intval($report['found']) .
                                ' | Importados: ' . intval($report['imported']) .
                                ' | Saltados: ' . intval($report['skipped']) . '.';
                        }
                    }

                    // refrescar huérfanos después del sync (por si cambió algo)
                    $orphans = $this->acfGetOrphansReport();

                } catch (\Throwable $e) {
                    $error = 'Error al ejecutar sync: ' . $e->getMessage();
                }
            }
        }

        // Estado visible
        $base = get_stylesheet_directory() . '/acf-json';
        $dirs = is_dir($base) ? glob($base . '/*', GLOB_ONLYDIR) : [];
        $dir_map = [];
        if (is_array($dirs)) {
            foreach ($dirs as $d) {
                $dir_map[basename($d)] = $d;
            }
            ksort($dir_map);
        }

        ?>
        <div class="wrap">
            <h1>ACF JSON Sync</h1>

            <?php if ($message): ?>
                <div class="notice notice-success"><p><?php echo esc_html($message); ?></p></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="notice notice-error"><p><?php echo esc_html($error); ?></p></div>
            <?php endif; ?>

            <h2>Estado (siempre visible)</h2>
            <table class="widefat striped" style="max-width: 980px;">
                <tbody>
                    <tr>
                        <th style="width:260px;">ACF JSON base</th>
                        <td><code><?php echo esc_html($base); ?></code></td>
                    </tr>
                    <tr>
                        <th>Subcarpetas detectadas</th>
                        <td>
                            <?php if (empty($dir_map)): ?>
                                <em>No se detectaron subcarpetas</em>
                            <?php else: ?>
                                <ul style="margin:0; padding-left:18px;">
                                    <?php foreach ($dir_map as $name => $path): ?>
                                        <li><code><?php echo esc_html($name); ?></code> — <?php echo esc_html($path); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>WPML</th>
                        <td>
                            <?php if ($has_wpml): ?>
                                <strong>Detectado</strong><br>
                                Idiomas activos detectados: <?php echo esc_html(implode('  ', array_keys($langs))); ?>
                            <?php else: ?>
                                <strong>No detectado</strong>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>JSON keys detectadas</th>
                        <td><?php echo intval($orphans['json_keys_count'] ?? 0); ?></td>
                    </tr>
                </tbody>
            </table>

            <?php if (!empty($report) && !empty($report['by_lang']) && is_array($report['by_lang'])): ?>
                <h2 style="margin-top:25px;">Resumen por idioma</h2>
                <table class="widefat striped" style="max-width: 680px;">
                    <thead>
                        <tr>
                            <th>Idioma</th>
                            <th>Encontrados</th>
                            <th>Importados</th>
                            <th>Saltados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['by_lang'] as $code => $row): ?>
                            <tr>
                                <td><code><?php echo esc_html($code); ?></code></td>
                                <td><?php echo intval($row['found'] ?? 0); ?></td>
                                <td><?php echo intval($row['imported'] ?? 0); ?></td>
                                <td><?php echo intval($row['skipped'] ?? 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if (!empty($report) && ( !empty($report['imported_keys']) || !empty($report['skipped_items']) )): ?>
                <h2 style="margin-top:25px;">Detalle</h2>

                <?php if (!empty($report['imported_keys'])): ?>
                    <h3>Importados</h3>
                    <ul>
                        <?php foreach ($report['imported_keys'] as $k): ?>
                            <li><code><?php echo esc_html($k); ?></code></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (!empty($report['skipped_items'])): ?>
                    <h3>Saltados</h3>
                    <ul>
                        <?php foreach ($report['skipped_items'] as $item): ?>
                            <li>
                                <code><?php echo esc_html($item['key']); ?></code> — <?php echo esc_html($item['reason']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>

            <h2 style="margin-top:35px;">Field Groups huérfanos</h2>

            <p>
                Estos grupos existen en la base de datos pero <strong>no tienen archivo correspondiente en <code>acf-json/</code></strong>.
                Este tool <strong>no borra automáticamente</strong> para evitar pérdidas accidentales.
                Si estás 100% segura de que ya no se usan, muévelos a <strong>Papelera</strong> o elimínalos permanentemente desde ACF.
            </p>

            <?php if (empty($orphans['items'])): ?>
                <div class="notice notice-success inline"><p>No se detectaron huérfanos</p></div>
            <?php else: ?>
                <div class="notice notice-warning inline">
                    <p><strong>Detectados:</strong> <?php echo intval($orphans['count']); ?></p>
                </div>

                <table class="widefat striped" style="max-width: 980px;">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Key</th>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orphans['items'] as $it): ?>
                            <tr>
                                <td><?php echo esc_html($it['title']); ?></td>
                                <td><code><?php echo esc_html($it['key']); ?></code></td>
                                <td><?php echo intval($it['id']); ?></td>
                                <td><code><?php echo esc_html($it['status']); ?></code></td>
                                <td>
                                    <a class="button button-small" href="<?php echo esc_url($it['edit_url']); ?>">
                                        Abrir en ACF
                                    </a>
                                    <span style="margin-left:8px; opacity:.8;">
                                        (Revisar → Papelera si procede)
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <h2 style="margin-top:35px;">Sincronizar ACF</h2>
            <p>Ejecuta la sincronización de ACF desde <code>acf-json</code> bajo demanda. Útil después de un pull/deploy.</p>

            <form method="post">
                <?php wp_nonce_field('cltvo_acf_sync', 'cltvo_acf_sync_nonce'); ?>

                <?php if ($has_wpml): ?>
                    <p>
                        <label for="acf_sync_mode"><strong>Modo:</strong></label>
                        <select name="acf_sync_mode" id="acf_sync_mode">
                            <option value="default" <?php selected($mode, 'default'); ?>>
                                Idioma default (recomendado)
                            </option>
                            <option value="current" <?php selected($mode, 'current'); ?>>
                                Idioma actual
                            </option>
                            <option value="all" <?php selected($mode, 'all'); ?>>
                                Todos los idiomas
                            </option>
                        </select>
                    </p>
                <?php endif; ?>

                <p style="margin-top:20px;">
                    <button type="submit" class="button button-primary">
                        Ejecutar Sync ahora
                    </button>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * ===========================
     * DISABLE COMMENTS
     * ===========================
     */
    private function wireDisableComments()
    {
        $support  = get_theme_support('CLTVO_DISABLE_COMMENTS');
        $opts     = is_array($support) && isset($support[0]) ? (array) $support[0] : [];
        $except   = isset($opts['except']) ? (array) $opts['except'] : [];

        add_action('admin_init', function() use ($except) {
            $post_types = get_post_types();
            foreach ($post_types as $pt) {
                if (in_array($pt, $except, true)) continue;
                if (post_type_supports($pt, 'comments')) {
                    remove_post_type_support($pt, 'comments');
                    remove_post_type_support($pt, 'trackbacks');
                }
            }
        });

        add_filter('comments_open', '__return_false', 20);
        add_filter('pings_open',    '__return_false', 20);
        add_filter('comments_array', function($c){ return []; }, 10, 2);

        add_action('admin_menu', function () {
            remove_submenu_page('options-general.php', 'options-discussion.php');
        }, 999);

        add_action('admin_menu', function() {
            remove_menu_page('edit-comments.php');
        }, 999);

        add_action('admin_init', function() {
            global $pagenow;
            if ($pagenow === 'edit-comments.php') {
                wp_safe_redirect(admin_url()); exit;
            }
        });

        add_action('wp_dashboard_setup', function() {
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        });

        add_action('admin_bar_menu', function($wp_admin_bar){
            $wp_admin_bar->remove_node('comments');
        }, 60);

        add_filter('comments_template', function($file){
            return get_stylesheet_directory() . '/empty-comments.php';
        }, 99);
    }
}