# Storing files as BLOBs with Doctrine ORM

PHP applications usually store uploaded files in the server's file system and persist
only the file paths in a database.
Obtaining a consistent backup of such a dataset may be difficult.

This sample project uses Doctrine ORM to store uploaded files as BLOBs in a database.
After all, MySQL supports 4GB BLOBs and PostgreSQL even 4TB BLOBs.

In Doctrine ORM, however, `msqli`, `pdo_mysql` and `pdo_pgsql` do not stream into/from a BLOB
but materialize the BLOB in memory in its entirety.

Therefore, the file size for BLOB storage is limited by the PHP `memory_limit` (128MB by default),
and for MySQL also by the `max_allowed_packet` parameter. In order to stay within these
limits, the PHP `upload_max_filesize` should be set accordingly.

Despite these drawbacks, using BLOBs can be advantageous since this keeps the complete dataset
in a single place (the database);
this provides for consistent backups and synchronization in a database cluster.

Within this project, a few additional techniques are mentioned that might be useful. 

### Subjects

- Uploading files and storing them as
  [BLOBs](https://en.wikipedia.org/wiki/Binary_large_object) with [Doctrine ORM](https://www.doctrine-project.org/)
- Serving and downloading uploaded files
- Hiding primary keys from clients and identifying file entities by
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