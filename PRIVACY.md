# PhpStack Privacy and Data Processing

## Scope

This document describes privacy and data-processing characteristics of the **PhpStack** BASE3 plugin itself.

PhpStack is a packaging and bootstrap boundary for centrally bundled third-party PHP runtime libraries. It does not implement the business functionality of those libraries and does not document their internal data-processing behavior.

For a specific bundled library, use that library's upstream documentation and license files. For a concrete application workflow that invokes a bundled library, use the privacy documentation of the consuming component as the primary description of that workflow.

## Component role

PhpStack has a deliberately small runtime role.

The component:

- provides a defined directory for bundled server-side PHP library distributions
- records distribution metadata in `libraries.json`
- loads the expected packaged autoload entry point during plugin initialization when it exists
- exposes dependency diagnostics for the currently integrated distribution

The component does not itself provide application features such as document generation, reporting, messaging, AI processing, media conversion, user administration, or content storage.

## Data controller and legal basis

PhpStack does not determine a legal basis for application data processing.

Any legal basis, controller responsibility, processor relationship, retention requirement, or transparency obligation arises from the deployment and from the concrete application feature that uses a bundled third-party library.

PhpStack's own runtime behavior does not require user-related business data.

## Personal data processed directly by PhpStack

PhpStack does not intentionally process personal data in its own source code.

The current plugin does not read or store:

- user accounts or user IDs
- names or email addresses
- authentication credentials
- HTTP request parameters
- request bodies
- uploaded files
- cookies
- PHP session data
- IP addresses
- conversations
- application records
- report contents
- generated documents
- media payloads

Its own runtime decisions are based on local software files and class availability.

## Software distribution metadata

`libraries.json` contains software distribution metadata such as:

- library name
- version
- distribution form
- upstream source URL
- checksum
- license identifier
- autoload entry point

This data describes software packages and does not normally contain personal data.

The current runtime does not parse this file as user-controlled configuration and does not transmit it to an external service.

## Bundled third-party software boundary

Third-party distributions live below:

```text
PhpStack/lib/<library>/
```

These files are vendored upstream software. They are not PhpStack business logic.

PhpStack intentionally does not restate each library's:

- API semantics
- supported input formats
- internal parsing behavior
- security model
- network behavior
- temporary file behavior
- metadata handling
- rendering behavior
- application-level privacy characteristics

Those points must be assessed from the actual bundled version's upstream documentation and from the way the consuming component invokes the library.

## Availability is not the same as use

A bundled library being present does not mean that its functions process data in every request or even that they are used by the deployment at all.

PhpStack makes a library available. A separate consumer decides whether to instantiate classes from that library and what data to pass to them.

Therefore, privacy assessment must distinguish between:

1. PhpStack loading a library distribution
2. a consuming component invoking that library
3. the third-party library processing the consumer's supplied data

Only the first item is PhpStack's own responsibility.

## Runtime initialization

`PhpStackPlugin::init()` registers the plugin instance in the BASE3 service container and conditionally includes the expected third-party autoload file with `require_once`.

The existence check uses the local filesystem. No application payload or user context is needed for this operation.

PhpStack does not create an alternate processing path when the autoload file is absent. The missing distribution is surfaced through dependency diagnostics instead of being replaced with another library.

## Dependency diagnostics

`PhpStackPlugin` implements the BASE3 `ICheck` contract.

Its current diagnostics check:

- whether the expected packaged autoload file exists
- whether the expected third-party class is available

The returned values describe software installation state. They do not contain user data.

The checks do not download, update, repair, or replace a missing library.

## Filesystem access

PhpStack performs local filesystem access only to locate the bundled autoload entry point.

The component does not implement:

- application file uploads
- document storage
- user file repositories
- temporary application payload files
- filesystem crawling
- remote mounts
- file metadata indexing

A third-party library or consuming component may use files for its own purpose. Such access is outside PhpStack's own processing boundary.

## Database processing

PhpStack does not define or use a database schema.

There are no PhpStack migrations, repositories, database tables, database caches, or database-backed history records.

If a consuming component reads database data and passes it to a bundled library, the consumer owns that data flow.

## Settings, configuration, and operational state

PhpStack does not define editable runtime settings, a settings store namespace, or operational state entries.

It does not persist:

- runtime selections
- library enablement flags
- user preferences
- locks
- cursors
- run markers
- retry state
- processing history

`libraries.json` is deployment metadata and is not used as a runtime state store.

## Sessions and cookies

PhpStack does not read or write cookies.

It does not access PHP session data and does not establish its own sessions.

## HTTP request handling

PhpStack does not implement request endpoints, routes, controllers, or service handlers that consume HTTP request data.

It does not inspect query parameters, form bodies, JSON bodies, headers, uploaded files, or request-origin metadata.

## Network communication

PhpStack itself performs no network communication.

Upstream URLs recorded in `libraries.json` identify the intended software distribution. The current runtime does not fetch those URLs.

There is no automatic:

- package download
- update lookup
- telemetry request
- license server contact
- vulnerability feed request
- remote dependency resolution

A bundled library may support networking when used by a consumer. That behavior is not initiated by PhpStack merely because the package is present.

## External processors

PhpStack does not send data to an external processor.

If a consuming component uses a bundled library to contact an external service, that external processor relationship belongs to the consuming workflow and must be documented there.

## Logging

The current PhpStack source contains no PhpStack-specific calls to the BASE3 logging service.

The plugin does not intentionally log:

- user data
- request data
- document contents
- library input payloads
- library output payloads
- credentials

PHP runtime errors, framework diagnostics, or messages emitted by a third-party library during actual consumer use may still appear in the deployment's general logging infrastructure. Their content and retention are controlled outside PhpStack.

## Error handling

A missing packaged autoload file is handled by not including it. Dependency diagnostics can report the missing distribution.

PhpStack does not capture application payloads as part of this error path.

If a third-party library throws an exception while a consumer is using it, the exception originates from that consumer/library execution path and should be evaluated with the consumer's error-handling and logging policy.

## Browser storage

PhpStack has no browser-side runtime and therefore does not use:

- `localStorage`
- `sessionStorage`
- IndexedDB
- browser cookies
- service workers
- browser caches under PhpStack control

## User interfaces

PhpStack does not provide an administration or end-user interface.

There are no forms, tables, dashboards, upload controls, editors, or library configuration views in the component.

## Authentication and authorization

PhpStack does not implement application authentication or authorization checks because it exposes no data-bearing user endpoint and no administration action.

Access to the deployed server-side source tree, library files, deployment metadata, and diagnostic systems remains an infrastructure and host-application responsibility.

## Secrets and credentials

PhpStack does not define or persist API keys, passwords, tokens, signing secrets, certificates, or provider credentials.

Third-party source URLs and checksums in `libraries.json` are not secrets.

If a bundled library is later configured by a consumer with credentials, that configuration belongs to the consumer and must not be attributed to PhpStack merely because the library binary is bundled here.

## Temporary files

PhpStack itself does not create temporary files.

Some third-party libraries may create temporary files or process local paths as part of their own functionality. Whether that occurs depends on the actual library and consumer configuration and must be assessed from that library's documentation and the consuming workflow.

## Caches

PhpStack does not implement an application data cache.

Any cache internal to a bundled third-party package is part of that package's behavior and should be assessed at the library/consumer boundary if the feature is actually used.

## Background processing

PhpStack defines no worker, scheduled job, cron job, queue consumer, or asynchronous data-processing task.

No user data is processed in the background by PhpStack itself.

## Data retention

Because PhpStack does not persist application or user data, it defines no application-data retention schedule.

The component's bundled software files and `libraries.json` remain on disk until changed or removed through software deployment and version-control processes.

Retention of data produced by a third-party library belongs to the consuming component or application environment.

## Data deletion

PhpStack provides no user-data deletion function because it stores no user data.

Removing or upgrading a bundled library is a software deployment operation, not a personal-data deletion operation.

Data created by an application workflow using a bundled library must be deleted through the storage owner responsible for that workflow.

## Backups

Backups of the PhpStack source tree may contain bundled third-party software and distribution metadata.

They do not contain PhpStack-owned user records because PhpStack has no such persistence.

If deployment tooling places unrelated runtime data inside the PhpStack directory contrary to this design, that data would need separate assessment.

## Data minimization

PhpStack follows data minimization structurally by not accepting or storing application payloads at all.

The component does not need user data to perform its role as a library packaging and autoload boundary.

Consumers remain responsible for minimizing the data they pass to a bundled library.

## Third-party library privacy review

Each bundled third-party distribution should be reviewed separately when it is actually used by a feature.

The review should be based on the exact bundled version and should consider, where relevant:

- input data handled by the library
- local file access
- metadata retention
- temporary files
- logging and exceptions
- network access
- executable or active content
- output data and generated artifacts
- library-specific security guidance
- known deployment requirements

This document does not make claims about those behaviors on behalf of third-party projects.

## Third-party licenses

PhpStack's own source is licensed separately from the vendored packages.

The authoritative inventory metadata is in `libraries.json`, while the actual third-party license texts are shipped with their respective distributions below `lib/`.

Deployment owners should use those upstream license files when reviewing obligations. PhpStack does not replace or reinterpret third-party license terms.

## Distribution integrity

`libraries.json` can record a checksum for an intended upstream distribution.

The current PhpStack runtime does not enforce that checksum. Integrity verification should therefore occur in the trusted build or deployment process if it is required.

This distinction is important for security and supply-chain review but does not add a user-data processing activity to PhpStack itself.

## Automatic updates

PhpStack does not automatically check for or install new library versions.

This avoids an implicit runtime connection to external package services and keeps third-party software changes under deployment control.

It also means the deployment owner is responsible for monitoring upstream security advisories and deciding when to update a bundled distribution.

## Security updates and vulnerability management

PhpStack does not contain its own vulnerability scanner or security advisory feed.

A deployment should maintain a software inventory from `libraries.json` and the actual vendored distribution, then review upstream security information for those exact versions.

Updating a library should be performed as an explicit software change with normal review and testing.

## Library-specific configuration

PhpStack does not provide a generic configuration UI or configuration store for the bundled libraries.

If a consumer creates options or settings for a library, those values belong to that consuming component's configuration boundary.

They may include privacy-relevant settings, but such settings are not owned by PhpStack.

## Library-specific output

PhpStack does not retain or classify output generated by a third-party library.

For example, if a consumer uses a library to create a file or transform data, the generated artifact belongs to the consumer's processing flow. Its access control, storage, download behavior, retention, and deletion policy must be documented by that consumer.

## Library-specific input

Likewise, PhpStack does not inspect the input a consumer passes to a library.

The consumer is responsible for:

- determining whether personal data is necessary
- enforcing access control before reading the source data
- minimizing fields before library processing
- controlling temporary or output storage
- applying any required retention and deletion policy

## No hidden fallback processing

PhpStack does not attempt to compensate for a missing library by routing data to another package, remote service, or alternate processing mode.

If the expected distribution is unavailable, the dependency state is reported. This keeps the processing boundary explicit.

## Recommended deployment review

Before deploying PhpStack or changing its bundled library set, review at least:

1. the exact `libraries.json` inventory
2. the actual versions present below `lib/`
3. the upstream license files included with each distribution
4. the checksum of acquired release archives where applicable
5. upstream security advisories for the bundled versions
6. which application components actually invoke each library
7. the privacy documentation of those consumers
8. whether the deployment exposes diagnostic information about library availability only to appropriate operators

## Summary

PhpStack itself is not an application-data processing component. It is a local packaging and autoload boundary for third-party PHP runtime libraries.

Its own source does not persist user data, receive HTTP payloads, use browser storage, access sessions, create database tables, send network requests, manage credentials, or run background processing.

The privacy impact of a bundled library arises only when another component actually invokes that library with application data. That processing must be documented at the consuming component and third-party library boundary rather than being generalized as PhpStack behavior.
