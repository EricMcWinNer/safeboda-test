<?php

namespace App\Http\Controllers;

use App\Exceptions\GeoCodingException;
use App\Models\Event;
use App\Utility\Coordinate;
use App\Utility\GeoCodingManager;
use App\Validators\EventControllerValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class EventController extends BaseController
{
    use EventControllerValidator;

    public function create(Request $request)
    {
        $this->validateCreationInput($request);
        $event = new Event();
        $event->name = $request->input('name');
        $event->description = $request->input('description');
        if ($request->input('latitude')) {
            $event->latitude = $request->input('latitude');
            $event->longitude = $request->input('longitude');
            try {
                $coordinate = new Coordinate($event->latitude, $event->longitude);
                $coordinate->location = GeoCodingManager::getAddressFromCoordinates($coordinate);
            } catch (GeoCodingException $e) {
                $event->location = "";
            }
        } else {
            $event->location = $request->input('location');
            $coordinate = GeoCodingManager::getCoordinatesFromAddress($event->location);
            $event->latitude = $coordinate->latitude;
            $event->longitude = $coordinate->longitude;
        }
        $event->event_starts_at = Carbon::parse($request->input('event_starts_at'));
        $event->event_ends_at = Carbon::parse($request->input('event_ends_at'));
        $event->save();
        return response()->json(
            [
                'message' => 'Event created successfully',
                'status' => 'success',
                'data' => $event,
            ],
            201
        );
    }

    public function read($id)
    {
        try {
            $event = Event::findOrFail($id);
            return response(
                [
                    'data' => $event,
                    'status' => 'success'
                ],
                200
            );
        } catch (ModelNotFoundException $e) {
            return response(
                [
                    'message' => 'No event exists with that id on our system',
                    'status' => 'failed'
                ],
                404
            );
        }
    }

    public function readWithPromoCodes($id)
    {
        try {
            $event = Event::with('promoCodes')->findOrFail($id);
            return response(
                [
                    'data' => $event,
                    'status' => 'success'
                ],
                200
            );
        } catch (ModelNotFoundException $e) {
            return response(
                [
                    'message' => 'No event exists with that id on our system',
                    'status' => 'failed'
                ],
                404
            );
        }
    }

    public function readAll()
    {
        $events = Event::with('promoCodes')->get();
        return response(
            [
                'data' => $events,
                'status' => 'success'
            ],
            200
        );
    }

    public function updateEvent(Request $request, $id)
    {
        try {
            $this->validateUpdateInput($request);
            $event = Event::findOrFail($id);
            $eventStartsAt = $request->input('event_starts_at');
            $eventEndsAt = $request->input('event_ends_at');
            $location = $request->input('location');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $this->updateValue($event, 'name', $request->input('name'));
            $this->updateValue($event, 'description', $request->input('description'));
            $this->updateValue($event, 'latitude', $request->input('latitude'));
            $this->updateValue($event, 'longitude', $request->input('longitude'));
            $this->updateValue($event, 'location', $request->input('location'));
            if ($latitude) {
                try {
                    $coordinate = new Coordinate($latitude, $longitude);
                    $location = GeoCodingManager::getAddressFromCoordinates($coordinate);
                } catch (GeoCodingException $e) {
                    $event->location = "";
                }
            }
            if ($location) {
                $coordinate = GeoCodingManager::getCoordinatesFromAddress($location);
                $event->longitude = $coordinate->longitude;
                $event->latitude = $coordinate->latitude;
            }
            if ($eventStartsAt) {
                $event->event_starts_at = Carbon::parse($eventStartsAt);
            }
            if ($eventEndsAt) {
                $event->event_ends_at = Carbon::parse($eventEndsAt);
            }

            $event->save();
            return response()->json(
                [
                    'message' => 'Event updated successfully',
                    'status' => 'success',
                    'data' => $event,
                ],
                201
            );
        } catch (ModelNotFoundException $e) {
            return response(
                [
                    'message' => 'No event exists with that id on our system',
                    'status' => 'failed'
                ],
                404
            );
        }
    }

    private function updateValue(Event $event, $key, $value): void
    {
        if ($value) {
            $event->{$key} = $value;
        }
    }

    public function deleteEvent($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->promoCodes()->delete();
            $event->delete();
            return response(
                [
                    'message' => 'Event deleted successfully',
                    'status' => 'success',
                ],
                200
            );
        } catch (ModelNotFoundException $e) {
            return response(
                [
                    'message' => 'No event exists with that id on our system',
                    'status' => 'failed'
                ],
                404
            );
        }
    }

}
