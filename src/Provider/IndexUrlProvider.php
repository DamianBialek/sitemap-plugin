<?php

declare(strict_types=1);

namespace SitemapPlugin\Provider;

use SitemapPlugin\Factory\IndexUrlFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

final class IndexUrlProvider implements IndexUrlProviderInterface
{
    /** @var UrlProviderInterface[] */
    private array $providers = [];

    private RouterInterface $router;

    private IndexUrlFactoryInterface $sitemapIndexUrlFactory;

    /** @var array */
    private $paths = [];
  
    private $urls = [];

    public function __construct(
        RouterInterface $router,
        IndexUrlFactoryInterface $sitemapIndexUrlFactory
    ) {
        $this->router = $router;
        $this->sitemapIndexUrlFactory = $sitemapIndexUrlFactory;
    }

    public function addProvider(UrlProviderInterface $provider, array $paths = []): void
    {
        $this->providers[] = $provider;
        $this->paths[$provider->getName()] = $paths;
    }

    public function generate(): iterable
    {
        foreach ($this->providers as $provider) {
            $pathCount = count($this->paths[$provider->getName()]);

            for ($i = 0; $i < $pathCount; $i++) {
                $params = ['index' => $i];

                $location = $this->router->generate('sylius_sitemap_'.$provider->getName(), $params);

                $this->urls[] = $this->sitemapIndexUrlFactory->createNew($location);
            }
          
        }

        return $this->urls;
    }
}
