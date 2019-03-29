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
     * Find the right number of clusters and run K-means.
     * The right number of clusters is found using the elbow method.
     * @param int|null $maxIterations The maximum number of iterations, null to run the algorithm until convergence.
     * @return bool True if the right number of clusters has been found, false otherwise.
     */
    public function classifyAndOptimize(int $maxIterations = null): bool
    {
        $nbValues = count($this->values);

        $runs = [];
        $previousDelta = 0;

        for ($nbClusters = 1; $nbClusters <= $nbValues; ++$nbClusters) {
            // Run K-means and store the results
            $this->classify($nbClusters, $maxIterations);
            $runs[] = [
                $this->avgDistanceToCentroids(),
                $this->clusters,
                $this->centroids,
                $this->iteration,
            ];

            print_r([
                $this->avgDistanceToCentroids(),
                $this->clusters,
                $this->centroids,
                $this->iteration,
            ]);

            if ($nbClusters < 3) {
                continue;
            }

            // If the elbow has been reached, store the results and return
            $delta = ($runs[0][0] + $runs[2][0]) / 2 - $runs[1][0];
            if ($delta < $previousDelta) {
                $this->avgDistance = $runs[1][0];
                $this->clusters = $runs[1][1];
                $this->centroids = $runs[1][2];
                $this->iteration = $runs[1][3];
                return true;
            }

            $previousDelta = $delta;
            array_shift($runs);
        }

        return false;
    }

    /**
     * Sort a set of values into clusters using the K-means algorithm.
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

        /**
         * @todo cleanup empty clusters & centroids
         */

        ksort($this->clusters);

        return !$centroidsMoved;
    }

    /**
     * Fill the clusters with the corresponding values.
     */
    public function fillClusters()
    {
        $this->clusters = [];

        foreach ($this->values as $valueId => $value) {
            // Compute distance to all centroids
            $distances = array_map(
                function (array $centroid) use ($value): float {
                    return Vector::distance($value, $centroid);
                },
                $this->centroids
            );

            // Assign the value to the cluster with the closest centroid
            $this->clusters[array_keys($distances, min($distances))[0]][$valueId] = $value;
        }
    }

    public function moveCentroids(): bool
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
    public function newCentroid(): array
    {
        $centroids = $this->centroids;

        // Compute the distance² between each point and its closest existing centroid
        $distances = array_map(function (array $value) use ($centroids): float {
            return min(array_map(function (array $centroid) use ($value): float {
                return Vector::distance($centroid, $value);
            }, $centroids)) ** 2;
        }, $this->values);

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
     * Get the centroids.
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
