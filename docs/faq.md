# PhpStack Frequently Asked Questions

## What is PhpStack?

PhpStack is a BASE3 plugin that provides a common repository location for bundled third-party PHP runtime libraries.

Its purpose is deliberately narrow: selected server-side PHP library distributions can be shipped once in a central component and made available to BASE3 plugins without requiring every consuming plugin to carry its own nested Composer installation or duplicate the same third-party package.

PhpStack is infrastructure for packaging and loading libraries. It is not an application framework, package manager, dependency resolver, library abstraction layer, or runtime service facade.

## What problem does PhpStack solve?

Reusable BASE3 plugins may need the same server-side PHP library. Without a shared boundary, every consumer could bundle its own copy, select its own version, and maintain its own autoload setup.

PhpStack provides one explicit place for such centrally bundled runtime distributions. This keeps the distribution choice visible and avoids unnecessary copies of the same package inside multiple plugins.

## Is PhpStack the server-side equivalent of ClientStack?

At a high level, yes. ClientStack groups third-party browser-side libraries and related UI assets. PhpStack groups selected third-party PHP runtime distributions for server-side use.

The two components remain independent. PhpStack does not load browser assets and ClientStack does not provide PHP runtime libraries.

## Does PhpStack provide its own PHP APIs for third-party libraries?

No.

PhpStack does not wrap or reimplement the APIs of the bundled libraries. Once the relevant packaged autoloader has been loaded, consuming code uses the namespaces and APIs provided by the third-party library itself.

For library-specific classes, methods, supported formats, behavior, limitations, security guidance, and upgrade notes, consult that library's own documentation.

## Which third-party libraries are bundled?

The authoritative distribution inventory is `libraries.json` in the PhpStack root.

Each entry records deployment metadata such as:

- the library name
- the bundled version
- the distribution form
- the upstream source
- the expected checksum
- the declared license
- the local autoload entry point

The bundled files themselves live below `lib/`.

This FAQ intentionally does not reproduce the libraries' own manuals. The upstream README and license files shipped with a distribution remain the appropriate source for library-specific behavior and licensing details.

## Why does the documentation not describe each bundled library in detail?

PhpStack owns the packaging boundary, not the third-party projects themselves.

Documenting a third-party library as though it were PhpStack functionality would blur ownership and make the component documentation stale whenever an upstream package changes. PhpStack therefore documents:

- how a distribution is included
- how it is loaded
- how its presence is checked
- where its version and license metadata are recorded

The third-party project's documentation remains authoritative for everything inside that library.

## Where are bundled distributions stored?

Bundled distributions are placed below:

```text
PhpStack/lib/<library>/
```

The current source tree contains a packaged distribution under `lib/dompdf/`.

The contents of that directory belong to the upstream third-party distribution and should be treated as vendored software rather than as PhpStack source code.

## What is `libraries.json` for?

`libraries.json` is deployment metadata for the bundled library set.

It records which distribution PhpStack intends to ship and how that distribution can be identified. It is useful for review, reproducibility, license inventory, and integrity checks performed by deployment or maintenance processes.

The current PhpStack runtime does not parse `libraries.json` as runtime configuration.

## Does PhpStack automatically download libraries from the internet?

No.

There is no automatic download, update, installation, or remote package-resolution mechanism in the current plugin. A library distribution must already exist under the expected `lib/` path when the plugin initializes.

This keeps package acquisition in the deployment process rather than in application runtime behavior.

## Does PhpStack run Composer during application startup?

No.

PhpStack does not execute Composer and does not maintain a nested Composer installation. It loads the autoload entry point supplied by the bundled packaged distribution.

## Does PhpStack create a second autoloader architecture for BASE3?

No.

PhpStack only loads the packaged autoload entry point of a bundled library when the plugin initializes. The library's own distribution is responsible for exposing its namespaces from that point onward.

PhpStack does not introduce a separate class map, container, package registry, or dependency injection mechanism.

## How does PhpStack initialize?

`PhpStackPlugin` implements the BASE3 `IPlugin` contract.

During `init()` it:

1. registers the plugin instance in the BASE3 container under its technical plugin name
2. resolves the expected autoload file for the bundled distribution
3. includes that autoload file with `require_once` when the file exists

The plugin does not perform application-specific processing during initialization.

## What is the technical plugin name?

`PhpStackPlugin::getName()` returns:

```text
phpstackplugin
```

This is the stable technical name used by BASE3 discovery and container composition.

## Does PhpStack expose a library resolver service?

No.

There is currently no service such as a library registry, version selector, package locator, or runtime resolver.

Consumers do not request a library from PhpStack. They use the classes exposed by the loaded third-party distribution directly.

## Can multiple versions of the same library be selected at runtime?

No.

PhpStack does not implement runtime version selection. The bundled distribution in the deployed component determines the available version.

If a version changes, that is a deployment change to PhpStack's bundled library set, not a per-request or per-consumer runtime choice.

## What happens if a bundled library is missing?

PhpStack does not intentionally fail the whole BASE3 bootstrap merely because the expected packaged autoload file is absent.

`init()` checks whether the file exists before including it. Dependency diagnostics can then report that the distribution is missing or that the expected library class is not available.

A consumer that actually requires the missing library can still fail when it tries to use that library. The component does not create a fallback implementation or silently substitute another package.

## Does PhpStack provide dependency diagnostics?

Yes.

`PhpStackPlugin` also implements `ICheck` and exposes dependency checks for the currently integrated bundled distribution. The checks verify whether the expected autoload file exists and whether the expected library class can be loaded.

These checks are diagnostics only. They do not install, repair, download, or replace the third-party software.

## Does PhpStack verify `libraries.json` checksums at runtime?

No.

The checksum in `libraries.json` is deployment metadata. The current `PhpStackPlugin` does not calculate or compare package checksums during application startup.

If checksum enforcement is required, it belongs in the build, release, deployment, or integrity-verification process.

## Does PhpStack modify bundled third-party source code?

The intended model is to bundle official packaged distributions intact.

PhpStack's own source does not patch the third-party library during runtime. Any project that changes vendored third-party files should document that separately because it would no longer be an unmodified upstream distribution.

## Does PhpStack provide an administration interface?

No.

The plugin contains no administration display for installing, updating, enabling, disabling, or configuring libraries.

## Does PhpStack have editable runtime settings?

No PhpStack-specific settings store, configuration form, or runtime library setting is present in the component.

The library inventory is deployment metadata, not an editable runtime configuration system.

## Does PhpStack use a database?

No.

The PhpStack component itself contains no database repository, migration, table definition, or database-backed state.

Third-party libraries may be used by consumers in workflows that access databases, but that behavior belongs to the consumer and the third-party library, not to PhpStack.

## Does PhpStack use the BASE3 State Store?

No.

The component does not persist locks, cursors, run markers, caches, or other operational state through `IStateStore`.

## Does PhpStack define background jobs or workers?

No.

There is no PhpStack job for downloading, updating, scanning, or refreshing bundled libraries.

## Does PhpStack perform network requests?

No network client or remote request is implemented by PhpStack itself.

The upstream source URLs in `libraries.json` are descriptive deployment metadata. The current runtime does not fetch them.

A bundled third-party library may itself support network-related behavior when explicitly used by a consumer. Such behavior is governed by that library and the consuming code, not by PhpStack initialization.

## Does PhpStack process HTTP request data?

No.

The component does not read request parameters, request bodies, cookies, sessions, uploaded files, or browser headers.

## Does PhpStack write logs?

No PhpStack-specific logging calls are present in the component source.

Errors or warnings produced by PHP itself or by a third-party library when a consumer uses it can still reach the host application's normal error or logging infrastructure. Those messages are outside PhpStack's own logging behavior.

## Does PhpStack store user data?

No.

PhpStack has no user repository, conversation store, file store, settings store, state store, or other persistence layer.

The component's own metadata describes software distributions, not users.

## Can a bundled library process personal or confidential data?

Potentially, yes, but only when consuming code passes such data to the library.

PhpStack does not inspect, transform, sanitize, retain, or transmit those application payloads. For the data-processing behavior of a particular library, consult:

- the consuming component's documentation
- the third-party library's upstream documentation
- the deployment's configuration and operational controls

This distinction is important because merely bundling a library does not mean that every feature of that library is used.

## Does the presence of a library mean that the application uses it?

No.

A bundled distribution being available only means its classes can be loaded. Actual use depends on whether another component calls those classes.

The privacy, security, and operational impact of a library must therefore be assessed in the context of the concrete consumer that invokes it.

## Where should license information be checked?

Use two sources:

1. `libraries.json` for PhpStack's inventory metadata
2. the upstream license files contained in the corresponding `lib/<library>/` distribution

The PhpStack plugin itself is licensed under GPL-3.0 as stated in its own `LICENSE` and source header. Bundled third-party code retains its own licenses.

## Who is responsible for third-party license compliance?

The deployment or distribution owner must review the licenses of the third-party software it ships and make sure required notices, source availability, attribution, or other obligations are satisfied.

PhpStack makes the bundled software boundary visible, but it does not replace legal or license review.

## How should a new library be added?

A new library should only be added for a concrete server-side use case.

At minimum, the change should:

1. place the intended upstream distribution below `lib/<library>/`
2. retain the upstream license and relevant distribution documentation
3. add or update the corresponding `libraries.json` entry with version, source, checksum, license, and autoload information
4. update `PhpStackPlugin` only if explicit bootstrap loading or dependency diagnostics are required
5. verify that no unrelated runtime architecture is introduced simply to accommodate the package

The component should remain a simple packaging and loading boundary.

## Should PhpStack grow into a general package manager?

That is not the current design.

Automatic dependency solving, runtime downloads, version negotiation, per-plugin package graphs, and self-updating libraries would create a substantially different responsibility. If such capabilities ever become necessary, they should be designed as deployment infrastructure instead of being added as incidental runtime behavior to PhpStack.

## Should consumers depend on `PhpStackPlugin` directly?

Normally, no.

A consumer that needs a bundled third-party library should use that library's namespace after the application composition has made the distribution available. PhpStack intentionally does not define application-domain contracts for those libraries.

Direct dependency on the plugin class would usually add coupling without providing a useful runtime abstraction.

## Does PhpStack define application-domain contracts?

No.

PhpStack does not define report exporters, document models, rendering contracts, storage contracts, or other application-specific APIs. Such contracts belong in the appropriate BASE3 framework or foundation area.

## Does PhpStack decide which application feature uses a bundled library?

No.

PhpStack does not know which consumer invokes a bundled library or for what business purpose. That decision stays with the consuming plugin or project composition.

## Is `lib/` part of PhpStack's own source API?

No.

`lib/` is the vendored third-party distribution area. PhpStack's own runtime integration is represented by its plugin source and distribution metadata.

Code should not treat internal files of a bundled package as stable PhpStack APIs. Use the public API documented by the upstream library.

## Where can I find privacy information?

See [PRIVACY.md](../PRIVACY.md).

That document describes the privacy characteristics of PhpStack itself and explains the boundary between PhpStack and the third-party libraries it bundles.
