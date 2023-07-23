<?php

namespace App\Helpers;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;


class CrawlerHelper
{
    private $browser;

    public function __construct()
    {
        $client = HttpClient::create();
        $this->browser = new HttpBrowser($client);
    }

    public function crawlLinks($url, $filterSection, $filterLink, $post_id)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {

            try {
                $results = [];
                $crawler = $this->browser->request('GET', $url);

                // Lấy phần tử .entry-content
                $filterSection = ($filterSection) ? $filterSection : 'body';
                $entryContent = $crawler->filter($filterSection);

                // Lấy các liên kết trong phần tử .entry-content
                $entryContent->filter('a')->each(function ($node) use (&$results, &$filterLink, &$post_id) {
                    $link = $node->attr('href');
                    $text = $node->text();
                    if ($filterLink) {
                        if ($link === $filterLink) {
                            $results[] = [
                                'internal_links' => $link,
                                'keyword' => $text,
                                'post_id' => $post_id
                            ];
                        }
                    } else {

                        $results[] = [
                            'internal_links' => $link,
                            'keyword' => $text,
                            'post_id' => $post_id
                        ];
                    }
                });
            } catch (\Throwable $th) {
                //throw $th;
            }

            return $results;
        }
    }
}
