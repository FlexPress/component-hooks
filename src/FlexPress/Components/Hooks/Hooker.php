<?php

namespace FlexPress\Components\Hooks;

class Hooker
{

    /**
     * @var \SplObjectStorage
     */
    protected $hookables;

    public function __construct(\SplObjectStorage $hookables, array $hookableArray)
    {
        $this->hookables = $hookables;

        if (!empty($hookableArray)) {

            foreach ($hookableArray as $hookable) {

                if (!method_exists($hookable, "hookUp")) {

                    $message = "One or more of the hookables you have passed to ";
                    $message .= get_class($this);
                    $message .= " does not use the HookableTrait";

                    throw new \RuntimeException($message);

                }

                $this->hookables->attach($hookable);

            }
        }
    }

    /**
     * Registers all the field groups added
     * @author Tim Perry
     */
    public function hookUp()
    {

        $this->hookables->rewind();
        while ($this->hookables->valid()) {

            $hookable = $this->hookables->current();
            $hookable->hookUp();

            $this->hookables->next();

        }

    }
}