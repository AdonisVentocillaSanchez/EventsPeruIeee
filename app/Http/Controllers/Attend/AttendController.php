<?php

namespace App\Http\Controllers\Attend;

use App\Models\AttendEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Braintree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event)
    {
        
        if ($event->registrationPayments()->first()->paymentways()->where('type_id', 1)->first()) {
            $this->store(new Request([
                'event' => $event
                ]));
        } else {
            $gateway = new Braintree\Gateway([
                'environment' => env('BRAINTREE_ENV'),
                'merchantId' => env('BRAINTREE_MERCHANT_ID'),
                'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
                'privateKey' => env('BRAINTREE_PRIVATE_KEY')
            ]);

            $token = $gateway->ClientToken()->generate();

            if (Auth::user()->usertypes()->where('role_id',2)->first()) {
                $string = "Miembro del IEEE";
                $amount = $event->registrationPayments()->first()->paymentways()->where('type_id',3)->first()->price;
            } else {
                $string = "Público general";
                $amount = $event->registrationPayments()->first()->paymentways()->where('type_id',2)->first()->price;
            }
            

            return view('attend.payment.create', [
                'amount' => $amount,
                'string' => $string,
                'eventId' => $event->id,
                'token' => $token,
                ]);
        }
        session()->flash('status', 'Algo falló');
        return redirect()->route('index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->userId . "   asfasd  " .  $request->eventId);
        // $userRole = User::find($request->userId)->roles()->where('role.id', '1')->first();
        $userRole = Auth::user()
                                ->usertypes()
                                ->where('role_id', '1')
                                ->first();

        DB::beginTransaction();
        try {
             $userRole->events()
                                ->attach([
                                    $request->event->id => ['paymentway_id' => $userRole->role_id]
                                ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('status', '¡Ocurrió un error!');
            redirect()->back();
        }
        DB::commit();


        // dd($userRole->events()->get()->first());
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     * Param La id del evento que queremos mostrar
     * @return \Illuminate\Http\Response
     */
    public function show($attendevent)
    {
        session(['event_id' => $attendevent]);
        $attendees = Event::find($attendevent)->usertypes()->get();
        $persons= [];
        $i = 0;
        foreach ($attendees as $attendant) {
            $persons[$i] = $attendant->users()->first()->person()->first();
            $i++;
        }
        return view('attend.show', [
            'attendees' => $persons,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\attendevent  $attendevent
     * @return \Illuminate\Http\Response
     */
    public function edit(attendevent $attendevent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\attendevent  $attendevent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, attendevent $attendevent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\attendevent  $attendevent
     * @return \Illuminate\Http\Response
     */
    public function destroy(attendevent $attendevent)
    {
        //
    }
}
