# Doctrine CLI cheatsheet

## Create database tables from entities
`vendor/bin/doctrine orm:schema-tool:create`

## Recreate all database tables (data is lost)
```bash
vendor/bin/doctrine orm:schema-tool:drop --force
vendor/bin/doctrine orm:schema-tool:create
```

## Update database tables from entities
`vendor/bin/doctrine orm:schema-tool:update --force`

# PHPUnit cheatsheet

## Run all tests with TestDox
`vendor/bin/phpunit --testdox tests`
