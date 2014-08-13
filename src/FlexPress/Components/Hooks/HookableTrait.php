<?php

namespace FlexPress\Components\Hooks;

trait HookableTrait
{

    protected $default_attributes = array("priority" => 10, "params" => 1);

    /**
     *
     * Starts the hook system, finds all the methods and
     * adds them using wordpress' hook system
     *
     * @author Tim Perry
     *
     */
    public function hookUp()
    {

        $class = new \ReflectionClass($this);
        $methods = $class->getMethods();

        foreach ($methods as $method) {

            if ($method->isPublic()
                && ($attributes = self::getMethodAttributes($method))
                && ($methodName = $method->getName())
            ) {

                if ($hook_name = self::getHookName($methodName, $attributes)) {
                    $this->registerHook($hook_name, $methodName, $attributes);
                }

            }

        }

    }

    /**
     *
     * @type action
     *
     * @param $method
     * @return array|bool
     * @author Tim Perry
     *
     */
    protected function getMethodAttributes($method)
    {
        // extract all the doc comment block attributes
        if (preg_match_all('/@(\w+) (.+)\r?\n/m', $method->getDocComment(), $docBlock)) {

            if (count($docBlock) >= 2) {

                // combine into key =>value array
                $attributes = array_combine($docBlock[1], $docBlock[2]);

                if (isset($attributes['type'])) {
                    return array_merge($this->default_attributes, $attributes);
                }

            }

        }

        return false;

    }


    /**
     *
     * Returns the hookname for a given $methodName and its attributes
     *
     * @param $methodName
     * @param $attributes
     * @return bool
     * @author Tim Perry
     */
    protected function getHookName($methodName, $attributes)
    {
        // allow the hook name to be overridden
        if (isset($attributes['hook_name'])) {
            return $attributes['hook_name'];
        }

        // convert camelcase to underscores
        preg_match_all(
            '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!',
            $methodName,
            $hook_name
        );

        if (isset($hook_name[0][0])) {
            return strtolower(implode("_", $hook_name[0]));
        }

        return false;

    }

    /**
     *
     * Used to register a hook of a given type
     *
     * @param $hook_name
     * @param $method_name
     * @param $attributes
     *
     * @author Tim Perry
     */
    protected function registerHook($hook_name, $method_name, $attributes)
    {
        switch ($attributes['type']) {

            default:
            case "action":
                add_action($hook_name, array($this, $method_name), $attributes['priority'], $attributes["params"]);
                break;

            case "filter":
                add_filter($hook_name, array($this, $method_name), $attributes['priority'], $attributes["params"]);
                break;

        }

    }
} 