<?php
/**
 * ActionPool
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

use Netsteps\Marketplace\Exception\FeedActionException;
use Netsteps\Marketplace\Model\Feed\ActionInterface as Action;

/**
 * Class ActionPool
 * @package Netsteps\Marketplace\Model\Feed
 */
class ActionPool implements ActionPoolInterface
{
    /**
     * @var Action[]
     */
    private array $_actions;

    /**
     * @param Action[] $actions
     */
    public function __construct(array $actions = [])
    {
        $this->_actions = $actions;
    }

    /**
     * @inheritDoc
     */
    public function get(string $code): ?\Netsteps\Marketplace\Model\Feed\ActionInterface
    {
        if(!array_key_exists($code, $this->_actions)){
            return null;
        }

        return $this->_actions[$code];
    }

    /**
     * @inheritDoc
     * @throws FeedActionException
     */
    public function getOrException(string $code): \Netsteps\Marketplace\Model\Feed\ActionInterface
    {
        $action = $this->get($code);

        if (!$action instanceof Action) {
            throw new FeedActionException(
                __(
                    'Cannot fetch action class for "%action". Expecting object tha implements action %expected but %fetched found.',
                    [
                        'action' => $code,
                        'expected' => Action::class,
                        'fetched' => is_null($action) ? 'null' : get_class($action)
                    ]
                )
            );
        }

        return $action;
    }

    /**
     * @inheritDoc
     */
    public function getActions(): array
    {
        return $this->_actions;
    }
}
