# Scratchpad

We will gradually revise the content of this file and move it to the official documentation.

## Installation

    # To create non-default PlantUML graphs
    $ sudo apt-get install graphviz

### Set up everything

#### By hand

    $ composer install
    $ php artsian migrate
    $ php artisan storage:link
    $ php artisan make:filament-user --name=admin --email=admin@admin.com --password=<your password>
    $ php artisan vendor:publish --tag=swark-assets
    $ php artisan vendor:publish --tag=swark-stamdata

    # import default contents, e.g. NIS2
    $ app:import default-importables/regulations

### Initially import static content, e.g. Excel files or markdown

swark expects a specific directory format, see technical details below.
Make sure, that your initial content is placed into the following file structure:

- `./custom/import.xlsx`
- `./custom/regulations/`
- `./custom/content/`

1. Copy that directory structure - including the Excel file - to `./stamdata/import/`
2. Import that content by executing

   $ app:import stamdata/custom

This will take a few seconds. After importing everything you can access swark and Filament to look through the data.
In order to generate dynamic architecture diagrams you need to install Java:

    $ sudo apt-get default-jre


## CI/CD process

### Versioning/Branching

We are using the same versioning and branching model as Laravel:

| Branch   | Purpose                        |
|----------|--------------------------------|
| `main`   | Current development effort     |
| `(\d).x` | Major version w/ bugfixes etc. |

### Releasing a new version
Releases will be done manually. Go to [our release workflow](https://github.com/swiss-architecture-knife/framework/actions/workflows/release.yaml), select `Run workflow` and pick the correct main branch.
Your `version to release` must match the selected branch, e.g. branch `0.x` can only be used for releases of `0.1.1` versions and not `1.0.0`.

With help of that workflow, the latest commit in the selected branch is tagged and a new GitHub release is created. The `CHANGELOG.md` in the version branch ist automatically updated.

## Technical details

### Importing initial content

swark uses an Excel file for most of the (tabular) stamdata. For more textual content like an analysis or a report, that
content can be placed in markdown files.
During an initial import, swark reads the Excel file specified as argument for `app:ingest-excel`.

- The directory `./public` contains public content, like publicly available regulations and their chapters.
- The directory `./private` contains _private_ content. For example, `./private/regulation/nis2/1/actual.md` would map
  to the actual status of the regulation chapter 1 of NIS2.

| File                                               | Description                                                                                                          |
|----------------------------------------------------|----------------------------------------------------------------------------------------------------------------------|
| `./regulation/${regulation}/${chapter}/actual.md`  | Actual status of a regulation chapter. If present, it will override the Excel column "E" in "Regulation chapters"    |
| `./regulation/${regulation}/${chapter}/target.md`  | Target status of a regulation chapter. If present, it will override the Excel column "F" in "Regulation chapters"    |
| `./content/company/introduction.md`                | Introduction to this installation's tenant                                                                           |
| `./content/strategy/introduction.md`               | Introduction for IT architecture strategy                                                                            |
| `./content/strategy/vision_quote.html`             | Quote for vision                                                                                                     |
| `./content/strategy/vision_title.html`             | Title for vision                                                                                                     |
| `./regulation/${regulation}/${chapter}/law.md`     | Official text of regulation. Supports Frontmatter: Use `relevancy`, `heading`, `reference` and `regulation.scomp_id` |
| `./regulation/${regulation}/${chapter}/summary.md` | Opinionated summary of this regulation                                                                               |

## Testing
### Running unit tests
```
vendor/bin/phpunit --testsuite=unit
```

### Running integration/feature tests

```
vendor/bin/phpunit --testsuite=integration|feature
```

By default, the testing environments expect you to have a valid MySQL database connection with `DB_HOST=127.0.0.1`, `DB_NAME=swark_testing`, `DB_USERNAME=root` and `DB_PASSWORD=root`. You can supply e.g. an empty password and a different database host by executing

```
DB_HOST=myhost.local DB_PASSWORD="" vendor/bin/phpunit
```
