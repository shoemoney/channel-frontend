<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Api;
use App\Library\Facets;
use App\Library\Pagination;
use App\Library\MetatagHelper;
use Illuminate\Support\Str;

/**
 * Class SearchController
 * @package App\Http\Controllers
 */
class SearchController extends Controller
{
    /** @var Api */
    protected $api;

    /** @var Facets */
    protected $facetHandler;

    /** @var Pagination */
    protected $pagination;

    /** @var MetatagHelper */
    protected $metatagHelper;

    /**
     * SearchController constructor.
     * @param Api $api
     * @param Facets $facetHandler
     * @param Pagination $pagination
     * @param MetatagHelper $metatagHelper
     */
    public function __construct(
        Api $api,
        Facets $facetHandler,
        Pagination $pagination,
        MetatagHelper $metatagHelper
    ) {
        $this->api = $api;
        $this->facetHandler = $facetHandler;
        $this->pagination = $pagination;
        $this->metatagHelper = $metatagHelper;
    }

    /**
     * Perform a search or sorted/filtered search
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Request $request)
    {
        $validParams = config('constants.validQueryParams');
        $params = $request->only($validParams);
        $queryString = $this->facetHandler->getFacetQueryString();
        $results = $this->api->request('search', $queryString);
        $state = $this->getAppState($results, $request, $params, $queryString);

        $this->setMeta($state);

        return view('search', [
            'state' => $state,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewJson(Request $request)
    {
        $validParams = config('constants.validQueryParams');
        $params = $request->only($validParams);
        $queryString = $this->facetHandler->getFacetQueryString();
        $results = $this->api->request('search', $queryString);
        $state = $this->getAppState($results, $request, $params, $_SERVER["QUERY_STRING"]);
        return response()->json($state);
    }

    /**
     * @param array $data
     * @param Request $request
     * @param array $params
     * @param string $originalQuery
     * @return array
     */
    public function getAppState($data, $request, $params, $originalQuery)
    {
        $requestUrl = $request->url();

        $pagerLinks = [];
        if (!empty($data['pages'])) {
            $pagerLinks = $this->pagination->generatePagerQueries($data['pages']['pager'], $params);
        }

        $facets = [];
        if (isset($data['aggregations'])) {
            $facets = $this->facetHandler->getFacetOptions($data['aggregations']);
        }

        $term = $request->get('term');
        return [
            'path' => '/search',
            'videos' => isset($data['data']) ? $data['data'] : [],
            'term' => $term,
            'message' => false,
            'title' => !is_null($term) ? 'Serach results for "' . ucfirst($term) . '"' : '""',
            'facets' => $facets,
            'url' => $requestUrl,
            'currentQuery' => $originalQuery,
            'clearedPageQuery' => $this->pagination->clearParams($params, ['page']),
            'clearedSortQuery' => $this->pagination->clearParams($params, ['sort', 'order']),
            'show_clear' => true,
            'totals' => isset($data['pages']) ? $data['pages'] : [],
            'pagerLinks' => $pagerLinks,
        ];
    }

    /**
     * @param $data
     * @return array
     */
    public function setMeta($data)
    {
        $imageUrl = $this->metatagHelper->getImageUrl();
        $description = config('app.description');
        $term = $data['term'];
        $name = config('app.name');
        $title = !is_null($term) ? 'Search results for ' . $term . ' | ' . $name : 'Search | ' .  $name;
        $pageUrl = $this->metatagHelper->getCurrentUrl();

        meta()
            ->set('title', $title)
            ->set('description', $description)
            ->set('og:description', $description)
            ->set('og:type', 'website')
            ->set('twitter:url', $pageUrl)
            ->set('twitter:title', $title)
            ->set('twitter:description', $description)
            ->set('twitter:image', $imageUrl)
            ->set('twitter:card', 'summary')
            ->set('og:url', $pageUrl)
            ->set('og:title', $title)
            ->set('og:description', $description)
            ->set('og:image', $imageUrl)
            ->set('og:image:type', 'image/jpg')
            ->set('og:image:width', 320)
            ->set('og:image:height', 180);
    }
}
