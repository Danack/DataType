# Container-Based Development Instructions

## Important: All Commands Must Run Inside Containers

This project uses Docker containers for development. **All commands, scripts, and tools must be executed inside the appropriate container**, not on the host machine.

## Running Commands Inside Containers

### PHP Commands (php_fpm container)

All PHP-related commands (tests, Behat, PHPUnit, Composer, etc.) must run inside the `php_fpm` container:

```bash
# Run PHP unit tests
docker exec datatype-developing_8_2-1 bash -c "sh runUnitTests.sh"

# Run Behat browser tests
docker exec datatype-developing_8_2-1 bash -c "sh runBehat.sh"

# Run PHPStan
docker exec datatype-developing_8_2-1 bash -c "sh runPhpStan.sh"

# Run Composer commands
docker exec datatype-developing_8_2-1 bash -c "composer install"

# Run PHP CLI commands
docker exec datatype-developing_8_2-1 bash -c "php cli.php <command>"

# Run mutation testing
docker exec datatype-developing_8_2-1 bash -c "sh runMutationTests.sh"
```


### Finding Uncovered Lines of Code

To identify which lines of code need test coverage, first run the unit tests to generate a coverage report:

```bash
docker exec datatype-developing_8_2-1 bash -c "sh runUnitTests.sh --no-progress"
```

Then use the `list_uncovered_lines.php` script to find uncovered lines. You can filter by namespace or directory:

```bash
# Find all uncovered lines in a specific namespace
docker exec datatype-developing_8_2-1 bash -c "php list_uncovered_lines.php clover.xml | grep DataType"

# Find all uncovered lines in a specific directory
docker exec datatype-developing_8_2-1 bash -c "php list_uncovered_lines.php clover.xml | grep DataType/Create"

# Count uncovered lines for a namespace
docker exec datatype-developing_8_2-1 bash -c "php list_uncovered_lines.php clover.xml | grep DataType | wc -l"
```

**Note**: Use `bash -c` without `-it` flags to avoid TTY errors when running non-interactive commands.

### Interactive Shell Access

To get an interactive shell inside a container:

```bash
# PHP container
docker exec -it datatype-developing_8_2-1 bash

# Then run commands directly:
sh runUnitTests.sh
sh runBehat.sh
composer install
```

### Node.js/JavaScript Commands (js_builder container)

All Node.js, npm, and JavaScript-related commands must run inside the `js_builder` container:

```bash
# Run Jest tests
docker exec datatype-developing_8_2-1 bash -c "npm run test"

# Run npm commands
docker exec datatype-developing_8_2-1 bash -c "npm install"
docker exec datatype-developing_8_2-1 bash -c "npm run build"

# Check TypeScript compilation logs after editing TypeScript files
docker logs datatype-developing_8_2-1 --tail 100
```

### Script Files

Script files in the project root (e.g., `runBehat.sh`, `runUnitTests.sh`) are designed to be executed **inside** the container, not on the host. They contain the actual commands without docker-compose exec calls.


## Key Points

1. **Never run PHP/Composer commands on the host** - they must run inside `php_fpm` container
2. **Never run npm/node commands on the host** - they must run inside `js_builder` container
3. **Script files don't call docker-compose exec** - they're meant to be executed inside containers
4. **Use `docker exec` from the host** to run commands inside containers
5. **Use `bash -c` for non-interactive commands** to avoid TTY issues

