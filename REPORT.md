# Quality Report

Environment: PHP 8.3, Laravel 13, Shopper 3.x.

All checks below were run on the final `v1.0.0` state of the package.

## Laravel Pint

Command: `vendor/bin/pint`

\`\`\`
![img_3.png](img_3.png)
\`\`\`

## PHPStan (level 9)

Command: `vendor/bin/phpstan analyse`

\`\`\`
![img_1.png](img_1.png)
\`\`\`

## Rector (dry-run)

Command: `vendor/bin/rector process --dry-run`

\`\`\`
![img_2.png](img_2.png)
\`\`\`

## Tests (Pest)

Command: `vendor/bin/pest`

\`\`\`
![img.png](img.png)
\`\`\`