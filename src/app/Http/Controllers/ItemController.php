<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Events\ItemCreatedEvent;
use App\Events\ItemDeletedEvent;
use App\Events\ItemUpdatedEvent;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    /**
     * Return the JSON of all items, in pages of size 10
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function index()
    {
        Log::info("Servicing GET request on /items");
        return Item::paginate(10);
    }

    /**
     * Show the detail of an item given by its id and return the item if successful.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info("Serving GET request on /item/".$id);
        return Item::findOrFail($id);
    }


    /**
     * Store the item posted upon validation and return the item if successful. Also dispatch the event item.created
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        Log::info("Serving POST request on /item/");

        Log::info("Run a simple validation of the fields - can be done using dedicated validator class as well");
     
        $this->validate($request, [
            'name'   =>  'max:255|min:1',
            'email'  =>  'max:255|email',
        ],[
            'name.required' => 'Name field is required',
            'email.required' => 'Email field is required',            
        ]
        );


        $item = Item::create([
            'name' => $request->input('name'),
            'email' => $request->input('email')
            ]
        );

        Log::info("Dispatch item.created event");
        event(new ItemCreatedEvent($item));        


        Log::info("Redirect to /item/id route for show individual item");
        return response()->json($item, 201, [
            'Location' => route('item.show', ['id' => $item->id])
        ]);

       
    }

    /**
     * Update an item based on it's id and return the item if successful. Also, dispatch the event item.updated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function replace(Request $request, $id)
    {

        Log::info("Serving PUT request for item ". $id);
        try {
            $item  = Item::findOrFail($id);
        }
        catch(ModelNotFoundException $e){

            Log::error("Item Not Found for item id ". $id);
            return response()->json([
                'error' => ['message'=>"item not found"]
            ], 404);
        }

        
        Log::error("Run a simple validation of the fields - can be done using dedicated validator class as well");
        $this->validate($request, [
            'name'   =>  'max:255|min:1',
            'email'  =>  'max:255|email',
        ],[
            'name.required' => 'Name field is required',
            'email.required' => 'Email field is required',            
        ]
        );

        $item->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email')
            ]);
        $item->save();

        Log::info("Dispatch item.updated event");
        event(new ItemUpdatedEvent($item));
        return $item;

    }

    /**
     * Update an item based on it's id and return the item if successful. Also, dispatch the event item.updated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Log::info("Serving PATCH request for item ". $id);
        try {
            $item  = Item::findOrFail($id);
        }
        catch(ModelNotFoundException $e){
            Log::error("Item Not Found for item id ". $id);
            return response()->json([
                'error' => ['message'=>"item not found"]
            ], 404);
        }

        Log::error("Run a simple validation of the fields - can be done using dedicated validator class as well");
        $this->validate($request, [
            'name'   =>  'max:255|min:1',
            'email'  =>  'max:255|email',
        ]
        );

        if(!empty($request['name'])){
            $item->name = $request['name'];
        }
        if(!empty($request['email'])){
            $item->name = $request['email'];
        }

        $item->save();

        Log::info("Dispatch item.updated event");
        event(new ItemUpdatedEvent($item));
        return $item;

    }


    /**
     * Delete an item given by its id and return the 204 if successful. Broadcast 'item.deleted'
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  id  $id
     * @return http 204 code
     */
    public function destroy($id)
    {

        Log::info("Serving DELETE request for item ". $id);
        try {
            $item  = Item::findOrFail($id);
            $item->delete();
        }
        catch(ModelNotFoundException $e){
            Log::error("Item Not Found for item id ". $id);
            return response()->json(['error' => 'No query results for model'], 404);
        }

        Log::info("Dispatch item.updated event");
        event(new ItemDeletedEvent()); 
        return response(null, 204);
        
    }
}




