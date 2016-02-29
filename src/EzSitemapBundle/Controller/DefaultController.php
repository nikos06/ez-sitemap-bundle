<?php

namespace Blend\EzSitemapBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\MVC\Symfony\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    /**
     *
     * Show a sitemap
     * TODO: Generate & store file
     * TODO: set stored file location
     * TODO: read already stored file (if not out of date)
     *
     * @return Response
     */
    public function indexAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge( 86400 );
        $response->headers->set( 'X-Location-Id', 2 );
        $response->headers->set( 'Content-Type', 'application/xml' );

        $rootUrl =  $this->container->getParameter('blend_ez_sitemap.main_uri');

        $contentLoaderService = $this->container->get('blend_ez_sitemap.content');

        $locations = $contentLoaderService->loadLocations();

        return $this->render(
            'BlendEzSitemapBundle:Default:index.xml.twig',
            [ 'results' => $locations, 'rootUrl' => $rootUrl ],
            $response
        );
    }

}
