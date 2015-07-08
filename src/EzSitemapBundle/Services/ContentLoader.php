<?php

namespace Blend\EzSitemapBundle\Services;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Symfony\Component\DependencyInjection\ContainerAware;
use eZ\Publish\API\Repository\SectionService;

class ContentLoader extends ContainerAware
{
    private $searchService = null;
    private $locationService = null;
    private $sectionService;

    public function __construct( $locationService, $searchService, SectionService $sectionService )
    {
        $this->searchService = $searchService;
        $this->locationService = $locationService;
        $this->sectionService = $sectionService;
    }

    /**
     * TODO: handle different sitemap definitions for multiple sitemaps (e.g. news, video, img)
     */
    public function loadLocations()
    {
        $contentTypes = $this->container->getParameter( 'blend_sitemap.content_types' );
        $allowedSections = $this->container->getParameter( 'blend_sitemap.allowed_sections' );
        $allowedSectionIds = array();
        foreach ( $allowedSections as $sectionIdentifier )
        {
            $allowedSectionIds[] = $this->sectionService->loadSectionByIdentifier( $sectionIdentifier )->id;
        }

        $query = new Query();

        $query->criterion = new LogicalAnd( [
            new Criterion\ContentTypeIdentifier( $contentTypes ),
            new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
            new Criterion\SectionId( $allowedSectionIds )
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
