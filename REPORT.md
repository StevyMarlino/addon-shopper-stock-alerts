# Quality Report

Environment: PHP 8.3, Laravel 13, Shopper 3.x.

All checks below were run on the final `v1.0.0` state of the package.

## Laravel Pint

Command: 
```bash
    vendor/bin/pint --test
```

\`\`\`
![img_1.png](img_1.png)
\`\`\`

## PHPStan (level 9)

Command: 
```bash
  vendor/bin/phpstan analyse
```

\`\`\`
![img.png](img.png)
\`\`\`

## Rector (dry-run)

Command: 
```bash
  vendor/bin/rector process --dry-run
```

\`\`\`
![img_4.png](img_4.png)
\`\`\`

## Tests (Pest)

Command: 
```bash
  vendor/bin/pest
```

\`\`\`
![img_5.png](img_5.png)
\`\`\`