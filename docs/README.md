# Documentación del Blank Theme

Esta carpeta documenta la arquitectura y comportamiento del boilerplate de WordPress de El Cultivo.

## Empezar aquí

- [Arquitectura](./architecture.md)
- [Estructura del tema](./theme-structure.md)
- [Changelog](./CHANGELOG.md)

## Core

- [Bootstrap](./core/bootstrap.md)
- [Framework](./core/framework.md)
- [Configuración](./core/config.md)
- [App](./core/app.md)
- [Helpers](./core/helpers.md)
- [Providers](./core/providers.md)
- [Templates y Views](./core/templates-and-views.md)

Los providers individuales viven en `core/providers/`.

## Integraciones

- [Advanced Custom Fields](./integrations/acf.md)
- [Mailgun y sistema de correo](./integrations/mailgun.md)

## Frontend

- [Build](./frontend/build.md)
- [JavaScript / ES6](./frontend/javascript.md)
- [Mazorca / Sass](./frontend/mazorca.md)

## Features

`features/` contiene documentación de funcionalidades transversales o incorporadas en versiones específicas del boilerplate.

Estas features deben verificarse contra la versión concreta del theme antes de asumir que forman parte del runtime de un proyecto existente.

## Criterio

La documentación distingue entre comportamiento actual, ejemplos de boilerplate, legacy y recomendaciones de refactor.

Una propuesta de mejora no debe documentarse como comportamiento vigente hasta que el cambio exista en código.
