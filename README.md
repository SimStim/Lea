# Lea: ePub anvil

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

0.0.10: "Let's Go Crazy"

* XMLetsGoCrazy domain helper class, initial
* Text domain object extracting meta data from xhtml fragment files upon construction, proof of concept

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