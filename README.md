# Eurovision Condorcet

This program a new voting system I designed for the Eurovision Song Contest. It can determine the results under this new system using a set of votes as input.

Thank you to Julien Boudry for writing Condorcet, a PHP library that this program is hugely dependent on.

## Instructions
Before using for the first time, execute the following command to get the required libraries.
```shell
composer update
```

The syntax to run the program is `php src/main.php [-v|-q|-N] [cvotes file] [method]`. The `.cvotes` file is required, but the other arguments are optional.
An example with an included file is this: `php src/main.php some-votes.cvotes`.

Use argument `-v` to make the output more verbose, or `-q` to make it more quiet. Use `-N` as argument to do narrative mode.

The `.cvotes` file contains the list of candidates and the list of votes. All votes should be tagged with a 3-letter country code to show the country of origin.

Using `grand-final-votes.cvotes` is currently not recommended, as the large number of votes makes the process slow and memory-intensive.

Countries eligible to vote are written in `voting-countries.json` as 3-letter codes.
## Methods
These methods are designed to balance between Eurovision tradition and principles of social choice theory that are accepted elsewhere.

Currently, both methods are written to give equal share of aggregate voting power to public votes (tagged `Public`) and jury votes (tagged `Jury`). Eventually, there will be parameters to set the balance between these groups, and the option to add a third group.
### Eurovision Schulze
The first method, called 'Eurovision Schulze' or 'Grand Final 1.5 root' gives each participating country a total voting strength proportional to the following formula:
$$\sqrt[3]{[voters]×[population]}$$
Therefore, if every country has the same ratio of voters to population, the weight per voter would be proportional to $\frac{1}{\sqrt[1.5]{[voters]}}$.
This may later be modified to give voters outside the EBU the same voting weight as the lowest in a participating country.
This method transforms the voting margins from each country's votes to make it further from 0, closer to unanimity.
### Eurovision Schulze 2
The second method, called 'Eurovision Schulze 2' or 'Grand Final square root', is less proportional, closer to equal representation. It gives each country voting power proportional to the square root of number of voters.
$$\sqrt{[voters]}$$
This one treats the whole world outside the EBU as a single country, using the same formula as the rest. Unlike the first method, it doesn't transform the margins.

## Narrative Mode:
Run this program with the `-N` argument to try Narrative mode.

```php src/main.php -N [cvotes file]```

Narrative mode simulates how the results may be revealed in a Eurovision Song Contest using one of the voting methods included here. The votes from each country are added in cycles, with the updated results being shown every time. The numbers shown are the beatpath margin from the frontrunner.

## Files
Here are some files that can be modified to set parameters.
* `populations.json`: Includes populations of countries. Like other files in this program, countries are written as ISO 3166-1 alpha-3 codes.
An additional file will later be added to set the balance between public votes, jury votes, and a third group.