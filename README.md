# Eurovision Condorcet

This program is a demonstration of a new voting system I designed for the Eurovision Song Contest. Using a set of votes as the input, this program can determine the results under this system.

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
These methods are designed to balance between Eurovision tradition and principles of social choice theory that are accepted elsewhere. The voting power given to each country is a compromise between proportionate representation and equal representation.

They are written so that the balance of voting power can be divided into multiple groups which overlap with countries, such as 'Public' and 'Jury'.
### Eurovision Schulze 0
The default method, called 'Eurovision Schulze 0' or 'Grand Final 0', gives each country within each group voting power  proportional to the 1.5 root of the number of voters. Therefore, the weight per voter in each country is proportional to $\frac{1}{\sqrt[1.5]{[voters]}}$.
### Eurovision Schulze 1
This was originally the default method. It's called 'Eurovision Schulze 1' or 'Grand Final 1'. It's similar to the one above, but accounts for population of each country, not just number of voters. It gives each participating country a total voting strength proportional to the following formula:
$$\sqrt[3]{[voters]×[population]}$$
This may later be modified to give voters outside the EBU the same voting weight as the lowest in a participating country.
This method transforms the voting margins from each country's votes to make it further from 0, closer to unanimity.
### Eurovision Schulze 2
This method, called 'Eurovision Schulze 2' or 'Grand Final square root', is less proportional, closer to equal representation. It gives each country voting power proportional to the square root of number of voters.
$$\sqrt{[voters]}$$
This one treats the whole world outside the EBU as a single country, using the same formula as the rest. Unlike the first method, it doesn't transform the margins.

## Narrative Mode:
Run this program with the `-N` argument to try Narrative mode.

```php src/main.php -N [cvotes file]```

Narrative mode simulates how the results may be revealed in a Eurovision Song Contest using one of the voting methods included here. The votes from each country are added in cycles, with the updated results being shown every time. The numbers shown are the beatpath margin from the frontrunner.

To make narrative mode go faster, use `-f` as an argument.

## Input File Format
The file format this program takes as input is the [Condorcet Election Format](https://github.com/CondorcetVote/CondorcetElectionFormat), but with some new parameters which are not part of the standard format. The extention for this file format is `.cvotes`.

Parameters are usually written in the first few lines starting with `#/`. The file should have a line that specifies the candidates (entries) in the contest. It is recommended that they are written as 3-letter country codes. They must be separated by semicolons. Example:
```
#/Candidates: ALB; AUS; AUT; BEL; CHE; CYP; CZE; DEU; ESP; EST; FIN; FRA; GBR; HRV; ISR; ITA; LTU; MDA; NOR; PRT; SRB; SVN; SWE; UKR
```
If you want to divide voting power between multiple groups with a specified stake, such as the public & the jury in the current Eurovision Song Contest, you can have a line like the following to specify group balance:
```
#/Group balance: Public=0.5; Jury=0.5
```
If 'Public' isn't explicitly listed, and all groups have a combined weight of less than 1, than a group called 'Public' will be automatically created with a weight of 1 minus the sum of the others.

Votes are written as a set of tags, and a ranking of candidates, like the following:
```
Jury, ALB || SWE>SVN>CHE>FRA>CYP>ESP>BEL>ISR>HRV>LTU>MDA>GBR>PRT>SRB>ITA>FIN>EST>AUS>CZE>UKR>NOR>DEU>AUT
```
The tags are listed before the `||`. Remember to separate each tag by a comma, not a semicolon. Each vote should have the country it originates from written as a 3-letter country code in the tags. If a recognized voting country isn't stated as a tag, it will automatically be put under the 'WLD' tag. You can also specify the voting group that it's part of. If not, it will automatically be assigned as 'Public'. You can generate multiple copies of the vote by writing `*[number]` at the end of the line.

## Parameter Files
Here are some files that can be modified to set parameters.
* `populations.json`: Includes populations of countries. Like other files in this program, countries are written as ISO 3166-1 alpha-3 codes.
* `voting-countries.json`: Specifies which countries are participating with their own national jury. Public votes may be from outside these countries, but they will be part of the _world_ vote, & automatically tagged `WLD`. Countries are written as ISO 3166-1 alpha-3 codes.

An additional file will later be added to set the balance between public votes, jury votes, and a third group.