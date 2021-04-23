<?php

namespace App\Http\Controllers;

use App\Api;
use App\Library\MetatagHelper;
use Illuminate\Http\Request;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /** @var Api */
    protected $api;

    /** @var MetatagHelper */
    protected $metatagHelper;

    /**
     * HomeController constructor.
     * @param Api $api
     * @param MetatagHelper $metatagHelper
     */
    public function __construct(
        Api $api,
        MetatagHelper $metatagHelper
    ) {
        $this->api = $api;
        $this->metatagHelper = $metatagHelper;
    }

    /**
     * Fetches our custom front page listing.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $data = $this->api->request('search/aggregate/topics');

        $this->setMeta($data);

        return view('main', [
            'state' => $this->getAppState($data, $params),
        ]);
    }

    /**
     * Return a JSON version of the index action
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexJson(Request $request)
    {
        $params = $request->all();
        $videos = $this->api->request('search/aggregate/topics');
        $state = $this->getAppState($videos, $params);
        return response()->json($state);
    }

    /**
     * Get JSON of the app state to inject into
     * the head of the page to hydrate initial request.
     *
     * @param $data
     * @param $params
     * @return array
     */
    public function getAppState($data, $params)
    {
        return [
            'path' => '/',
            'videos' => isset($data['data']) ? $data['data'] : [],
        ];
    }

    /**
     * @param $data
     * @return array
     */
    public function setMeta()
    {
        $imageUrl = $this->metatagHelper->getImageUrl();
        $description = config('app.description');
        $title = config('app.name');
        $pageUrl = $this->metatagHelper->getCurrentUrl();

        meta()
            ->set('title', $title)
            ->set('canonical', $pageUrl)
            ->set('description', $description)
            ->set('og:description', $description)
            ->set('twitter:url', $pageUrl)
            ->set('twitter:title', $title)
            ->set('twitter:description', $description)
            ->set('twitter:image', $imageUrl)
            ->set('twitter:card', 'summary_large_image')
            ->set('og:url', $pageUrl)
            ->set('og:title', $title)
            ->set('og:description', $description)
            ->set('og:type', 'website')
            ->set('og:image', $imageUrl)
            ->set('og:image:type', 'image/jpg')
            ->set('og:image:width', 320)
            ->set('og:image:height', 180);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestionsJson()
    {
        $cannedTerms = [];
        $featuredPlaylist = $this->api->request('playlists/Featured');
        if (isset($featuredPlaylist['success']) && $featuredPlaylist['success']) {
            $playlistData = $featuredPlaylist['data'];
            foreach ($playlistData['videos'] as $video) {
                if (!empty($video['topics'])) {
                    foreach ($video['topics'] as $topic) {
                        $cannedTerms[] = [
                            'term'=> $topic,
                            'query' => [
                                'topics' => $topic
                            ]
                        ];
                    }
                }
                if (!empty($video['tags'])) {
                    foreach ($video['tags'] as $tag) {
                        $cannedTerms[] = [
                            'term' => $tag,
                            'query' => [
                                'tags' => $tag
                            ]
                        ];
                    }
                }
                if (isset($video['people'])) {
                    foreach ($video['people'] as $person) {
                        $cannedTerms[] = [
                            'term' => $person,
                            'query' => [
                                'speakers' => $person
                            ]
                        ];
                    }
                }
            }
        }
        // Randomise and limit to 12
        if (!empty($cannedTerms)) {
            shuffle($cannedTerms);
            $cannedTerms = array_slice($cannedTerms, 0, 12);
        }
        return response()->json($cannedTerms);
    }
}
