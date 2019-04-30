# php-math

A maths PHP library that can be used for machine learning.

[![Build Status](https://travis-ci.com/IngeniozIT/php-math.svg?branch=master)](https://travis-ci.com/IngeniozIT/php-math)
[![Code Coverage](https://codecov.io/gh/IngeniozIT/php-math/branch/master/graph/badge.svg)](https://codecov.io/gh/IngeniozIT/php-math)

This library contains :

- [A k-means algorithm](#kmeans)
- [Random number generators](#random)
- [Basic vector operations](#vector)
- [Activation functions for ML](#activationfunction)

[Technical details](#technical-details) about the algorithm implementations.

# API

## KMeans
Implementation of the k-means algorithm.

```php
use IngeniozIT\Math\KMeans;
```

### construct(array &$values)

```php
// The list of vectors you want to clusterize
$myValues = [
	[9, 8, 7, 6],
	[5, 4, 3, 2],
	[1, 0, -1, -2]
];

$kMeans = new KMeans($myValues);
```

### classifyAndOptimize(int $maxIterations = null): bool
Run K-means while finding the right number of clusters.

The right number of clusters is found using the elbow method. [More about that.](#elbow-method-implementation)

Returns true if the clustering strongly converged.

### classify(int $nbClusters, int $maxIterations = null): bool
Sort the values into clusters using the K-means algorithm.

This implementation uses the k-means++ algorithm and automatically replaces empty clusters.[More about that.](#k-means-implementation)

Returns true if the clustering strongly converged.

### values(): array
Get the values given to KMeans.

### nbClusters(): ?int
Get the number of clusters found in the previous run.

### avgDistanceToCentroids(): ?float
Get the average distance from each point to its corresponding cluster's centroid.

### clusters(): ?array
Get each cluster's content.

### centroids(): ?array
Get the centroids cordinates.

## Random
Random number generators.

```php
use IngeniozIT\Math\Random;
```

### static frand(float $min = 0.0, float $max = 1.0): float
Get a random float number between two values.

### static nrand(float $mean, float $stdDeviation): float
Get a random float number between two values with a normal distribution.

## Vector
Basic vector operations.

```php
use IngeniozIT\Math\Vector;
```

:star: This class could be completed with more vector operations (multiplication, substraction, angle calculation, ...). Feel free to join in.

### static distance(array $pointA, array $pointB): float
Get the distance between two points.

### static length(array $vector): float
Get the length of a vector.

### static sum(array $points): array
Get the sum of multiple vectors.

### static scalarDiv(array $vector, float $nb): array
Divide a vector by a scalar value.

### static mean(array $points): array
Get the average of several points.

## ActivationFunction
Activation functions for machine learning.

```php
use IngeniozIT\Math\ActivationFunction;
```
- identity
- binaryStep
- sigmoid
- tanh
- relu
- leakyRelu
- gaussian

# Technical details

## K-means implementation
- [Wikipedia - k-means clustering](https://en.wikipedia.org/wiki/K-means_clustering)
- [Wikipedia - K-means++](https://en.wikipedia.org/wiki/K-means%2B%2B)

Just a classic k-means algorithm that uses k-means++ to find the initial position of its centroids. It also searches for a new centroid whenever an empty cluster has been found (it should not happen, but it has been taken care of anyway). That's quite it.

## Elbow method implementation
[Wikipedia - Elbow method (clustering)](https://en.wikipedia.org/wiki/Elbow_method_(clustering))

The algorithm uses the elbow method to determine if the right number of clusters has been found :
- It classifies the values given with an increasing number of clusters.
- The elbow method is used at every iteration (so that it can stop searching when it found a realiably optimal number of clusters).
- In order to be considered reliable, a number of clusters must give the best results for `$nbClusters / M_E` iterations. If it does, it is considered that the algorithm has converged.

### Why using Euler's number ?
It's a *magic* number. You can see an example in this video :
[![Vsauce2 - The Game You Quit](https://img.youtube.com/vi/OeJobV4jJG0/0.jpg)](https://www.youtube.com/watch?v=OeJobV4jJG0)