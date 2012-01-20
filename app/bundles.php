<?php

$bundles = array(
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),
    new Symfony\Bundle\MonologBundle\MonologBundle(),
    new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
    new Symfony\Bundle\AsseticBundle\AsseticBundle(),
    new Test\BlogBundle\TestBlogBundle(),
);

if (in_array($kernel->getEnvironment(), array('dev', 'test'))) {
    $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
    $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
    $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
}

return $bundles;