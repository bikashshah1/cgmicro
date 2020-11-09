<?php

namespace App\Events;

class ItemDeletedEvent extends ItemEvent
{
    /**
     * The event's broadcast name.
     * @return string
     */
    public function broadcastAs()
    {
        return 'item.deleted';
    }
}
