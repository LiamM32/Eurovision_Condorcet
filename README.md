
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
>
> The aim of these skeletons is to provide an example of implementation and execution without having to fork Condorcet. Modules living in their own namespace.  
> Not to produce documentation of what can be done in the code. To do this, refer to the [official documentation](https://www.condorcet.io) and to the [methods that exist natively](#examples-of-real-implementations) in the Condorcet project, which use exactly the same internal API.


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

## Examples of real implementations

### Methods (selection)
- All Natively implemented methods / https://github.com/julien-boudry/Condorcet/tree/master/src/Algo/Methods
- Dogson Quick, a simple pairwise-based method / https://github.com/julien-boudry/Condorcet/blob/master/src/Algo/Methods/Dodgson/DodgsonQuick.php
- Borda Count is a vote-oriented method / https://github.com/julien-boudry/Condorcet/blob/master/src/Algo/Methods/Borda/BordaCount.php
- Single Transferable Vote, a proportional method / https://github.com/julien-boudry/Condorcet/blob/master/src/Algo/Methods/STV/SingleTransferableVote.php

### Constraints
- **NoTie**: https://github.com/julien-boudry/Condorcet/blob/master/src/Constraints/NoTie.php
