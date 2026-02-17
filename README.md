# 🝄 Lea ePub anvil

Lea produces ePubs conforming to the latest standards, i.e., 3.3 at the time of writing. Lea is not trying to be a full
ePub authoring environment, a semantic curator, or a replacement for Sigil's editorial layer. Lea is a compiler,
validator, and normalizer. The output is intended to be opened in Sigil. Lea guarantees correctness, human editors can
add meaning later.

## Features

- filename and identifier consistency
- dynamic local cross-file link targets
- external link target validity check
- generation of ePub manifest, including metadata and spine
- error messages with focused suggestions for fixes
- easy to learn and use if you know some XML or HTML
- Lea is a bitch, the female dog variant; you can always rely on her

### Existing

* filename and identifier consistency
* dynamic local cross-file link targets
* generation of ePub OPF manifest
* support for book series collections with ISSN
* support for anthologies with per-text authors and rights
* support for text blocks
* support for the execution of pre-made scripts enabling handling of any edge cases like linked images
* <lea:> tags anywhere in the source files, for example, for images with captions
* validation of external links
* support for advanced typesetting: embedded fonts and stylesheets
* EPUB checker validation
* command-line arguments for run-time configuration
* the example ebook of 7+ MB compiles into a 3.2 MB ePub in 0.236 seconds on my Zen-2 based slow-ass notebook

### Planned

* template system for images
* automatic detection and content-based compression of image files
* import of word processor documents into Lea definition files
* simple but useful reports

## 1.0.0 and ⊙

🝄 stands for Lea as a holistic entity. The glyph ⊙ was chosen as a marker for specific aspects at defined points in
time. If 🝄 is the forge, then ⊙ is the moment when the hammer strikes and a new creation emerges.

⊙ expresses a structured combination where the structure matters more than the operands. It is not about symmetry but
coherence. ⊙ marks the point around which a meaning is calibrated. Lea's process is information-preserving
normalization, or a lossless canonicalization.

With the release of "⊙ 1.0.0," Lea will be able to dynamically create an ePub from config and content files only.

Releases marked with ⊙ are recommended for most users. These releases will be stable, have long-term support, and will
come with major new features.

## License

Copyright Eduard Pech (https://logophilia.eu/the-director-without-a-service-weapon/). Licensed under
PolyForm-Noncommercial-1.0.0; see separate file LICENSE.md for details.

## Kilometer stones

1.0.5

* Added a third option to the lea:subfolder tag for the ePub output folder.
* Added a method to sanitize stylesheets; included images are automatically imported into the ePub.
* Scripts are processed first now, so that they can output blocks; useful for dynamic lists.
* Added a script to iterate over all authors and include text blocks based on their names.
* Girlfriend's readFile is not case-sensitive anymore on Linux; she'll still complain.
* Changed script execution to work on ebook config files; handy for some dc tag automation.

1.0.4

* Grammar changes to simplify tags and their usage.
* Fixed metadata error: multiple authors for a text produced a wrong identifier for any author beyond the first.
* Added spinner animation to EPUBCheck validation.
* Fixed an exception when checking external links with an empty list of URLs.
* Checking external URLs doesn't hammer the CPU anymore, thanks to usleep.
* The lea:subfolder tag can now discriminate between text and images via an added attribute.
* Simplified grammar for collection, contributor, date, and publisher tags.
* Added a few granular progress messages; you'll know when you see them.
* Added a script for linked images.

1.0.3

* Added handling of lea:block tags.
* Added extraction of rights on a per-text basis.
* Added the script class AlphabetSt and the execution of lea:script tags.
* Added three scripts: table of contents, list of rights, list of blurbs.
* Other things: fixed extraction of rights and blurbs, refactoring, new helper functions and messages.

1.0.2

* Refactored the message system using a new class called Affirmation.
* Lea will now base the repo path relative to where she was invoked.
* Also flattened and renamed the examples folder to arx.

1.0.1

* Refactored validation of external link targets for a 10x speed gain.
* Generating and adding production log to the META-INF folder.
* The last - and only - TODO is gone, and we have a proper ISBN validation function now.
* Refactored lea:image tag handling.
* Refactored lea:link tag handling; links are now permitted to contain child nodes.
* Added EPUBCheck validation option.
* Moved PurpleRain out of the REPO root and into the resources' folder.
* Updated the example files to use attributes for lea:image and lea:link.
* Refactored ISBN class to cache ISBN validation results.
* Extended Target class with a property for the normalized identifier.

⊙ 1.0.0 "Check it out, here's what you gotta do (Push) Gotta step in the room with the mood Never juicing yourself, just
a confident attitude (Push) Believe me, you will get busy"

* Modified README.md to clarify the glyphs.
* New Target class for link targets.
* New feature: global link targets; links don't need to know in which text file the targets were declared.
* Added validation of external link targets; this required adding the curl dependency to composer.json.

0.0.22 "Nuestra presentacion especial comenzara en breve"

* As 1.0.0 is drawing near, today was the day to settle on a glyph for Lea: "⊙" (U+2299).
* Updated this README in preparation for the 1.0.0 release.
* Girlfriend can silence dove cries now.
* Girlfriend can recall memories.
* PaisleyPark is under the new leadership of pControl.
* Added dynamic metadata generation, completing content.opf.
* New killer feature coming next time!

0.0.21 "No image at all?"

* Mostly refactoring the handling of ePub cover and navigation files.
* Added a new Image class, as well as extraction and handling of images.
* Added memory to Girlfriend.
* Added new subfolder tag for global subfolder definition.
* Added new defaultcaption tag for convenience.
* Fixed the DOM reWrap method.
* EPUB checker now at 0 errors. Next: meta data generation and global link targets.

0.0.20 "Look, everybody makes mistakes, oh yeah, not one or two (Right!)
But that don't make the dirty little things they say about you true (You tell 'em!)"

* Added extraction and handling of stylesheets
* Added extraction and handling of fonts
* Simplified identifier data structure
* Added generation and handling of the cover xhtml file
* Added generation and handling of nav.xhtml
* Validated with pagina EPUB Checker: 91 errors, all from missing references. ⇒ Generation is error-free.

0.0.19 "Strip your dress down"

* Lea can be completely stripped now!
* All DOMs of text files are also rewrapped into valid xhtml before output.
* May sound funny, but we're closer to 1.0.0 than you think. Sigil already approves, with a few hacks in place.

0.0.18 "Why did I ever let you in this morning? Why I did let you come inside my door?"

* Extract theOpera into its own class.
* Added function into Girlfriend to extract a subset of array elements with keys matching a pattern.
* Now building normalized identifiers once, shaving off milliseconds versus repeated object access.
* Methods to build metadata, manifest, spine, and guide blocks for content.opf. Incomplete as yet.

0.0.17: "The ride up front is better When you've been in the back."

* Added three string normalization methods to Girlfriend.
* Started with theOpera, which implies adding ZipArchive dependency to composer.json.
* Lea can produce a complete ePub now, minus table of contents and DOM headers. Getting there!

0.0.16: "Come to the park and play with us. There are many rules in Paisley Park."

* Changed PaisleyPark constructor in preparation for batch processing. Not sure whether it will ever happen though.
* Added DOMDocument properties to classes in preparation for serialization and conversion during theOpera.
* Added Date class and date extraction.
* Added extraction of description.
* Added Publisher class and publisher extraction.
* Added extraction of rights declaration.
* Added extraction of language declaration.
* Added extraction of the cover file name.
* Added extraction of image file names. Images will get a massive overhaul later, but works for now.
* Added Collection class and collection extraction.
* Made XPath queries more specific to account for node context.
* Fixed ISBN property in Ebook class, which was not following pattern for properties.

0.0.15: "Tell me, how're we gonna put this back together? How're we gonna think with the same mind?"

* Added contributors and subjects to Ebook class
* New Contributor class
* More and better error handling that removed several dependent errors
* New pretty-print message-function in PaisleyPark
* Prepared the example files to include all required declarations

0.0.14: "Style is not biting style when you can't find the funk."

* Added Fancy class handling ANSI color codes
* Expanded message system for more flexibility later, during user interaction
* Updated PaisleyPark → Segue with messages using new system

0.0.13: "It's time we all reach out for something new. That means you, too."

* Moved validation out of collection classes; will be the gist of processing instead
* Added a few new helper functions
* Limited list of error messages printed; we don't want to overwhelm anyone here

0.0.12: "Oops, out of time."

* Refactoring, mostly
* Also added a few more properties

0.0.11: "I'm delirious; You, you, you get me delirious"

* Have been fighting with XML, XSD, and RELAX NG for the better part of the day, to no avail
* Decided to bake the grammar into clean PHP code instead
* Girlfriend is collecting soft errors now, might be useful at some point

0.0.10: "Let's Go Crazy"

* XMLetsGoCrazy domain helper class, initial
* Text domain object extracting metadata from xhtml fragment files upon construction, proof of concept

0.0.9: "There're thieves in the temple tonight"

* VSCodium has problems with syntax highlighting - PHPStorm to the rescue; project setup
* Minor refactoring, while we're at it

0.0.8: “I was dreamin' when I wrote this, forgive me if it goes astray”

* A bit of refactoring
* Added properties to DTOs

0.0.7: "You don't have to watch Dynasty to have an attitude"

* Class structure: IPO=Cream, Segue, TheOpera
* Started on some component classes
* Releases are named from now on
* We also have an exclusive Girlfriend as of this release

0.0.6: Added some sample files into examples folder

0.0.5: VSCodium json files to facilitate

* ‘composer dump-autoload’ on file changes
* Xdebug for CLI on port 9004
* ebook configs folder structure under ‘examples’

0.0.4: Initial PSR-4 and ancillary setup

0.0.3: Conceptualize classes and folder structure (high level)

0.0.2: Initial code commit after setting up VSCodium

0.0.1: Figure out this new personal access token bs

0.0.0: We gaze into that void, hearts bared to its depths, and in our longing stare ... something shifts. A stillness
that aches like a farewell, as if our dreams summon a reply too vast for our fragile existence to bear.