# Módulo custom para Drupal 9 Get Marvel 

El módulo permite configurar los dados para conexión con la API de marvel, así como configurar los tipos de entidades que la API permiten, los cuales reprensentan los endpoints dados por la API.

### Pre-requisitos 📋

Instalación estandar de Drupal 9
Drush (Si la instalación se realiza por este medio)


Para hacer uso del módulo se recomienda descargarlo y agregarlo  en la carpeta de módulos custom:

```
/web/modules/custom
```

### Instalación 🔧

Es posible realizar la instalación por medio drush o por medio de la interfaz de usario con permisos de administrador.

#### Por medio de UI

```
Ingresar a la ruta /admin/modules
Luego buscar el módulo "Get Marvel", seleccionar y dar clic en instalar.
Limpiar caché
```

#### Por medio de Drush

```
drush cr
drush en get_marvel
drush cr
```

Una ver instalado se debe crear por defecto un tipo de contenido llamado "Contenido Favorito Marvel" allí reposaran los contenidos que los usuario marquen como favoritos, dicho tipo también debe crear los campo que usarán. (/node/add/marvel_content)

### Configuración 🔩

El módulo cuenta con dos secciones de configuración una con los datos base para la conexión con la API y la segunda se configura los tipos de entidades que se presentaran los cuales establecer los endpoints habilitados. 

* [API Marvel](https://developer.marvel.com/documentation/generalinfo) - Estructura de datos API Marvel
* [Ejemplo API](https://developer.marvel.com/docs) - Documentación interactiva
* [Obtener Keys](https://developer.marvel.com/account) - Documentación interactiva



####Configuración se debe acceder a la interfaz de la adminitración.

```
/admin/config/services/get-marvel/data
```

Las llaves se pueden obener en el link referenciado en "Obtener Keys".

####Congiguración de entidades (Endpoints)

```
/admin/config/services/get-marvel/endpoints
```

Se puede agregar las entidades que se manejan la API, agregando el hombre de entidad (Completa los endpoints según entidades) y la etiqueta (Botones para llamado de endpoint).

Se recomienda borrar caché cada vez que se ajusten las configuraciones.

## Ejecutando las pruebas ⚙️

Para validar los contenidos se accede a:

```
/marvel-content
```


Una vez en esta ruta se peude validar los endpoints configurados, se cargaran 30 contenidos de la entidad que se haya configurado en primera posición. En la parte superior se mostrarán los botones para obtener las diferentes entidades(endpoints) configurados.

Para cargar más contenidos, se usa el botón "Ver Más" en la parte de abajo.


### Agregar a Favorito ⌨️

Cada contenido tiene un botón "+ Favoritos", si el usuario está logueado permitirá crear uno con el contenidos agregado a favorito, la relación con el usuario que crear el contenidos permite establecer cuales de los contenidos creados son sus favoritos.
