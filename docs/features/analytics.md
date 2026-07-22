# Analytics

## Descripción

`analytics.php` centraliza todos los scripts de tracking del proyecto (Google Analytics, Meta Pixel, TikTok Pixel, etc.) en un único archivo. Trabaja en conjunto con el módulo de performance: los scripts declarados aquí se ejecutan via Partytown en un web worker, liberando el hilo principal del navegador y mejorando el TBT (Total Blocking Time).

> Para entender cómo se activa y controla Partytown, ver [performance.md](./performance.md).

---

## Cómo agregar un script de tracking

Dentro de `analytics.php`, usar la variable `$type` en lugar de `text/javascript` directamente:

```php
// Correcto
<script type="<?php echo $type; ?>">
  // script de tracking
</script>

// Incorrecto
<script type="text/javascript">
  // script de tracking
</script>
```

`$type` resuelve automáticamente a `text/partytown` o `text/javascript` según el valor de la flag `CLTVO_PARTYTOWN` en `functions.php`. Esto permite activar o desactivar Partytown globalmente sin tocar cada script individualmente.

---

## Objetos globales

Algunas plataformas de tracking exponen objetos globales (`fbq`, `ttq`, `dataLayer`, etc.) que el código del sitio invoca directamente. Como Partytown corre en un web worker separado del hilo principal, estos objetos no están disponibles en `window` por defecto.

Para que el web worker los intercepte correctamente, hay que declararlos en el forward de Partytown en `scripts.php`:

```php
echo 'partytown = { forward: ["dataLayer.push", "fbq", "ttq"] };';
```

Agregar una entrada por cada objeto global que use el proyecto.

---

## Excluir un script de Partytown

Si un script específico no es compatible con Partytown, usar `text/javascript` directamente en ese script. El resto seguirá usando `$type` normalmente:

```php
// Este script queda fuera de Partytown
<script type="text/javascript">
  // script incompatible
</script>

// Este sigue usando Partytown
<script type="<?php echo $type; ?>">
  // otro script de tracking
</script>
```

---

## Limitaciones conocidas

- No todos los scripts de tracking son compatibles con Partytown. Plataformas que dependen de acceso directo al DOM o cookies de primera parte pueden tener comportamiento inesperado en el web worker.
- Si un script falla silenciosamente en producción, verificar primero si está correctamente declarado en el forward de `scripts.php`.