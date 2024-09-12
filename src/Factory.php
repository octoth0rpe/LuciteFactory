<?php

declare(strict_types=1);

namespace Lucite\Factory;

use Psr\Container\ContainerInterface;

class Factory
{
    protected ContainerInterface $container;
    protected array $setter_map;

    public function __construct(ContainerInterface $container, array $setter_map = [])
    {
        $this->container = $container;
        $this->setter_map = $setter_map;
    }

    /**
     * Register a new setter.
     *
     * @param string $setter_name The name of the setter function to look for when assembling a new object
     * @param mixed $container_key The name of the key to get from the container if the setter is found.
     *
     * @return Factory
     */
    public function registerSetter(string $setter_name, string $container_key): Factory
    {
        $this->setter_map[$setter_name] = $container_key;
        return $this;
    }

    /**
     * Assemble a new object for a given class name. Looks for setters and calls them if
     * a container key is registered for that setter name
     *
     * @param string $class_name The name of the class to create an instance of
     *
     * @return object
     */
    public function assemble(string $class_name): object
    {
        $object = new $class_name();
        return $this->call_setters($object);
    }

    /**
     * Call all registered setters on a given object.
     *
     * @param object $object to inspect for setters
     *
     * @return object
     */
    public function call_setters(object $object): object
    {
        $methods = get_class_methods(get_class($object));
        foreach ($methods as $method) {
            if (isset($this->setter_map[$method])) {
                $object->$method($this->container->get($this->setter_map[$method]));
            }
        }
        return $object;
    }
}
