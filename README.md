# ubermuda/admin-bundle

A reusable admin shell (layout + sidebar nav registry) and paginated / filterable / sortable listing framework for the Symfony apps we own. It ships neutral `admin-*` CSS classes and namespaced Twig components so any consuming app can mount a consistent `/admin` area without re-implementing the plumbing.

## Installation

The bundle is distributed over Git, not Packagist. Add it as a VCS repository in the consuming app's `composer.json`:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/ubermuda/admin-bundle" }
    ]
}
```

Then require it (it tracks `main` as a dev branch):

```bash
composer require ubermuda/admin-bundle:@dev
```

Register the bundle in `config/bundles.php` (Symfony Flex does not auto-register VCS bundles):

```php
return [
    // ...
    Ubermuda\AdminBundle\UbermudaAdminBundle::class => ['all' => true],
];
```

## Configuration

Configure the `ubermuda_admin` node — e.g. `config/packages/ubermuda_admin.yaml`:

```yaml
ubermuda_admin:
    # Text shown as the brand logotype in the admin sidebar.
    brand_label: 'Make Plans'
    # Route the sidebar's "Back to app" link points at.
    app_route: 'app_dashboard'
    # importmap() entry rendered in the admin layout <head>.
    importmap_entry: 'app'
    # data-theme attribute on <html> (e.g. a DaisyUI theme). Omitted when null.
    theme: 'makeplans'
    # Extra class(es) appended to <body>, e.g. an app font class.
    body_class: 'font-jakarta'
```

All keys have defaults (`Admin`, `app_dashboard`, `app`, `null`, `''`); override the ones that differ.

### App fonts

The layout renders no font `<link>`s of its own. To load app fonts in the admin, override the `head_fonts` block from your app — e.g. `templates/bundles/UbermudaAdminBundle/base.html.twig`:

```twig
{% extends '!@UbermudaAdmin/base.html.twig' %}

{% block head_fonts %}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
{% endblock %}
```

Pair it with `body_class` (and your Tailwind font utility) so the loaded font is actually applied.

## Tailwind / CSS wiring

The bundle ships its structural CSS as a Tailwind v4 source partial at `assets/admin.css`. The consuming app compiles it in its own Tailwind pass — the bundle does **not** ship compiled CSS. Three steps:

### 1. Register the bundle's templates as a Tailwind `@source`

The bundle's Twig templates emit utility classes (icons, spacing, colours) that Tailwind must see to compile. Point Tailwind at the installed bundle's `templates/` dir from your app's Tailwind entry (e.g. `assets/styles/app.css`):

```css
@source "../../vendor/ubermuda/admin-bundle/templates";
```

(Adjust the relative path to your Tailwind entry's location.)

### 2. `@import` the structural partial

```css
@import "../../vendor/ubermuda/admin-bundle/assets/admin.css";
```

This defines the structural classes the base template and listing components need: `admin-page`, `admin-form`, `admin-edit-form`, `admin-edit-sidebar`, `admin-table` (+ `thead`/`tbody` rules), `admin-sidebar`, `admin-nav-link` (+ `.is-active`), `admin-brand`, and `admin-alert` + the `admin-alert-success` / `-error` / `-warning` / `-info` severity variants.

The sidebar background is themeable — override the CSS variable (or the whole rule) to brand it:

```css
.admin-sidebar {
    background: var(--admin-sidebar-background, theme(colors.slate.900));
}
```

Set `--admin-sidebar-background` (e.g. to your brand gradient) in your app's CSS to reskin the sidebar without touching the bundle.

### 3. Provide the primitive classes

The bundle's templates also reference a set of **primitive** design-system classes that it deliberately does **not** ship — they carry your app's look-and-feel (button colours, card radius, badge palette). Your app must define them.

> **Tailwind v4 note:** `@apply admin-button` does **not** work — Tailwind v4 only lets you `@apply` real utility classes, not custom component classes. The clean way to supply a primitive is to add the neutral `admin-*` name as a **grouped selector** on your app's existing design-system rule:
>
> ```css
> .mp-button, .admin-button { @apply /* your button utilities */; }
> .mp-card,   .admin-card   { @apply /* your card utilities */; }
> ```
>
> That way each primitive is defined once and both names resolve to it.

The full primitive-class contract your app must satisfy:

| Class | Role |
|---|---|
| `admin-card` | Card container (typically `overflow-hidden`, rounded, shadow). Wraps tables and form panels. |
| `admin-button` | Primary button. |
| `admin-button-secondary` | Secondary / neutral button. |
| `admin-button-small` | Small button size. |
| `admin-button-danger` | Destructive button (delete). |
| `admin-field-input` | Text input / textarea. |
| `admin-field-select` | Select box. |
| `admin-field-label` | Form field label. |
| `admin-form-actions` | Row of form action buttons (submit / cancel). |
| `admin-badge` | Base badge / chip. |
| `admin-badge-neutral` | Neutral badge variant. |
| `admin-badge-on` | "On" / enabled badge variant. |
| `admin-badge-off` | "Off" / disabled badge variant. |
| `admin-badge-count` | Numeric count badge. |
| `admin-dialog-box` | Modal dialog container (delete confirmation). |
| `admin-dialog-title` | Dialog title. |
| `admin-dialog-body` | Dialog body text. |
| `admin-dialog-actions` | Dialog action-button row. |

## Nav registry

Sidebar items are contributed by services implementing `Ubermuda\AdminBundle\Menu\AdminMenuItemInterface`. The interface is auto-tagged (`app.admin_menu_item`) via `instanceof` autoconfiguration, so a plain service definition is enough — no manual tagging.

```php
namespace App\Admin\Menu;

use Ubermuda\AdminBundle\Menu\AdminMenuItemInterface;

final class UsersMenuItem implements AdminMenuItemInterface
{
    public function getLabel(): string
    {
        return 'Users';
    }

    /** lucide icon name — rendered as ux:icon "lucide:{name}". */
    public function getIcon(): string
    {
        return 'users';
    }

    public function getRouteName(): string
    {
        return 'app_admin_user_list';
    }

    /** Route-name prefix used for the active-state highlight (`_route starts with`). */
    public function getActiveRoutePrefix(): string
    {
        return 'app_admin_user';
    }

    public function getPriority(): int
    {
        return 100;
    }
}
```

Items render ordered by priority, **higher first** (an item with priority `100` appears above one with `60`).

## Listing components

The listing framework is exposed as Twig components under the `UbermudaAdmin` namespace. Reference them with the namespace prefix:

- `<twig:UbermudaAdmin:AdminList>`
- `<twig:UbermudaAdmin:AdminListFilterBar>`
- `<twig:UbermudaAdmin:AdminListSortLink>`
- `<twig:UbermudaAdmin:AdminListPagination>`

> The unprefixed forms (`<twig:AdminList>`, etc.) will **not** resolve — the components live in the bundle's `UbermudaAdmin` Twig namespace, so the prefix is required.
