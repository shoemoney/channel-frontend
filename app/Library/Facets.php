<?php

namespace App\Library;

/**
 * Class Facets
 * @package App\Library
 */
class Facets
{
    /** @var array */
    protected $facetMap = [
        'date_recorded' => [
            'label' => 'Year Recorded',
            'type' => 'date'
        ],
        'in_playlists' => [
            'label' => 'Playlist',
            'type' => 'term'
        ],
        'speakers' => [
            'label' => 'People',
            'type' => 'term'
        ],
        'topics' => [
            'label' => 'Topic',
            'type' => 'term'
        ],
        'tags' => [
            'label' => 'Tags',
            'type' => 'term'
        ]
    ];

    /**
     * Build an array of available facet options for filtering
     *
     * @param $aggregations
     * @return array
     */
    public function getFacetOptions($aggregations)
    {
        if (isset($aggregations['global'])) {
            $aggregations = $aggregations['global'];
            unset($aggregations['doc_count']);
        }

        $facetOptions = [];
        foreach ($aggregations as $facet => $facetData) {
            if (array_key_exists($facet, $this->facetMap)) {
                $facetOptions[$facet]['items'] = $this->processFacetData($facetData['buckets']);
                $facetOptions[$facet]['label'] = $this->facetMap[$facet]['label'];
                $facetOptions[$facet]['type'] = $this->facetMap[$facet]['type'];
                $facetOptions[$facet]['id'] = $facet;
            }
        }
        return $facetOptions;
    }

    /**
     * Helper to extract necessary facet data.
     */
    private function processFacetData($buckets)
    {
        $processedBuckets = [];
        foreach ($buckets as $index => $value) {
            $bucket = [];
            if ($value['key']) {
                $bucket['count'] = $value['doc_count'];
                $bucket['key'] = $value['key'];
                if (isset($value['key_as_string'])) {
                    $bucket['key_as_string'] = $value['key_as_string'];
                }
                $processedBuckets[] = $bucket;
            }
        }
        return $processedBuckets;
    }

    /**
     * Deconstruct URL parameters to build a query string for the API
     *
     * @param $params
     * @return string
     */
    public function getFacetQueryString()
    {
        $queryParams = $_SERVER["QUERY_STRING"];
        $facets = implode('|', array_keys($this->facetMap));
        $re = "/(?<=^|&)(" . $facets . ")(?==)/";
        parse_str(preg_replace($re, "$1[]", $queryParams), $params);
        return http_build_query($params);
    }
}
