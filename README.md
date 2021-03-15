# MTGDatahealp

## Usage

- Update database by launching php ./scripts/Crawler.php

## 
- Don't write all decks on memory then on file. Append at end of the file each time.
- Need to fixes authentification middleware.
- Use Queries to generate wantlist.
- Add session and binder. the wantlist substract the binder before rendering.We can see also the deck we can build with our builder. Something like you have 80% card of this archetype. 
- Don't build url by  concatenation but use http_build_url() instead.
- Look PHP-Goose module system for scrapper.
- Parser et stocker proprement tout les donnees.
- Creer des Mappers pour Deck, Decklist, Cards, Events. (Meh?)
- Don't generate a wantlist on txt format but use MTG Card Market API to directly create the wantlist and set the price automatically.

## Dependencies
pdeand/http PHP PSR-7 cURL HTTP Client.

## Contributing

- Fork it (( https://github.com/[my-github-username]/magic_curator/fork )
- Create your feature branch (`git checkout -b my-new-awesome-feature`)
- Commit your changes (`git commit -am `Added some new awesome feature`)
- Push to the branch (`git push origin my-new-awesome-feature`)
- Create the new pull request ! :)
