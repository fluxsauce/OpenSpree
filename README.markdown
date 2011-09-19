# OpenSpree

*OpenSpree* is a fairly complete hot-seat adaptation of *[Spree!](http://cheapass.com/freegames/spree)*, a board game about looting a shopping mall, implemented in object-oriented PHP and deployed to Orchestra.io.

Some minor features and edge cases are missing, but the game is playable and winnable.

Keep checking back for new features like AI players, asynchronous play (less page loads), blood spatters, Hong Kong rules, new official custom art straight from the game designers, and more!

## Overview

Springtime. Midnight. The Mall is beckoning.

Welcome to LeGrand Mall, the oldest and most poorly secured shopping mall in the world. It's as leakproof as the Titanic and as solid as a wet paper bag. This place just begs to be robbed.

Enter you. And your infantile collection of friends.

It's not enough to simply loot this mall. You have to do it with "flair." Which boils down to an allnight looting race with shopping carts, flashlights, and guns.

So park your car, dash into the mall, and load up your little red wagon. You can snitch stuff out of the stores if you like, but it's even more fun to steal it from your friends.

This explains the guns.

## Known issues

OpenSpree is under active development; at this time, a game is able to be completed, but stop cards have not yet been implemented.  In short, I need to figure out how to allow other players to have an opportuntity to interrupt an action without disclosing what cards they have, yet still obeying the rules of the game.  Artifical intelligence players won't have that problem.

## Want to play?

A playable copy of OpenSpree has been deployed to http://openspree.orchestra.io/ using the GitHub repository as the origin. 

## Requirements

* PHP 5 or higher.

Future versions may require an Apache web server.

## Contributed code

* [PHP-Dijktra](https://github.com/kay/PHP-Dijkstra) by doug@neverfear.org is an implementation of Dijkstra's shortest path-algorithm. 

## Contributing

The OpenSpree source code is [hosted on GitHub](https://github.com/fluxsauce/OpenSpree).

Please use the [issue tracker](https://github.com/fluxsauce/OpenSpree/issues) if you find any bugs or wish to contribute.

## License

*Spree!* is (c) and TM 1997, 2011 James Ernest and Cheapass Games: [www.cheapass.com](http://www.cheapass.com). Spree was released on 2011.09.15 under the [CC BY-NC-ND 3.0](http://creativecommons.org/licenses/by-nc-nd/3.0/) license by James Ernest.  Permission for the development of OpenSpree was given in writing from author James Ernest.

*OpenSpree* is [CC BY-NC-ND 3.0](http://creativecommons.org/licenses/by-nc-nd/3.0/) by [Jon Peck](http://theconfluence.org) and [FluxSauce](http://fluxsauce.com).