# Probo Example: Joomla

A **Joomla 6** site built by Probo's **LAMP + Joomla** plugin from a database
dump. The full Joomla codebase lives in the [`docroot/`](docroot/) subdirectory
of the repository, and the `.probo.yaml` file at the repository root tells
Probo.CI how to stand the site up in a build container.

Fork this repository to easily get a Joomla sandbox site running in Probo.CI.

## Example Database

The example database for this example can be downloaded
[here](https://probosupportfiles.blob.core.windows.net/utils/joomla.sql.gz) —
be sure to place the `joomla.sql.gz` file in your assets if you are trying to
build it on your own Probo.CI account.

## How it runs in a Probo Joomla container

`.probo.yaml` uses `type: lamp` with `php: 8.4` and the built-in **Joomla**
plugin, which installs the site from a database dump:

```yaml
type: lamp
php: 8.4
database: mariadb:11.4

# The sample database can be obtained at:
# https://probosupportfiles.blob.core.windows.net/utils/joomla.sql.gz
assets:
  - joomla.sql.gz

steps:
  - name: Setup Joomla
    plugin: Joomla
    database: joomla.sql.gz
    databaseGzipped: true
    clearCaches: false
    subDirectory: docroot
```

The Joomla plugin imports the gzipped database into the Probo-provisioned
MariaDB instance, points Apache/PHP at the `docroot/` web root, and writes the
database connection details into `configuration.php`; nginx reverse-proxies
`*.probo.build` to the container.

### How `configuration.php` is handled

Joomla stores its settings as a `JConfig` class in a `configuration.php` file
at the web root. This repository intentionally does **not** commit one (it is
listed in `.gitignore`), so the plugin generates a fresh, minimal
`configuration.php` on every build using the Probo-provisioned database
credentials, a randomly generated `$secret`, and the default `jos_` table
prefix — which matches the prefix used by the example database above.

If your own repository already ships a `configuration.php`, the plugin instead
preserves it and patches only the database connection properties Probo
controls (`$dbtype`, `$host`, `$user`, `$password`, and `$db`), leaving your
`$secret` and `$dbprefix` untouched so the site keeps matching the database
you import.

The Joomla plugin also inherits all Probo PHP/LAMP configuration options, so
additional build steps can layer LAMP commands on top of the Joomla-specific
setup. Other useful plugin options include `databaseName`, `databasePrefix`,
`dbType`, and `clearCaches` (which runs `php cli/joomla.php cache:clean` after
the build). See the Probo.CI Joomla plugin documentation for the full list.

