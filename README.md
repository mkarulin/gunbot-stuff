# gunbot-stuff
Scripts that i use with Gunbot

## Scripts
### Gunbot pair generator (Binance only)
#### Usage 1: 
`php generator.php` will generate a single json of all Binance pairs with minimum volume of 500 and strategy "tssl"
#### Usage 2:
`php generator.php 1000 stepgain 2` will generate json of all Binance pairs with minimum volume of 1000 an strategy "stepgain", equally divided into 2 groups. This is useful when running multiple instances.
