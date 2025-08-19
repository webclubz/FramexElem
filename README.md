Here’s an updated **README.md** in English, with clear usage notes and examples (including **named slots**).

---

# FramexElem Module

**FramexElem** is a lightweight ProcessWire module that lets you compose pages from **reusable components** stored in `site/templates/components`.
It supports:

* Components with parameters
* A **default slot** via `@slot`
* **Named slots** via `@slot:name` and `$elem->slot('name', fn(){ ... })`
* A clean one-liner via `FramexElem::widget(...)`

> **Requirements:** PHP ≥ 7.4 (typed properties), ProcessWire 3.x

---

## Installation

1. Copy the module folder to: `site/modules/FramexElem/`
2. In **Admin → Modules**, click **Refresh**, then **Install** “Framex Elements”.

---

## Component lookup & naming

Components live under:

```
/site/templates/components/
```

You can reference a component by:

* A single file: `components/card.php`
* A folder with `index.php`: `components/card/index.php`

Both are loaded with:

```php
new \ProcessWire\FramexElem('card');
```

Nested components are supported using `/` **or** `.`:

```php
new \ProcessWire\FramexElem('ui/card');
new \ProcessWire\FramexElem('ui.card');
```

---

## Quick start

### 1) Simple component (no parameters)

```php
<?php
$elem = new \ProcessWire\FramexElem('simple');
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
$elem = new \ProcessWire\FramexElem('welcome', [
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

Everything you print between constructing the element and calling `->close()` becomes the **default slot** and replaces `@slot` in the component.

```php
<?php $welcome = new \ProcessWire\FramexElem('welcomeBox', ['name' => 'John Doe']); ?>
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

Provide additional regions inside your component. If a named slot is not set, its placeholder becomes an empty string.

```php
<?php $card = new \ProcessWire\FramexElem('card', ['title' => 'Hello']); ?>

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
<?= \ProcessWire\FramexElem::widget('badge', ['text' => 'New'])->close(); ?>
```

Inline named slot:

```php
<?= \ProcessWire\FramexElem::widget('panel', ['title' => 'Info'])
      ->slot('footer', function () { ?><em>Details…</em><?php })
      ->close(); ?>
```

---

## Managing parameters

```php
<?php
$elem = new \ProcessWire\FramexElem('dynamic');

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

* **Escape output**: Use `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` for any untrusted content.
* **Optional slots**: It’s safe to include `@slot` / `@slot:name` even if they aren’t provided; they’ll render empty.
* **Structure**: Keep components small and focused; prefer passing data via parameters instead of doing heavy logic inside them.

---

## Troubleshooting

* **Seeing `:actions` or `:footer` printed**
  Ensure you’re on version **0.0.2+** (named slots are replaced **before** the default slot). Also check for typos in placeholders (`@slot:actions` must match `slot('actions', ...)`).

* **Duplicate output**
  Call `->close()` only once per instance. If you use a “delayed output” strategy with a global layout, keep using the pattern that works for your site (this module echoes on `close()` by design).

---

## API Reference

* `__construct(string $component, array $parameters = [])`
  Create a component instance; starts capturing the default slot.

* `close(): void`
  Renders and **echoes** the component. Replaces **named slots first** (`@slot:name`), then the **default slot** (`@slot`).

* `static widget(string $component, array $parameters = []): self`
  Shorthand to create an instance for in-place rendering.

* `slot(string $name, callable $producer): self`
  Capture content for a named slot. Whatever you echo inside the closure becomes the slot content.

* `setParameter(string $key, $value): void`
  Set a single parameter.

* `getParameter(string $key)`
  Get a single parameter (or `null` if missing).

* `removeParameter(string $key): void`
  Remove a parameter.

* `getParameters(): array`
  Return all parameters.

---

## License

MIT

---

## ⚠️ Disclaimer

This is a personal project created for learning and convenience in local development environments. The scripts are provided as-is without any guarantees or warranties. Use them at your OWN RISK. I am not responsible for any data loss, misconfiguration, or damage that may result from using these scripts on your system. Always review and adapt the code to your specific needs before running it on production or critical environments.
