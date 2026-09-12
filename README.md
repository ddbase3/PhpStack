# PhpStack

PhpStack is the server-side counterpart to ClientStack.

ClientStack owns browser and UI libraries. PhpStack owns centrally bundled third-party PHP runtime libraries that are shared by BASE3 plugins and should not require nested Composer installations.

## Scope

PhpStack is intentionally small.

It currently provides only the infrastructure boundary for bundled PHP libraries and loads their own packaged autoloaders during plugin initialization.

PhpStack does not provide:

- a second dependency injection container
- a library resolver service
- runtime version selection
- automatic downloads or updates
- an administration UI
- a nested Composer installation

Consumers use the namespaces exposed by the bundled libraries directly.

## Structure

```text
PhpStack/
├── lib/
│   └── dompdf/
│       ├── autoload.inc.php
│       ├── vendor/
│       └── ...
├── src/
│   └── PhpStackPlugin.php
├── libraries.json
├── LICENSE
└── README.md
```

`libraries.json` records the intended library distribution, version, source, checksum, license and autoload entry point. It is deployment metadata only and is not interpreted as runtime configuration.

## Dompdf

The first bundled library is Dompdf 3.1.6.

PhpStack bundles the official packaged Dompdf release under:

```text
PhpStack/lib/dompdf/
```

The packaged release is used because it contains Dompdf together with the dependency versions selected for that release and provides its own `autoload.inc.php`.

Release metadata:

```text
Version: 3.1.6
Archive: dompdf-3.1.6.zip
SHA-256: 05df8ee4907325ed2e09a139de9784325f761b43a049297155499270163ac94d
Source: https://github.com/dompdf/dompdf/releases/tag/v3.1.6
```

The bundled distribution provides the entry point:

```text
PhpStack/lib/dompdf/autoload.inc.php
```

`PhpStackPlugin::init()` loads that file when present. The plugin does not fail the entire BASE3 bootstrap if a bundled library is absent. Dependency diagnostics report the missing distribution instead.

## Usage

A consuming plugin does not depend on a PhpStack service. Once PhpStack has initialized, it uses the library namespace directly:

```php
use Dompdf\Dompdf;
use Dompdf\Options;
```

For example, an ILIAS-specific `pdfreportexporter` can live in IliasReporting and use Dompdf without introducing a dependency from Vizion to IliasReporting or from Vizion to PhpStack.

## Dependency direction

```text
ResourceFoundation
    shared report-export contract

PhpStack
    bundled server-side PHP libraries

IliasReporting
    platform-specific exporter implementation
    uses Dompdf namespace

Vizion
    platform-neutral report UI
    discovers exporters by interface and getName()
```

PhpStack does not define reporting contracts and does not know about IliasReporting or Vizion.

## Future libraries

Additional PHP libraries may be added under `lib/<library>/` and documented in `libraries.json` when there is a concrete use case.

Automatic installation or update handling is deliberately not part of the current design. If that becomes necessary later, it should be designed as a deployment concern rather than runtime behavior.
