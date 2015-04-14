<?php

namespace Blend\EzSitemapBundle\Services;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Symfony\Component\DependencyInjection\ContainerAware;

class ContentLoader extends ContainerAware
{
    private $searchService = null;
    private $locationService = null;

    public function __construct( $locationService, $searchService )
    {
        $this->searchService = $searchService;
        $this->locationService = $locationService;
    }

    /**
     * TODO: handle different sitemap definitions for multiple sitemaps (e.g. news, video, img)
     */
    public function loadLocations()
    {
        $contentTypes = $this->container->getParameter( 'blend_sitemap.content_types' );

        $query = new Query();

        $query->criterion = new LogicalAnd( [
            new Criterion\ContentTypeIdentifier( $contentTypes ),
            new Criterion\Visibility( Criterion\Visibility::VISIBLE )
        ] );

        $query->sortClauses = [ new SortClause\LocationPathString( Query::SORT_ASC ) ];
        $list = $this->searchService->findContent( $query );

        $results = [];
        foreach( $list->searchHits as $content )
        {
            $locationId = $content->valueObject->versionInfo->contentInfo->mainLocationId;

            $results[] = $this->locationService->loadLocation( $locationId );
        }

        return $results;
    }
}
