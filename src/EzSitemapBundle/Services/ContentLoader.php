<?php

namespace Blend\EzSitemapBundle\Services;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Symfony\Component\DependencyInjection\ContainerAware;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;


class ContentLoader extends ContainerAware
{
    private $searchService = null;
    private $locationService = null;
    private $sectionService;

    public function __construct($locationService, $searchService, SectionService $sectionService)
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
        $query = new LocationQuery();
        $queryCriteria = [
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
        ];

        $contentTypeCriterion = $this->generateContentTypeCriterion(
            $this->container->getParameter('blend_ez_sitemap.allowed_content_types')
        );
        if ($contentTypeCriterion !== null) {
            $queryCriteria[] = $contentTypeCriterion;
        }

        $sectionCriterion = $this->generateSectionCriterion(
            $this->container->getParameter('blend_ez_sitemap.allowed_sections')
        );
        if ($sectionCriterion !== null) {
            $queryCriteria[] = $sectionCriterion;
        }

        $query->query = new LogicalAnd($queryCriteria);

        $query->sortClauses = [
//            new SortClause\LocationPathString(Query::SORT_ASC)
        ];
        $list = $this->searchService->findLocations($query);

        $results = [];
        foreach ($list->searchHits as $location) {
            $locationId = $location->valueObject->id;
            $results[] = $this->locationService->loadLocation($locationId);
        }

        return $results;
    }

    private function generateContentTypeCriterion(array $contentTypes = null)
    {
        if ($contentTypes && !empty($contentTypes)) {
            return new Criterion\ContentTypeIdentifier($contentTypes);
        }
        return null;
    }

    private function generateSectionCriterion(array $sectionIdentifiers = null)
    {
        // if no sections are allowed, allow all
        if ($sectionIdentifiers && !empty($sectionIdentifiers)) {
            $allowedSections = $sectionIdentifiers;
            $allowedSectionIds = [];

            foreach ($allowedSections as $sectionIdentifier) {
                if (!is_numeric($sectionIdentifier)) {
                    $allowedSectionIds[] = $this->sectionService->loadSectionByIdentifier($sectionIdentifier)->id;
                } else {
                    $allowedSectionIds[] = $sectionIdentifier;
                }
            }
            return new Criterion\SectionId($allowedSectionIds);
        }
        return null;
    }
}
