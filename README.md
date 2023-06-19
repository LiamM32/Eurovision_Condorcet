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


The first method, called 'Eurovision Schulze' or 'Grand Final 1.5 root' gives each participating country a total voting strength proportional to the following formula:
$$\sqrt[3]{[voters]×[population]}$$
Therefore, if every country has the same ratio of voters to population, the weight per voter would be proportional to $\frac{1}{\sqrt[1.5]{[voters]}}$.
This may later be modified to give voters outside the EBU the same voting weight as the lowest in a participating country.
This method transforms the voting margins from each country's votes to make it further from 0, closer to unanimity.

The second method, called 'Eurovision Schulze 2' or 'Grand Final square root', is less proportional, closer to equal representation. It gives each country voting power proportional to the square root of number of voters.
$$\sqrt{[voters]}$$
This one treats the whole world outside the EBU as a single country, using the same formula as the rest. Unlike the first method, it doesn't transform the margins.
