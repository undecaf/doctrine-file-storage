# Storing files as BLOBs with Doctrine ORM

PHP applications usually store uploaded files in the server's file system and persist only file paths in a database.
Obtaining a consistent backup of such a dataset may be difficult.

This sample project uses Doctrine ORM to store uploaded files as BLOBs in a database. This approach may be advantageous whenever file
sizes are below the maximum BLOB capacity (currently 4GB for MySQL and MariaDB, 4TB for PostgreSQL): it keeps the complete
dataset in a single place (the database) and thus can guarantee consistent backups and synchronization in a database cluster.

Along the way, a few additional techniques are shown that might be useful. 

### Subjects

- Uploading files and storing them as
  [BLOBs](https://en.wikipedia.org/wiki/Binary_large_object) with [Doctrine ORM](https://www.doctrine-project.org/)
- Serving and downloading uploaded files
- Hiding primary keys from the clients and identifying file entities by
  [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier)
- Using a
  [mapped superclass](https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/inheritance-mapping.html#mapped-superclasses)
  as base class for entity classes
- Applying
  [collection per class inheritance](https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/inheritance-mapping.html#collection-per-class-inheritance)
  to derive specialized file entities (such as image entities)
- Generating image thumbnails with the
  [Imagick PHP extension](https://www.php.net/manual/en/class.imagick.php)
- Rendering a UI with the [Fluid Template Engine](https://typo3.org/fluid)