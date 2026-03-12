# Lea Grammar

This document defines the user-facing grammar for Lea source files.

Scope:
- Ebook config XML (`arx/ebooks/.../*.xml`)
- Text/block XHTML files (`arx/text/...`, `arx/blocks/...`)

Conventions:
- **count** is cardinality in the relevant document scope.
- **string** means value read as text (`textContent`) unless stated otherwise.
- For tags that support inline markup in content, Lea may still consume flattened text depending on the extractor.

---

## `<lea:title>`
- count: `1`
- format: `string`

## `<lea:author>`
- count: `> 0`
- format: `string`

## `<lea:date>`
- count: `1`
- format: anything `DateTime` understands
- attributes:
  - `created` — count `[0,1]`, format anything `DateTime` understands

## `<lea:description>`
- count: `>= 0`
- format: `string`

## `<lea:script>`
- count: `>= 0`
- format: `string` (script selector via `textContent`)
- attributes: free-form key/value map (`string` -> `string`)

`lea:script` is an extensibility hook.
- `textContent` identifies the runtime handler.
- Attributes are consumed by the handler.
- Formal per-script requirements are defined in `SCRIPTS.md`.

## `<lea:collection>`
- count: `[0,1]`
- format: `string`
- attributes:
  - `type` — count `1`, allowed value: `series`
  - `position` — count `1`, format `integer`
  - `issn` — count `1`, format `string`

## `<lea:publisher>`
- count: `1`
- format: `string`
- attributes:
  - `contact` — count `1`, format `string`

## `<lea:rights>`
- count: `1`
- format: `string` (resolved via `textContent`; inline markup allowed but not semantically interpreted)

## `<lea:language>`
- count: `1`
- format: `string`

## `<lea:subfolder>`
- count: `>= 0`
- format: `string`
- attributes:
  - `tag` — count `1`, allowed values: `epub`, `text`, `images`

## `<lea:option>`
- count: `>= 0`
- format: `string`
- attributes:
  - `value` — count `1`, format `string`

## `<lea:text>`
- count: `> 0`
- format: `string`

## `<lea:isbn>`
- count: `1`
- format: `string`

## `<lea:contributor>`
- count: `>= 0`
- format: `string`
- attributes:
  - `roles` — count `1`, format `string`

## `<lea:font>`
- count: `>= 0`
- format: `string`

## `<lea:stylesheet>`
- count: `>= 0`
- format: `string`

## `<lea:cover>`
- count: `1`
- format: `string`

## `<lea:image>`
- count: `>= 0`
- format: `string`
- attributes:
  - `source` — count `1`, format `string`
  - `folder` — count `[0,1]`, format `string`
- children:
  - `<lea:caption>` — count `[0,1]`, format `string` (inline DOM allowed)

## `<lea:target>`
- count: `>= 0`
- format: `string`

## `<lea:link>`
- count: `>= 0`
- format: `string`
- common attributes:
  - `to` — count `[0,1]`, format `string`

## `<lea:subject>`
- count: `>= 0`
- format: `string`

## `<lea:blurb>`
- count: `>= 0`
- format: `string`

## `<lea:section>`
- count: `>= 0` (optional convenience tag, both per-file and globally)
- format: `string`
- attributes:
  - `title` — count `[0,1]`, format `string`
  - `class` — count `[0,1]`, format `string`

## `<lea:chapter>`
- count: `>= 0` (optional convenience tag, both per-file and globally)
- format: `string`
- attributes:
  - `title` — count `[0,1]`, format `string`
  - `content-top` — count `[0,1]`, format `string`
  - `class-top` — count `[0,1]`, format `string`
  - `class-bottom` — count `[0,1]`, format `string`

## `<lea:block>`
- count: `>= 0`
- format: `string`
- attributes:
  - `folder` — count `[0,1]`, format `string`
