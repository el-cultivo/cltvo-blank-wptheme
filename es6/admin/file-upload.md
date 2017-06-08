# Para usarse con Cltvo_FileUpoload.js

# Ejemplo de uso

```
//micorriza-admin.js
import {
	metaboxUploadInterface, 
	fileUploadConfig//callbacks de la interface
} from './file-upload'

metaboxUploadInterface(fileUploadConfig);//ésta configuración default se puede usar junto con el metabox master Cltvo_FileUpoload.js para subir todo tipo de archivos
```

Alternativamente se puede hacer esto, teniendo el mismo efecto
```
//micorriza-admin.js
import {defaultFileUploadConfig} from '.file-upload'
defaultFileUploadConfig()
```