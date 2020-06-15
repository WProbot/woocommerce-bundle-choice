<?php
/**
 * Models a group of WordPress filters to be added or removed together.
 *
 * @package tad\WPBrowser\Module\WPLoader
 */

namespace tad\WPBrowser\Module\WPLoader;

/**
 * Class FiltersGroup
 *
 * @package tad\WPBrowser\Module\WPLoader
 */
class FiltersGroup
{
    /**
     * @var array
     */
    protected $filters;
    /**
     * @var callable|string
     */
    protected $removeCallback;
    /**
     * @var callable|string
     */
    protected $addCallback;

    /**
     * FiltersGroup constructor.
     *
     * @param array<string> $filters    The list of filters to remove.
     * @param callable|null $removeWith The callable that should be used to remove the filters or `null` to use the
     *                                  default one.
     * @param callable|null $addWith    The callable that should be used to add the filters, or `null` to use the
     *                                  default one.
     */
    public function __construct(array $filters = [], callable $removeWith = null, callable $addWith = null)
    {
        $this->filters        = $filters;
        $this->removeCallback = null === $removeWith ? 'remove_filter' : $removeWith;
        $this->addCallback    = null === $addWith ? 'add_filter' : $addWith;
    }

    /**
     * Removes the filters of the group.
     *
     * @return void
     */
    public function remove()
    {
        foreach ($this->filters as $filter) {
            $filterWithoutAcceptedArguments = array_slice($filter, 0, 3);
            call_user_func_array($this->removeCallback, $filterWithoutAcceptedArguments);
        }
    }

    /**
     * Adds the filters of the group.
     *
     * @return void
     */
    public function add()
    {
        foreach ($this->filters as $filter) {
            call_user_func_array($this->addCallback, $filter);
        }
    }
}
