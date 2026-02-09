# Lea ePub anvil

Produce ePubs conforming to latest standards.  
Support for many advanced features like semantics.  
Easy to use.

## Planned features:

* semantic tagging
* proprietary XML tags
* text blocks (simple templating, using those XML tags)
* automated manifest (authors including: "file as," tagging of specific texts)
* automated spine
* automated table of contents (configurable via templating)
* automated inclusion of font files (from CSS definitions)
* EPUB checker validation
* dry run
* independent of OS: developed on Fedora, but should run on Ubuntu without any problems
* others I can't remember right now

The output is supposed to be opened in Sigil. You should check the report cards and make sure everything is squeaky
clean.  
Don't forget to prettify, because Lea only works for paranoiacs!

## Launch timing of 1.0.0

Semantically defined as: Lea creates an ePub identical to one I have previously produced manually (except for the
useless stuff that Sigil inserts sometimes, like the bookmarks file). This would include passing EPUB checker, and
manual inspection of ZIP contents (including diffs where applicable).

## License

Copyright Eduard Pech (https://logophilia.eu/the-director-without-a-service-weapon/). Licensed under
PolyForm-Noncommercial-1.0.0; see separate file LICENSE.md for details.

## Kilometer stones:

0.0.21 "No image at all?"

* Mostly refactoring the handling of ePub cover and navigation files.
* Added new Image class, and extraction and handling of images.
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

The following will be done ASAP. Not available at the time of writing.

x.y.z: "Rearrange your brain, got to free your mind!"

* Moved repo to sha-256 in preparation for Git 3.0

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

* Have been fighting with XML, XSD and RELAX NG for the better part of the day, to no avail
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