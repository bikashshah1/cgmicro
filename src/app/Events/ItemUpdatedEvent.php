<?php

namespace App\Events;

class ItemUpdatedEvent extends ItemEvent
{
    /**
     * The event's broadcast name.
     * @return string
     */
    public function broadcastAs()
    {
        return 'item.updated';
    }
}