<?php
declare(strict_types = 1);

namespace IngeniozIT\Math;

use IngeniozIT\Math\Random;
use IngeniozIT\Math\Vector;

class KMeans
{
    protected $values;

    public function __construct(array &$values)
    {
        $this->values = $values;
    }

    // Current run

    protected $avgDistance;
    protected $clusters = [];
    protected $centroids = [];
    protected $iteration = 0;

    /**
     * Run K-means while finding the right number of clusters.
     * The right number of clusters is found using the elbow method.
     * @param int|null $maxIterations The maximum number of iterations, null to run the algorithm until convergence.
     * @return bool True if the right number of clusters has been found, false otherwise.
     */
    public function classifyAndOptimize(int $maxIterations = null): bool
    {
        // Run with 1 cluster
        $this->classify(1, $maxIterations);

        $avgDist = $this->avgDistanceToCentroids();
        // All values are equal, only 1 cluster is needed
        if ($avgDist == 0) {
            return true;
        }

        $nbValues = count($this->values);
        $runs = [];
        $avgDistances = [];
        $bestClustersNb = 2;
        $bestClustersNbCount = 0;
        for ($nbClusters = 2; $nbClusters < $nbValues; ++$nbClusters) {
            $this->classify($nbClusters, $maxIterations);
            $runs[$nbClusters] = [
                $this->avgDistanceToCentroids(),
                $this->clusters,
                $this->centroids,
                $this->iteration,
            ];
            $avgDistances[$nbClusters] = $runs[$nbClusters][0];

            if ($nbClusters < min(4, $nbValues)) {
                continue;
            }

            $bestNb = $this->findElbow($avgDist, $avgDistances);

            if ($bestNb === $bestClustersNb) {
                if (ceil(count($avgDistances) / M_E) <= $bestClustersNbCount++) {
                    $this->avgDistance = $runs[$bestNb][0];
                    $this->clusters = $runs[$bestNb][1];
                    $this->centroids = $runs[$bestNb][2];
                    $this->iteration = $runs[$bestNb][3];
                    return true;
                }
            } else {
                foreach ($avgDistances as $nbClust => $dist) {
                    if ($nbClust >= $bestNb) {
                        break;
                    }
                    unset($runs[$nbClust]);
                    unset($avgDistances[$nbClust]);
                }
                $bestClustersNb = $bestNb;
                $bestClustersNbCount = 0;
            }
        }

        $this->avgDistance = $runs[$bestClustersNb][0];
        $this->clusters = $runs[$bestClustersNb][1];
        $this->centroids = $runs[$bestClustersNb][2];
        $this->iteration = $runs[$bestClustersNb][3];

        return false;
    }

    protected function findElbow(float $firstDist, array $distances): int
    {
        $distKeys = array_keys($distances);
        $lastKey = $distKeys[count($distKeys) - 1];

        $maxDist = 0;
        $bestClusters = 0;
        foreach ($distances as $nbClusters => $mse) {
            if ($nbClusters >= $lastKey) {
                break;
            }
            $dist = $this->distanceFromLine([$nbClusters, $mse], [1, $firstDist], [$lastKey, $distances[$lastKey]]);
            if ($dist >= $maxDist) {
                $maxDist = $dist;
                $bestClusters = $nbClusters;
            }
        }

        return $bestClusters;
    }

    protected function distanceFromLine(array $point, array $startLine, array $endLine): float
    {
        $A = -1 * ($endLine[1] - $startLine[1]) / ($endLine[0] - $startLine[0]);
        $B = 1;
        $C = -1 * $startLine[1];

        return abs($A * $point[0] + $B * $point[1] + $C) / (($A ** 2 + $B ** 2) ** 0.5);
    }

    /**
     * Sort the values into clusters using the K-means algorithm.
     * @param int $nbClusters The number of clusters.
     * @param int|null $maxIterations The maximum number of iterations, null to run the algorithm until convergence.
     * @return bool True if K-means has converged, false otherwise.
     */
    public function classify(int $nbClusters, int $maxIterations = null): bool
    {
        $this->iteration = 0;
        $this->clusters = [];
        $this->centroids = [];
        $this->avgDistance = null;

        // Why would you look for less than 1 cluster ?
        if ($nbClusters < 1) {
            throw new \InvalidArgumentException('Number of clusters must be at least 1.');
        }

        $nbValues = count($this->values);

        // Initialize centroids on random points

        // First centroid is totally random
        $this->centroids = [
            $this->values[array_keys($this->values)[rand(0, $nbValues - 1)]]
        ];
        // Next centroids use the k-means++ algorithm
        for ($i = $nbClusters - 1; $i; --$i) {
            $this->centroids[] = $this->newCentroid();
        }

        // Repeat until the centroids stop moving or $maxIterations is reached
        do {
            ++$this->iteration;
            // Assign each value to a cluster
            $this->fillClusters();
            // Move centroids
            $centroidsMoved = $this->moveCentroids();
        } while (true === $centroidsMoved && (null === $maxIterations || $this->iteration < $maxIterations));

        ksort($this->clusters);

        return !$centroidsMoved;
    }

    /**
     * Fill the clusters with the corresponding values.
     */
    protected function fillClusters()
    {
        $this->clusters = [];

        foreach ($this->values as $valueId => $value) {
            // Compute distance to all centroids
            $distances = [];
            foreach ($this->centroids as $centrId => $centroid) {
                $distances[$centrId] = Vector::distance($value, $centroid);
            }

            // Assign the value to the cluster with the closest centroid
            $this->clusters[array_keys($distances, min($distances))[0]][$valueId] = $value;
        }

        foreach ($this->clusters as &$cluster) {
            ksort($cluster);
        }
    }

    protected function moveCentroids(): bool
    {
        $centroidsMoved = false;
        foreach ($this->clusters as $clusterId => $cluster) {
            if (empty($cluster)) {
                // The cluster is empty, respawn its centroid
                unset($this->centroids[$clusterId]);
                $this->centroids[$clusterId] = $this->newCentroid();
                $centroidsMoved = true;
            } elseif (!$centroidsMoved) {
                // Place the centroid in the middle of all the points of the cluster
                $newCentroid = Vector::mean($cluster);
                if (0.0 !== Vector::distance($newCentroid, $this->centroids[$clusterId])) {
                    $this->centroids[$clusterId] = $newCentroid;
                    $centroidsMoved = true;
                }
            }
        }

        return $centroidsMoved;
    }

    /**
     * Get a new centroid using the k-means++ algorithm.
     * @return array The coordinates of the new centroid.
     */
    protected function newCentroid(): array
    {
        $centroids = $this->centroids;

        // Compute the distance² between each point and its closest existing centroid
        $distances = [];
        foreach ($this->values as $valueId => $value) {
            $minDist = null;
            foreach ($centroids as $centroid) {
                $dist = Vector::distance($centroid, $value);
                $minDist = null === $minDist ? $dist : min($minDist, $dist);
            }
            $distances[$valueId] = $minDist ** 2;
        }

        // Chose a new data point using a weighted probability distribution proportional to distance²
        $randomWeight = Random::frand(0, array_sum($distances));

        foreach ($distances as $valueId => $distance) {
            if ($randomWeight <= $distance) {
                break;
            }
            $randomWeight -= $distance;
        }

        return $this->values[$valueId];
    }

    // Getters

    /**
     * Get the values given to KMeans.
     * @return array The values given to the constructor.
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * Get the number of clusters found in the previous run.
     * @return int|null Null if the algorithm has not run yet, the number of clusters found otherwise.
     */
    public function nbClusters(): ?int
    {
        if (0 === $this->iteration) {
            return null;
        }

        return count($this->clusters);
    }

    /**
     * Get the average distance from each point to its corresponding cluster's centroid.
     * @return float|null Null if the algorithm has not run yet, the average distance otherwise.
     */
    public function avgDistanceToCentroids(): ?float
    {
        if (0 === $this->iteration) {
            return null;
        }

        if (null === $this->avgDistance) {
            $sum = 0;
            foreach ($this->clusters as $clusterId => $cluster) {
                foreach ($cluster as $point) {
                    $sum += Vector::distance($this->centroids[$clusterId], $point);
                }
            }
            $this->avgDistance = $sum / count($this->values);
        }

        return $this->avgDistance;
    }

    /**
     * Get each cluster's content.
     * @return array|null Null if the algorithm has not run yet,
     * [cluster_id_1 => [value_id_1, value_id_2, ...]] otherwise.
     */
    public function clusters(): ?array
    {
        if (0 === $this->iteration) {
            return null;
        }

        return array_map('array_keys', $this->clusters);
    }

    /**
     * Get the centroids cordinates.
     * @return array|null Null if the algorithm has not been run yet, the coordinates the each cluster otherwise.
     */
    public function centroids(): ?array
    {
        if (0 === $this->iteration) {
            return null;
        }

        return $this->centroids;
    }
}
