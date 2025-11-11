## Question:

I have this application startup:

#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use App\Command\{
    DebianCommand,
    PhpFullDevCommand,
    PythonCommand,
    NodeCommand,
    NginxCommand,
    ApacheCommand,
    DotNetCommand,
    PhpCommand,
    PostgreCommand,
    MySQLCommand
};
use App\Command\Dev\AddNew;
use Symfony\Component\Filesystem\Filesystem;

require_once './vendor/autoload.php';

$fileSystem = new Filesystem();

$application = new Application();
$application->add(new PhpFullDevCommand($fileSystem));
$application->add(new DebianCommand($fileSystem));
$application->add(new PythonCommand($fileSystem));
$application->add(new NodeCommand($fileSystem));
$application->add(new NginxCommand($fileSystem));
$application->add(new ApacheCommand($fileSystem));
$application->add(new DotNetCommand($fileSystem));
$application->add(new PhpCommand($fileSystem));
$application->add(new PostgreCommand($fileSystem));
$application->add(new MySQLCommand($fileSystem));
$application->add(new AddNew());

$application->run();

Considering that I am using Symfony, is it possible to have a more elegant code? Needing to add the same concrete filesystem implementation bothers a little.

## Answer

Yes — Symfony Console apps can be made cleaner and more “Symfony-ish” by leveraging the container and service auto-registration instead of manually instantiating every command and injecting the same dependencies by hand.

### ✅ A more elegant Symfony approach

If your project is already using Symfony components with the `FrameworkBundle` or `DependencyInjection` component, the cleanest way is:

1. **Register commands as services**
2. **Let Symfony autowire the `Filesystem`**
3. **Auto-discover commands instead of adding them manually**

---

## ✅ 1. Create a real `bin/console` style entrypoint

Instead of manually building an `Application`, use Symfony’s container-driven console:

`bin/console`:

```php
#!/usr/bin/env php
<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require __DIR__.'/../vendor/autoload.php';

$kernel = new Kernel('dev', true);
$application = new Application($kernel);
$application->run();
```

Now Symfony handles dependency injection and auto-regisers Commands.

---

## ✅ 2. Register commands as services (no manual `$application->add()`)

In `config/services.yaml`:

```yaml
services:
    App\Command\:
        resource: '../src/Command/*'
        tags: ['console.command']
        autowire: true
        autoconfigure: true
```

* All commands under `src/Command`
* Automatically discovered
* Automatically registered
* Dependencies injected

---

## ✅ 3. Inject Filesystem automatically

Since you want to avoid repeating `new Filesystem()` in every command:

In a command:

```php
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;

class PhpCommand extends Command
{
    public function __construct(private Filesystem $filesystem)
    {
        parent::__construct();
    }

    // ...
}
```

No need to pass it manually. Symfony autowires it.

---

### ✅ Result: your startup file is now trivial

No manual `add()`, no manual instantiation, no shared `$filesystem` injection.

```php
#!/usr/bin/env php
<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require __DIR__.'/vendor/autoload.php';

$kernel = new Kernel('dev', true);
(new Application($kernel))->run();
```

---

## ✅ If you *aren’t* using full Symfony kernel

If this is a standalone console app (no framework, no Kernel), you can still avoid repeating `new Filesystem` with a minimal DI container:

### Option A: Register all commands in an array

```php
$filesystem = new Filesystem();

$commands = [
    new PhpFullDevCommand($filesystem),
    new DebianCommand($filesystem),
    // ...
];

foreach ($commands as $command) {
    $application->add($command);
}
```

### Option B: Simple command discovery

Put your commands in `src/Command`, then:

```php
$filesystem = new Filesystem();
$commandNamespace = 'App\\Command\\';
$commandDir = __DIR__.'/src/Command';

foreach (scandir($commandDir) as $file) {
    if (!str_ends_with($file, 'php')) continue;

    $className = $commandNamespace . pathinfo($file, PATHINFO_FILENAME);
    if (class_exists($className)) {
        $app->add(new $className($filesystem));
    }
}
```

Still simpler than adding each one manually.

---

## ✅ Best practice (recommended)

If this is a real application and not a script prototype:

✔ create a Kernel
✔ auto-discover commands
✔ autowire dependencies
✔ let Symfony inject `Filesystem`

This eliminates boilerplate **and** keeps your code aligned with Symfony conventions.

---

### If you'd like, I can show what your `Kernel` class and folder structure should look like, or convert your project step-by-step to a clean Symfony Console app.



