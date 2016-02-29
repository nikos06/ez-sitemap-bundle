<?php
namespace Blend\EzSitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('blend:sitemap:generate')
            ->setDescription('generate/update Sitemap.xml');
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $container = $this->getContainer();
        $contentLoaderService = $container->get('blend_ez_sitemap.content');
        $locations = $contentLoaderService->loadLocations();

        $sitemap = new \DOMDocument('1.0', 'utf-8');
        $sitemap->preserveWhiteSpace = false;
        $sitemap->formatOutput = true;
        $urlSet = $sitemap->createElement('urlset');
        $sitemap->appendChild($urlSet);

        // add url blocks to sitemap xml
        //  <url>
        //    <loc>/</loc>
        //    <lastmod>2015-06-15</lastmod>
        //  </url>
        foreach( $locations as $location )
        {
            // create url block
            $urlBlock = $sitemap->createElement('url');
            $urlSet->appendChild($urlBlock);

            // create loc tag
            $loc  = $sitemap->createElement('loc');
            $urlBlock->appendChild($loc);
            $url = $container->get('router')->generate( $location );
            $locText = $sitemap->createTextNode($url);
            $loc->appendChild($locText);

            // create lastmod tag
            $lastmod = $sitemap->createElement('lastmod');
            $urlBlock->appendChild($lastmod);
            $lastmodText = $sitemap->createTextNode( $location->contentInfo->modificationDate->format('Y-m-d') );
            $lastmod->appendChild($lastmodText);
        }
        $fp = fopen('web/sitemap.xml', 'w');
        fwrite($fp, $sitemap->saveXml());
        fclose($fp);
    }
}
