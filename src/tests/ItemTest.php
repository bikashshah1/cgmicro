<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Item;
use Database\Factories\ItemFactory;

use App\Events\ItemCreatedEvent;
use App\Events\ItemDeletedEvent;
use App\Events\ItemUpdatedEvent;
// use Faker\Factory as Faker;

class ItemViewTest extends TestCase
{
	
	 use DatabaseMigrations;
	/**
     * A basic test example.
     *
     * @return void
     */
    public function testItem()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

 	/**
     * This test will make a GET call on /items route. This should get all items, paginated.
     * Assert that the records in the database is returned with the 'total' returned matching the number of rows in the table.
     *
     * @return void
     */
    public function testGetAllItems()
    {
        
        // Using factory to create dummy items and put them in the database
        $item = Item::factory()->count(10)->create();

        // Check if the route returns a JSON with pagination of 10 items
    	$this->json('GET','/items')->assertResponseOk();
    	$response = json_decode($this->response->getContent());
        $this->assertEquals($response->total, 10);

    }


    /**
     * This test will make a GET call a particular item. This should return the exact item that was requested.
     * Assert that the records in the database is returned with the 'total' returned matching the number of rows in the table.
     *
     * @return void
     */
    public function testGetItemById()
    {
        
        // Using factory to create a dummy item and put it in the database
        $item = Item::factory()->create();

        // Test GET websevice checking assestions valid response and expected item back.
    	$this->json('GET','/item/'.$item->id)->seeJsonContains(['email' => $item->email])->seeJsonContains(['name' => $item->name]);
    	

    }


   /**
     * This test will check addition of new item. Once added, the item is queried back using GET call and matched with the item that was posted. This will also test if the Event is dispatched with the appropriate item or not. 
     *
     * @return void
     */
    public function testPostItem()
    {

    	Event::fake();

    	$item = Item::factory()->make(); //Use factory to fake create an item (it is not in db yet)


    	$data = ['name'=>$item->name, 'email'=>$item->email]; 


    	// Test POST webservice and check if response okay. Check it against the reply to GET webservice
        $this->json('POST','/item', $data)->seeStatusCode(201);
        $this->json('GET','/items')->seeJsonContains(['email' => $data['email']])->seeJsonContains(['name' => $data['name']]);

    	//Test if event was fired
        Event::assertDispatched(function (ItemCreatedEvent $event) use ($item) {
            return $event->item->email === $item->email;
        });
       
    }

    
    /**
     * This test will check update (both put and patch) of existing item. Once updated, the item is queried back using GET call and matched with the item that was posted. This will also test if the Event is dispatched with the appropriate item or not. 
     *
     * @return void
     */
    public function testUpdateItem()
    {
        
        Event::fake();
    	$item = Item::factory()->create(); //User factory to add an item to the database

    	$original = [
                'name' => $item->name,
                'email' => $item->email
        ];

        $modified = [
                'name' => $item->name . '_modified',
                'email' => 'modified_' . $item->email 
        ];

        // Test PUT to check it is modifying the data and checking for valid response. Check again via GET webservice if the update persisted.
        $content = $this->json('PUT','/item/' . $item->id, $modified)->seeStatusCode(200);
       
        $this->json('GET','/items')->seeJsonContains(['email' => $modified['email']])->seeJsonContains(['name' => $modified['name']]);

        // Assert event fired
        Event::assertDispatched(function (ItemUpdatedEvent $event) use ($modified) {
            return $event->item->email === $modified['email'];
        });


        // Test PATCH and check if only the modified field is updated.
        $modified2 = [
                'name' => $item->name . '_modified2'
        ];

        
        $content = $this->json('PATCH','/item/' . $item->id, $modified2)->seeStatusCode(200);
       
        $this->json('GET','/items')->seeJsonContains(['email' => $modified['email']])->seeJsonContains(['name' => $modified2['name']]);
        

        // Assert event fired
        Event::assertDispatched(function (ItemUpdatedEvent $event) use ($modified2) {
        	
            return $event->item->name === $modified2['name'];
        });
     
    }

    /**
     * This test will check delete an existing item. This will also test if the Event is dispatched with the appropriate item or not. 
     *
     * @return void
     */
    public function testDeleteItem()
    {
        
        Event::fake();
        $item = Item::factory()->create(); //This data will now be in the database

        $this->json('GET','/item/'.$item->id)->seeJsonContains(['email' => $item->email])->seeJsonContains(['name' => $item->name]);

        $this->json('delete','/item/'.$item->id)->seeStatusCode(204);
       
        $this->json('GET','/item/'.$item->id)->seeStatusCode(404);  //TODO: this should give nonfound
    

        Event::assertDispatched(ItemDeletedEvent::class);
        
            
    }

}