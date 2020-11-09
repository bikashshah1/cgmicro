<?php

namespace App\Events;

class ItemCreatedEvent extends ItemEvent
{
    /**
     * The event's broadcast name.
     * @return string
     */
    public function broadcastAs()
    {
        return 'item.created';
    }
}
