# M贸dulo custom para Drupal 9 Get Marvel 

El m贸dulo permite configurar los dados para conexi贸n con la API de marvel, as铆 como configurar los tipos de entidades que la API permiten, los cuales reprensentan los endpoints dados por la API.

### Pre-requisitos 馃搵

Instalaci贸n estandar de Drupal 9
Drush (Si la instalaci贸n se realiza por este medio)


Para hacer uso del m贸dulo se recomienda descargarlo y agregarlo  en la carpeta de m贸dulos custom:

```
/web/modules/custom
```

### Instalaci贸n 馃敡

Es posible realizar la instalaci贸n por medio drush o por medio de la interfaz de usario con permisos de administrador.

#### Por medio de UI

```
Ingresar a la ruta /admin/modules
Luego buscar el m贸dulo "Get Marvel", seleccionar y dar clic en instalar.
Limpiar cach茅
```

#### Por medio de Drush

```
drush cr
drush en get_marvel
drush cr
```

Una ver instalado se debe crear por defecto un tipo de contenido llamado "Contenido Favorito Marvel" all铆 reposaran los contenidos que los usuario marquen como favoritos, dicho tipo tambi茅n debe crear los campo que usar谩n. (/node/add/marvel_content)

### Configuraci贸n 馃敥

El m贸dulo cuenta con dos secciones de configuraci贸n una con los datos base para la conexi贸n con la API y la segunda se configura los tipos de entidades que se presentaran los cuales establecer los endpoints habilitados. 

* [API Marvel](https://developer.marvel.com/documentation/generalinfo) - Estructura de datos API Marvel
* [Ejemplo API](https://developer.marvel.com/docs) - Documentaci贸n interactiva
* [Obtener Keys](https://developer.marvel.com/account) - Documentaci贸n interactiva



####Configuraci贸n se debe acceder a la interfaz de la adminitraci贸n.

```
/admin/config/services/get-marvel/data
```

Las llaves se pueden obener en el link referenciado en "Obtener Keys".

####Congiguraci贸n de entidades (Endpoints)

```
/admin/config/services/get-marvel/endpoints
```

Se puede agregar las entidades que se manejan la API, agregando el hombre de entidad (Completa los endpoints seg煤n entidades) y la etiqueta (Botones para llamado de endpoint).

Se recomienda borrar cach茅 cada vez que se ajusten las configuraciones.

## Ejecutando las pruebas 鈿欙笍

Para validar los contenidos se accede a:

```
/marvel-content
```


Una vez en esta ruta se peude validar los endpoints configurados, se cargaran 30 contenidos de la entidad que se haya configurado en primera posici贸n. En la parte superior se mostrar谩n los botones para obtener las diferentes entidades(endpoints) configurados.

Para cargar m谩s contenidos, se usa el bot贸n "Ver M谩s" en la parte de abajo.


### Agregar a Favorito 鈱笍

Cada contenido tiene un bot贸n "+ Favoritos", si el usuario est谩 logueado permitir谩 crear uno con el contenidos agregado a favorito, la relaci贸n con el usuario que crear el contenidos permite establecer cuales de los contenidos creados son sus favoritos.
