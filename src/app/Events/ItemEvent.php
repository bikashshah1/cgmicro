<?php


namespace App\Events;


use App\Models\Item;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ItemEvent implements ShouldBroadcast
{
    use SerializesModels;

    /** @var Item */
    public $item;

    /**
     * ItemCreatedEvent constructor.
     * @param Item $item Item.
     */
    public function __construct(Item $item=null)
    {
        $this->item = $item;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|Channel[]
     */
    public function broadcastOn()
    {
        return new Channel('itemsChannel');
    }
}
