This program counts votes under a new Schulze-based voting system I designed for the Eurovision Song Contest.

Thank you to Julien Boudry for writing Condorcet, a PHP library that this program is hugely dependent on.

Before using for the first time, execute the following command to get the required libraries.
```shell
composer update
```

The syntax to run the program is `php src/main.php [cvotes file]`.

The .cvotes file contains the list of candidates and the list of votes. For demonstration, you can use the included `some-votes.cvotes`. All votes should be tagged with a 3-letter country code to show the country of origin.
Another included .cvotes file is `grand-final-votes.cvotes`, but the program currently runs out of memory if this one is used.

Countries eligible to vote are written in `voting-countries.json` as 3-letter codes.


The voting method currently supported, called "Eurovision Schulze", each country's voting power is proportional to the cube root of vote margin times population. This is different from the current contest, where each participating country has equal voting weight. This formula effectively gives more influence to voters in less decisive countries, as a pairwise ratio of 9:16 is half as strong as a ratio of 1:0 (which means unanimous agreement to which entry is better). This may later be softened. This method will later be adjusted to not give excess weight to the world vote, where the ratio of population to voters would presumably be very high.
