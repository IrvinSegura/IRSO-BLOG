# IRSO BLOG

Plataforma de blog hecha con **Laravel 13**, **Jetstream (Livewire)** y **Tailwind CSS**. Tiene un blog público y un panel de administración con permisos por rol. Se puede iniciar sesión con correo o con Google, Microsoft y Facebook, y hay tema claro y oscuro.

![Blog público de IRSO BLOG](docs/screenshots/blog.png)

## Contenido

- [Características](#características)
- [Tecnologías](#tecnologías)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Cuentas de demostración](#cuentas-de-demostración)
- [Roles y permisos](#roles-y-permisos)
- [Módulos por rol](#módulos-por-rol)
- [Configuración opcional](#configuración-opcional)
- [Pruebas](#pruebas)
- [Solución de problemas](#solución-de-problemas)
- [Estructura del proyecto](#estructura-del-proyecto)

## Características

- **Blog público:** historias destacadas (las más comentadas), sección "Lo último" con la publicación más reciente primero y categorías con contador.
- **Panel de administración:** layout con menú lateral que se puede ocultar, búsqueda de usuarios, modales de confirmación, avisos tipo *toast* y paginación en español.
- **Permisos por rol:** Administrador, Editor y Usuario. Cada rol ve solo lo que puede usar, y el servidor también bloquea el acceso.
- **Mis publicaciones:** cada usuario crea, edita y responde los comentarios de sus propias publicaciones.
- **Formulario de publicación:** vista previa en vivo, zona para arrastrar la imagen de portada y validación del peso (máximo 5 MB) antes de enviar.
- **Estadísticas** con gráficas de Chart.js: publicaciones por mes, usuarios por rol y categorías más usadas.
- **Papelera de reciclaje** para restaurar usuarios, categorías y roles eliminados.
- **Inicio de sesión social** con Google, Microsoft y Facebook. Si el proveedor no comparte un correo verificado, se confirma con un código enviado por correo.
- **Tema claro y oscuro** compartido entre el panel y el blog; la preferencia se recuerda.

## Tecnologías

| Capa | Herramientas |
|---|---|
| Backend | PHP 8.3+, Laravel 13, Fortify, Jetstream 5 (Livewire 3), Socialite |
| Frontend | Blade, Tailwind CSS 3, Alpine.js (incluido en Livewire), Chart.js 4, Vite |
| Base de datos | SQLite (recomendada en local) o MySQL |
| Pruebas | PHPUnit |

## Requisitos

- **PHP 8.3 o superior** con las extensiones `pdo_sqlite` (o `pdo_mysql`), `mbstring`, `openssl`, `curl`, `fileinfo` y `gd`
- **Composer 2**
- **Node.js 20 o superior** y npm
- **Git**

## Instalación

> Los comandos sirven en Git Bash, macOS y Linux. Las diferencias para **Windows PowerShell** se indican en cada paso.

### 1. Clonar el repositorio

```bash
git clone https://github.com/IrvinSegura/IRSO-BLOG.git
cd IRSO-BLOG
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Crear el archivo de entorno

```bash
cp .env.example .env
php artisan key:generate
```

En PowerShell usa `Copy-Item .env.example .env` en lugar de `cp`.

Abre `.env` y ajusta, como mínimo:

```dotenv
APP_NAME="IRSO BLOG"
APP_URL=http://localhost:8000   # debe ser la misma dirección con la que abres la app
MAIL_MAILER=log                 # en local, los correos se escriben en storage/logs/laravel.log
```

### 4. Configurar la base de datos

**Opción A: SQLite (lo más rápido para desarrollo).** En `.env` deja así el bloque de base de datos:

```dotenv
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Después crea el archivo vacío de la base:

```bash
touch database/database.sqlite
```

En PowerShell: `New-Item database/database.sqlite -ItemType File`.

**Opción B: MySQL.** Crea una base vacía (por ejemplo `irso_blog`) y pon sus datos en `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

### 5. Crear las tablas y los datos de ejemplo

```bash
php artisan migrate --seed
```

Esto crea los 3 roles, 16 categorías, las [cuentas de demostración](#cuentas-de-demostración) y 6 publicaciones de ejemplo con comentarios. Los seeders se pueden ejecutar varias veces sin duplicar datos.

### 6. Publicar el almacenamiento

Las portadas subidas se guardan en `storage/app/public`; este enlace las hace visibles en la web:

```bash
php artisan storage:link
```

### 7. Compilar los estilos y scripts

```bash
npm run build
```

Mientras desarrollas, usa `npm run dev` para que los cambios se vean al instante.

### 8. Levantar el servidor

```bash
php artisan serve
```

Abre **http://localhost:8000** y entra con alguna de las [cuentas de demostración](#cuentas-de-demostración).

También puedes levantar todo con un solo comando (servidor, logs y Vite):

```bash
composer run dev
```

## Cuentas de demostración

Las crea `php artisan migrate --seed`. Todas usan la contraseña **`12345678`**.

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@irsoblog.test` | `12345678` |
| Editor | `editor@irsoblog.test` | `12345678` |
| Usuario | `usuario@irsoblog.test` | `12345678` |

> Son solo para desarrollo. No ejecutes los seeders en producción o cambia esas contraseñas.

Quien se registra por su cuenta, o entra por primera vez con Google, Microsoft o Facebook, queda con el rol **Usuario** y llega directamente al blog.

## Roles y permisos

| Sección | Usuario | Editor | Administrador |
|---|:-:|:-:|:-:|
| Panel de inicio, blog y mi perfil | ✅ | ✅ | ✅ |
| Mis publicaciones (crear, editar y responder comentarios) | ✅ | ✅ | ✅ |
| Todas las publicaciones, editar las de otros y exportar JSON | ❌ | ✅ | ✅ |
| Categorías y estadísticas | ❌ | ✅ | ✅ |
| Usuarios, usuarios ocultos, roles y papelera | ❌ | ❌ | ✅ |

Los permisos se aplican en el servidor con el middleware `role` y la política `PublicationPolicy`, no solo ocultando enlaces. Quien abra una sección sin permiso ve una página **403**.

## Módulos por rol

### Acceso

| Inicio de sesión | Registro |
|---|---|
| ![Inicio de sesión](docs/screenshots/login.png) | ![Registro](docs/screenshots/registro.png) |

Login y registro con correo, o con Google, Microsoft y Facebook.

### Administrador

**Panel de inicio:** totales del blog, publicaciones recientes, usuarios nuevos y categorías más usadas.

![Panel del administrador](docs/screenshots/admin-panel.png)

| Control de usuarios | Nuevo usuario (selector de rol) |
|---|---|
| ![Control de usuarios](docs/screenshots/admin-usuarios.png) | ![Nuevo usuario](docs/screenshots/admin-nuevo-usuario.png) |

| Roles | Categorías |
|---|---|
| ![Roles](docs/screenshots/admin-roles.png) | ![Categorías](docs/screenshots/admin-categorias.png) |

| Estadísticas | Papelera de reciclaje |
|---|---|
| ![Estadísticas](docs/screenshots/admin-estadisticas.png) | ![Papelera de reciclaje](docs/screenshots/admin-papelera.png) |

**Todas las publicaciones:** con sus comentarios y el botón para editar cualquiera.

![Publicaciones](docs/screenshots/admin-publicaciones.png)

### Editor

Gestiona el contenido de todo el blog (publicaciones, categorías y estadísticas), pero no administra personas.

| Panel del editor | Publicaciones |
|---|---|
| ![Panel del editor](docs/screenshots/editor-panel.png) | ![Publicaciones del editor](docs/screenshots/editor-publicaciones.png) |

Si un editor intenta entrar a una sección de administrador, como `/admin/users`, ve la página 403:

![Página 403](docs/screenshots/editor-403.png)

### Usuario

Solo ve y administra **sus propias publicaciones**. El menú lateral muestra únicamente *Panel*, *Mis publicaciones* y *Crear publicación*.

| Panel del usuario | Mis publicaciones (con comentarios) |
|---|---|
| ![Panel del usuario](docs/screenshots/usuario-panel.png) | ![Mis publicaciones](docs/screenshots/usuario-mis-publicaciones.png) |

| Crear publicación (vista previa en vivo) | Editar publicación |
|---|---|
| ![Crear publicación](docs/screenshots/usuario-crear-publicacion.png) | ![Editar publicación](docs/screenshots/usuario-editar-publicacion.png) |

### Tema oscuro

El interruptor está en el menú de usuario del panel y en el encabezado del blog (**Tema:**). La preferencia se comparte entre los dos y se mantiene hasta que la cambies.

| Panel | Estadísticas |
|---|---|
| ![Panel en tema oscuro](docs/screenshots/admin-panel-oscuro.png) | ![Estadísticas en tema oscuro](docs/screenshots/admin-estadisticas-oscuro.png) |

![Blog en tema oscuro](docs/screenshots/blog-oscuro.png)

## Configuración opcional

### Inicio de sesión con Google, Microsoft y Facebook

Agrega en `.env` las claves de cada proveedor que quieras usar. Los que no tengan claves muestran el aviso *"El acceso con … aún no está configurado"*.

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

MICROSOFT_CLIENT_ID=
MICROSOFT_CLIENT_SECRET=
MICROSOFT_TENANT=common

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
```

Registra en la consola de cada proveedor la URL de retorno, con el mismo dominio y puerto que `APP_URL`:

| Proveedor | Dónde se crea la app | URL de retorno (local) |
|---|---|---|
| Google | Google Cloud Console → Credenciales → ID de cliente de OAuth (Aplicación web) | `http://localhost:8000/auth/google/callback` |
| Microsoft | Microsoft Entra → Registros de aplicaciones (cuentas de cualquier directorio y personales) | `http://localhost:8000/auth/microsoft/callback` |
| Facebook | Meta for Developers → Inicio de sesión con Facebook (permiso `email`) | `http://localhost:8000/auth/facebook/callback` |

- **Microsoft:** copia el **Valor** del secreto, no su Id.
- **Facebook:** en modo desarrollo permite `localhost` sin registrarlo.

### Correo

Con `MAIL_MAILER=log`, los correos (recuperar contraseña o código de verificación) se escriben en `storage/logs/laravel.log`. Para enviarlos de verdad, configura un servidor SMTP en las variables `MAIL_*`.

## Pruebas

```bash
php artisan test
```

La suite cubre la autenticación (incluido el login social), los permisos por rol, el módulo de publicaciones, la redirección a `APP_URL` y los seeders.

## Solución de problemas

| Problema | Solución |
|---|---|
| `Vite manifest not found` | Ejecuta `npm run build` o deja `npm run dev` corriendo. |
| No se ven las portadas subidas | Ejecuta `php artisan storage:link`. |
| La app te manda de `127.0.0.1` a `localhost` (o a otro puerto) | Es intencional: se usa siempre la dirección de `APP_URL`. Cámbiala si usas otro puerto. |
| `cURL error 60: SSL certificate problem` al entrar con Google o Microsoft (Windows) | Tu PHP no tiene certificados raíz. Descarga `cacert.pem` y apunta a él `curl.cainfo` y `openssl.cafile` en `php.ini`; después reinicia el servidor. |
| Laravel ignora tu `.env` | Con `APP_ENV=local`, Laravel carga **`.env.local`** si existe. Revisa ese archivo o bórralo. |
| `El token '&&' no es un separador de instrucciones válido` | Windows PowerShell 5.1 no admite `&&`. Ejecuta los comandos por separado o usa `;`. |

## Estructura del proyecto

No se muestran `vendor/`, `node_modules/` ni `public/build/`, que se generan con `composer install`, `npm install` y `npm run build`.

```text
IRSO-BLOG/
├── app/
│   ├── Actions/
│   │   ├── Fortify/              # Registro, cambio y restablecimiento de contraseña, edición de perfil
│   │   └── Jetstream/            # Eliminación de cuentas
│   ├── Http/
│   │   ├── Controllers/          # Dashboard, Blog, Publication, Comment, Category, Role, User,
│   │   │                         # Statistics, RecycleBin, Socialite (login social) y más
│   │   ├── Middleware/           # EnsureUserHasRole (role:admin,editor) y RedirectToCanonicalHost
│   │   └── Responses/            # RegisterResponse: manda al blog después de registrarse
│   ├── Livewire/                 # Navigation (barra de Jetstream)
│   ├── Models/                   # User, Role, Publication, Category, Comment, Reaction, Tag,
│   │                             # Complaint, SavedPublication, TagPublication, SocialAccount
│   ├── Notifications/            # SocialLoginCode: código para confirmar el correo
│   ├── Policies/                 # PublicationPolicy: quién puede editar una publicación
│   ├── Providers/                # AppServiceProvider, FortifyServiceProvider, JetstreamServiceProvider
│   └── View/Components/          # AppLayout y GuestLayout
├── bootstrap/
│   └── app.php                   # Rutas, middleware (alias "role") y manejo de excepciones
├── config/                       # app, auth, database, fortify, jetstream, mail, services (claves OAuth)…
├── database/
│   ├── factories/                # UserFactory para las pruebas
│   ├── migrations/               # Tablas de usuarios, publicaciones, categorías, roles, comentarios,
│   │                             # reacciones, etiquetas, cuentas sociales y la unión de duplicados
│   └── seeders/                  # Roles, categorías, cuentas demo y publicaciones de ejemplo
├── docs/
│   └── screenshots/              # Imágenes de este README
├── public/
│   ├── img/                      # Iconos de reacciones
│   └── index.php                 # Punto de entrada de la aplicación
├── resources/
│   ├── css/app.css               # Tailwind
│   ├── js/
│   │   ├── app.js
│   │   └── statistics.js         # Gráficas del módulo de estadísticas (Chart.js)
│   ├── markdown/                 # Términos y política de privacidad
│   └── views/
│       ├── admin/                # Módulos del panel
│       │   ├── categories/       # Categorías
│       │   ├── publications/     # Todas, mis publicaciones, crear, editar y formulario compartido
│       │   ├── roles/            # Roles
│       │   ├── users/            # Control de usuarios y usuarios ocultos
│       │   ├── recycle-bin.blade.php
│       │   └── statistics.blade.php
│       ├── api/                  # Tokens de API (Jetstream)
│       ├── auth/                 # Login, registro, recuperar contraseña y confirmación por código
│       ├── blog/                 # Blog público
│       ├── components/
│       │   ├── admin/            # Botones, campos, selector, modales, tablas, iconos, insignias…
│       │   ├── layouts/          # Layouts del panel (admin) y de login y registro (auth)
│       │   ├── theme-script.blade.php
│       │   └── theme-toggle.blade.php
│       ├── emails/               # Plantillas de correo
│       ├── errors/               # Página 403
│       ├── layouts/              # Layouts de Jetstream (app y guest)
│       ├── profile/              # Mi perfil (Jetstream)
│       ├── vendor/pagination/    # Paginación en español
│       └── dashboard.blade.php   # Panel de inicio
├── routes/
│   ├── web.php                   # Blog, panel, login social y rutas protegidas por rol
│   ├── api.php
│   └── console.php
├── storage/                      # Logs, caché, sesiones y archivos subidos (storage/app/public)
├── tests/
│   ├── Feature/                  # Autenticación, login social, roles, publicaciones, seeders…
│   └── Unit/
├── .env.example                  # Variables de entorno de ejemplo (incluye las del login social)
├── artisan
├── composer.json                 # Dependencias de PHP
├── package.json                  # Dependencias de JavaScript
├── phpunit.xml
├── tailwind.config.js            # Tema de Tailwind (modo oscuro por clase)
└── vite.config.js
```

## Licencia

Proyecto de código abierto bajo la licencia [MIT](https://opensource.org/licenses/MIT).
