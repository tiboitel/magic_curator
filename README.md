# FastsearchLBC

## 
- Need to scrapp author of  deck in get_decks_list.
- Don't build url by  concatenation but use http_build_url() instead.
- Don't generate a wantlist on txt format but use MTG Card Market API to directly create the wantlist and set the price automatically.

## Dependencies
pdeand/http PHP PSR-7 cURL HTTP Client.
lazer PHP flat-file database.
