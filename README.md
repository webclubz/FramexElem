# FramexElem Module

**FramexElem** is a lightweight ProcessWire module for composing pages from **reusable components** in `site/templates/components`.

It supports:

* Components with parameters
* A **default slot** via `@slot`
* **Named slots** via `@slot:name` and `$elem->slot('name', fn(){ ... })`
* A clean one-liner via `FramexElem::widget(...)`

> **Requirements:** PHP ≥ 7.4, ProcessWire 3.x

---

## Installation

1. Copy the module to: `site/modules/FramexElem/`
2. In **Admin → Modules**, click **Refresh** and **Install** “Framex Elements”.

---

## Template header (import)

At the top of your template file, add:

```php
<?php
namespace ProcessWire;              // recommended in PW templates
use ProcessWire\FramexElem;         // now you can write: new FramexElem(...)
```

> If your template does **not** declare a namespace, keep only:
>
> ```php
> <?php
> use ProcessWire\FramexElem;
> ```

---

## Component lookup & naming

Components live under:

```
/site/templates/components/
```

You can reference a component by:

* Single file: `components/card.php`
* Folder with index: `components/card/index.php`

Both are loaded via:

```php
new FramexElem('card');
```

Nested components are supported with `/` **or** `.`:

```php
new FramexElem('ui/card');   // → components/ui/card.php or components/ui/card/index.php
new FramexElem('ui.card');
```

---

## Quick start

### 1) Simple component (no parameters)

```php
<?php
namespace ProcessWire;
use ProcessWire\FramexElem;

$elem = new FramexElem('simple');
$elem->close();
```

**`/site/templates/components/simple.php`**

```php
<div class="simple">Hello from simple component</div>
```

---

### 2) Component with parameters

```php
<?php
namespace ProcessWire;
use ProcessWire\FramexElem;

$elem = new FramexElem('welcome', [
  'title' => 'Welcome',
  'message' => 'Hello, world!'
]);
$elem->close();
```

**`/site/templates/components/welcome.php`**

```php
<section class="welcome">
  <h2><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>
  <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
</section>
```

---

### 3) Default slot (`@slot`)

Everything printed between constructing the element and calling `->close()` becomes the **default slot** and replaces `@slot` in the component.

```php
<?php
namespace ProcessWire;
use ProcessWire\FramexElem;

$welcome = new FramexElem('welcomeBox', ['name' => 'John Doe']); ?>
  <p>We're glad to have you here. Explore our content and enjoy your stay!</p>
<?php $welcome->close(); ?>
```

**`/site/templates/components/welcomeBox.php`**

```php
<div class="welcome">
  <h1>Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h1>
  @slot
</div>
```

---

### 4) Named slots (`@slot:name`)

Provide additional regions inside your component. If a named slot is not set, its placeholder renders as an empty string.

```php
<?php
namespace ProcessWire;
use ProcessWire\FramexElem;

$card = new FramexElem('card', ['title' => 'Hello']); ?>

  <p>Default body content…</p>

  <?php $card->slot('actions', function () { ?>
    <a href="/contact" class="btn">Contact</a>
  <?php }); ?>

  <?php $card->slot('footer', function () { ?>
    <small>© <?= date('Y') ?></small>
  <?php }); ?>

<?php $card->close(); ?>
```

**`/site/templates/components/card.php`**

```php
<div class="card">
  <div class="card-header">
    <h3><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
  </div>
  <div class="card-body">@slot</div>
  <div class="card-actions">@slot:actions</div>
  <div class="card-footer">@slot:footer</div>
</div>
```

> **Naming rules:** `@slot:name` accepts letters, digits, `_`, `-` (regex: `[a-zA-Z0-9_\-]+`).

---

### 5) One-liner with `widget()`

```php
<?php
namespace ProcessWire;
use ProcessWire\FramexElem;

echo FramexElem::widget('badge', ['text' => 'New'])->close();
```

Inline named slot:

```php
<?php
namespace ProcessWire;
use ProcessWire\FramexElem;

echo FramexElem::widget('panel', ['title' => 'Info'])
  ->slot('footer', function () { ?><em>Details…</em><?php })
  ->close();
```

---

## Managing parameters

```php
<?php
namespace ProcessWire;
use ProcessWire\FramexElem;

$elem = new FramexElem('dynamic');

// Set parameters
$elem->setParameter('greeting', 'Hello');
$elem->setParameter('name', 'Jane');

// Read a parameter
echo $elem->getParameter('greeting'); // Hello

// Remove a parameter
$elem->removeParameter('greeting');

// Get all parameters
$all = $elem->getParameters();

$elem->close();
```

---

## Authoring components: tips

* **Escape output**: Use `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` (or PW’s `$sanitizer`) for untrusted content.
* **Optional slots**: It’s fine to include `@slot` / `@slot:name` even if not provided; they render empty.
* **Keep logic light**: Prefer passing data via parameters; keep components focused on markup.

---

## Troubleshooting

* **Literal `:actions` / `:footer` appears**
  Ensure you’re on **0.0.2+** where named slots are replaced **before** the default slot. Confirm your placeholders match the slot names exactly.

* **Duplicate output**
  Call `->close()` only once per instance. This module intentionally **echoes** on `close()` to keep templates clean.

---

## API Reference

* `__construct(string $component, array $parameters = [])`
  Create a component instance; starts capturing the default slot.

* `close(): void`
  Renders and **echoes** the component. Replaces **named slots first** (`@slot:name`), then the **default slot** (`@slot`).

* `static widget(string $component, array $parameters = []): self`
  Shorthand to create an instance for in-place rendering.

* `slot(string $name, callable $producer): self`
  Capture content for a named slot. Anything echoed inside the closure becomes the slot content.

* `setParameter(string $key, $value): void`
  Set a parameter.

* `getParameter(string $key)`
  Get a parameter (or `null` if missing).

* `removeParameter(string $key): void`
  Remove a parameter.

* `getParameters(): array`
  Return all parameters.

---

## License

MIT


## ⚠️ Disclaimer

This is a personal project created for learning and convenience in local development environments. The scripts are provided as-is without any guarantees or warranties. Use them at your OWN RISK. I am not responsible for any data loss, misconfiguration, or damage that may result from using these scripts on your system. Always review and adapt the code to your specific needs before running it on production or critical environments.
