# 🝄 Lea EPUB Anvil

Lea produces EPUBs conforming to the latest standards, i.e., 3.3 at the time of writing.
Lea is not trying to be a full EPUB authoring environment, a semantic curator,
or a replacement for Sigil's editorial layer. Lea is a compiler, validator, and normalizer.
The output is intended to be opened in Sigil. Lea guarantees correctness,
human editors can add meaning later.

## Features

- filename and identifier consistency
- dynamic local cross-file link targets
- validation of external links
- generation of EPUB OPF manifest, including metadata and spine
- error messages with focused suggestions for fixes
- easy to learn and use if you know some XML or HTML
- Lea is a bitch, the female dog variant; you can always rely on her
- support for book series collections with ISSN
- support for anthologies with per-text authors and rights
- support for text blocks
- support for the execution of pre-made scripts enabling handling of any edge cases like linked images
- structural tags (`lea:image`, `lea:chapter`, `lea:section`, `lea:subsection`) must be defined
  directly under the lea root, while inline tags like `lea:link`, `lea:target`, `lea:script`
  and `lea:block` may appear anywhere, and `lea:caption` lives inside its `lea:image`;
  misplaced tags are reported and omitted from the EPUB
- support for advanced typesetting: embedded fonts and stylesheets
- automatic cover page generated from `<lea:cover>`
- EPUBCheck validation
- production log shipped inside every EPUB (META-INF/lea-log.txt), recording Lea's messages
  and the EPUBCheck report
- command-line arguments for run-time configuration
- `<lea:>` tags are globally checked for syntactical correctness (case, spelling, and placement),
  even those dynamically injected by scripts
- all generated content is XML-escaped
- source files may be well-formed XML documents or bare XML fragments
- generated EPUB is backwards-compatible with the EPUB2 standard
- repo includes a fully Lea-produced, actually published title:
  the 2026 annual omnibus edition "The Pitch Science Fiction 2026". Over 3000 pages,
  700,000+ words, more than 200 illustrations, compiled in a fraction of a second.
  A year of science fiction, forged in less time than it takes to turn a page.

### Planned

* new helper class NewPowerGeneration.
* pipe every file access through Girlfriend for centralized error handling.
* check for the existence of extensions before using them, e.g., curl.

## 1.0.0 and ⊙

🝄 stands for Lea as a holistic entity. The glyph ⊙ was chosen as a marker for specific aspects
at defined points in time. If 🝄 is the flux for soldering, then ⊙ is the moment when
the soldering iron heats the flux and a new creation emerges.

⊙ expresses a structured combination where the structure matters more than the operands.
It is not about symmetry but coherence. ⊙ marks the point around which a meaning is calibrated.
Lea's process is information-preserving normalization, or a lossless canonicalization.

With the release of "⊙ 1.0.0," Lea will be able to dynamically create an EPUB
from config and content files only.

Releases marked with ⊙ are recommended for most users. These releases will be stable,
have long-term support, and will come with major new features.

## 1.1.0 and the root guarantee

Version 1.0.0 promised that `<lea:>` tags may appear anywhere. It was a generous
promise, and scrutiny found its cost: a structural tag standing in the wrong
place simply vanished from the finished EPUB, and Lea never said a word. The
same fate awaited a tag whose name lost its case. Silence is not normalization;
Lea's process is information-preserving, and a silent omission is information
destroyed.

So the grammar got teeth, and the two doors through which tags could disappear
are now closed and guarded. The name guard: a misspelled tag was reported
before, and now a tag that merely loses its case is caught as well. The
placement guard is new, and it is called the root guarantee: the structural
tags `lea:image`, `lea:chapter`, `lea:section` and `lea:subsection` must be
defined directly under the lea root. Inline tags keep the old freedom:
`lea:link`, `lea:target`, `lea:script` and `lea:block` may appear anywhere,
and `lea:caption` lives inside its `lea:image`, where a caption belongs.

A structural tag that breaks the guarantee is no mystery anymore. Lea names
the tag, still produces the EPUB so it can be inspected, and marks it as one
that must not be published. And she keeps her word at the moment she speaks
it: the misplaced tag is removed from the document on the spot. Lea eats her
own words, so no later stage of the compilation will ever find them again.

Lea carrying these guarantees will be marked ⊙1.1.1. Ed is still working on her.

## License

Copyright Eduard Pech (https://logophilia.eu/the-director-without-a-service-weapon/). Licensed under
PolyForm-Noncommercial-1.0.0; see separate file LICENSE.md for details.
