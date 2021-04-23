<?php

namespace App\Library;

use Illuminate\Support\Arr;

/**
 * Class Pagination
 *
 * Utility functions for helping
 * with search pagination.
 *
 * @package App\Library
 */
class Pagination
{
    /**
     * Constructs pager links
     *
     * @param array $pager
     * @param array $params
     * @return mixed
     */
    public function generatePagerQueries($pager, $params = [])
    {
        unset($params['page']);
        foreach ($pager as $key => $pagerLink) {
            if (!empty($pagerLink)) {
                [$linkParam, $linkValue] = explode('=', $pagerLink);
                $params[$linkParam] = $linkValue;
                $pager[$key] = http_build_query($params);
            } else {
                $pager[$key] = '';
            }
        }
        return $pager;
    }

    /**
     * Clear unwanted sorting parameters
     *
     * @param array $params
     * @param array $keys
     * @return mixed
     */
    public function clearParams($params, $keys = [])
    {
        return http_build_query(Arr::except($params, $keys));
    }
}
