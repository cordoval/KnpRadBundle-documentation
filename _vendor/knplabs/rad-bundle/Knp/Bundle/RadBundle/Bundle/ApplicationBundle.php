<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\Bundle;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

use Knp\Bundle\RadBundle\Extension\ApplicationExtension;
use Knp\Bundle\RadBundle\HttpKernel\RadKernel;

/**
 * Application bundle.
 *
 * Each application have only one application bundle.
 * It's the place of all application-related code.
 */
class ApplicationBundle extends ContainerAware implements BundleInterface
{
    protected $name;
    protected $parent;
    protected $namespace;
    protected $rootDir;
    protected $path;
    protected $extension;

    /**
     * Initializes bundle.
     *
     * @param string      $namespace Application namespace
     * @param string      $rootDir   Src dir path
     * @param string|null $parent    Parent bundle name (if has one)
     */
    public function __construct($namespace, $rootDir, $parent = null)
    {
        $this->namespace = $namespace;
        $this->rootDir   = $rootDir;
        $this->name      = preg_replace("/^(?:.*\\\)?([^\\\]+)$/", '$1', $namespace);
        $this->parent    = $parent;
    }

    /**
     * Boots the application.
     */
    public function boot()
    {
    }

    /**
     * Shutdowns the application.
     */
    public function shutdown()
    {
    }

    /**
     * Builds the application.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
    }

    /**
     * Returns the application's container extension.
     *
     * If application has DependencyInjection\{APP_NAME}Extension in
     * it - it will be loaded. ApplicationExtension will be loaded otherwise.
     *
     * @return ExtensionInterface The container extension
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $class = $this->getNamespace().'\\DependencyInjection\\'.$this->getName().'Extension';
            if (class_exists($class)) {
                $this->extension = new $class();
            } else {
                $this->extension = new ApplicationExtension($this->getPath());
            }
        }

        if ($this->extension) {
            return $this->extension;
        }
    }

    /**
     * Gets the application namespace.
     *
     * @return string The application namespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Gets the application directory path.
     *
     * @return string The application absolute path
     */
    public function getPath()
    {
        if (null === $this->path) {
            $this->path = sprintf('%s/%s',
                $this->rootDir, str_replace('\\', DIRECTORY_SEPARATOR, $this->getNamespace())
            );
        }

        return $this->path;
    }

    /**
     * Returns the bundle parent name.
     *
     * @return string The Bundle parent name it overrides or null if no parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the application (bundle) name (the class short name).
     *
     * @return string The application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Finds and registers Commands.
     *
     * Override this method if your bundle commands do not follow the conventions:
     *
     * * Commands are in the 'Command' sub-directory
     * * Commands extend Symfony\Component\Console\Command\Command
     *
     * @param Application $application An Application instance
     */
    public function registerCommands(Application $application)
    {
        if (!$dir = realpath($this->getPath().'/Command')) {
            return;
        }

        $finder = new Finder();
        $finder->files()->name('*Command.php')->in($dir);

        $prefix = $this->getNamespace().'\\Command';
        foreach ($finder as $file) {
            $ns = $prefix;
            if ($relativePath = $file->getRelativePath()) {
                $ns .= '\\'.strtr($relativePath, '/', '\\');
            }
            $r = new \ReflectionClass($ns.'\\'.$file->getBasename('.php'));
            if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract()) {
                $application->add($r->newInstance());
            }
        }
    }
}
