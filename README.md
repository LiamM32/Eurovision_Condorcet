
<p align="center">
  <img src="https://raw.githubusercontent.com/CondorcetVote/Condorcet_Modules_Skeletons/master/condorcet-black.png" alt="Condorcet Logo">
</p>

- **[Condorcet PHP project](https://github.com/julien-boudry/Condorcet)**
- **[Condorcet PHP documentation](https://www.condorcet.io)**

> Skeleton to develop your own Condorcet module.
> 
> Your own methods for resolving election results using Condorcet as an election framework for preferential voting.
> Also includes a template to develop your own voting constraints.
> Does not contain example drivers for external storage, please refer to the official documentation (advice also valid for other types of modules).

## Installation

```shell
composer update
```

## Execute all tests
```shell
composer test 
```

Or:
```shell
vendor/bin/phpunit
```

## Execute a script example based on your method

```shell
php src/main.php
```

## Execute as the command line version
```shell
php bin/CondorcetCommandLine.php
```