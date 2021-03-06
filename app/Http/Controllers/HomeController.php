<?php

namespace App\Http\Controllers;

use App\Models\AttendEvent;
use App\Models\Event;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $eventsCreatedByYou = Auth::user()
                                ->usertypes()
                                ->where('role_id', 2)->first()
                                ->eventscreated()
                                ->where('status', 0)
                                ->get();

        return view('home', [
            'eventsCreated' => $eventsCreatedByYou,
        ]);
    }

    /**
     * Block comment
     *
     * @return View
     */
    public function activeEvents()
    {
        $events = Auth::user()
                        ->usertypes()
                        ->where('role_id',2)->first()
                        ->eventscreated()
                        ->where('status',1)
                        ->get();

        return view('Events.active', [
            'events' => $events,
        ]);
    }

    public function dashboard(Event $event)
    {
        $relationship = $event->usertypes()->get();
        $attendees =[];
        $i=0;
        foreach ($relationship as $key) {
            $attendees[$i] = $key->users()->first();
            $i++;
        }
        return view('Events.dashboard', compact('event','attendees'));
    }

    /**
     * Block comment
     *
     * @return View
     */
    public function myevents()
    {
        $events = Auth::user()
                        ->usertypes()
                        ->where('role_id', 1)->first()
                        ->events()
                        ->get();

        return view('Events.my-events',[
            'events' => $events,
        ]);
    }
}
