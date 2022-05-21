<?php

namespace App\Http\Helpers;

use App\Models\Appointment;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Http\Helpers\NotificationHelper;

/**
 * Class AppointmentHelper
 * @package App\Http\Helpers
 */
class AppointmentHelper
{

    /**
     * Get all appointments
     * @param $request
     * @return Appointment[]|Collection [Object]  Appointment
     */
    public function all($request = [])
    {
        $appointments = Appointment::query()->select(['*'])->get();

        return $appointments;
    }

    /**
     * Get appointment
     * @param  [Integer] $id Appointment id
     * @return Appointment|Builder|Model|object|null [Object]  Appointment
     */
    public function get($id)
    {
        return Appointment::where('id', $id)->first();
    }

    /**
     * Get appointment locations
     * @param  [Integer] $id Appointment id
     * @return Appointment|Builder|Model|object|null [Object]  Appointment
     */
    public function getLocation()
    {
        $locations = Appointment::whereNotNull('location')->groupBy('location')->pluck('location')->toArray();

        $output = [];
        foreach($locations as $location){
        	$loc = new \StdClass;
        	$loc->name = $location;
        	$output[] = $loc;
        }

        return $output;
    }

    /**
     * Add or update a appointment
     * @param
     * @return mixed [Object]  Appointment
     */
    public function addOrUpdate($params)
    {
        $this->helpers['notification'] = new NotificationHelper;

        $appointmentParams = $params['appointment'];
        $mandatoryParameters = ['firstname', 'date'];

        // Check if all parameters are provided
        try {
            _helper('api')->checkParameters($appointmentParams, $mandatoryParameters);
        } catch (Exception $e) {
            // Return the exception message if error
            _helper('api')->error($e->getMessage());
        }

        if (array_key_exists('id', $appointmentParams) && $appointmentParams['id']) {
            $action = 'UPDATE';
            $appointment = Appointment::find($appointmentParams['id']);
            $appointment->updated_by = (int)$params['user'][0];
        } else {
        		$action = 'ADD';
            $appointment = new Appointment();
            $appointment->created_by = (int)$params['user'][0];
            $appointment->updated_by = (int)$params['user'][0];
        }

        $appointment->firstname = $appointmentParams['firstname'];
        $appointment->job = $appointmentParams['job'];
        $appointment->phone = $appointmentParams['phone'];
        $appointment->email = $appointmentParams['email'];
        $appointment->note = $appointmentParams['note'];

        $appointment->datetime = Carbon::parse($appointmentParams['date'])->format('Y-m-d');
        $datetime = $appointment->datetime . ' ' . $appointmentParams['hours']. ':' . $appointmentParams['minutes'];
        $appointment->datetime = Carbon::parse($datetime)->format('Y-m-d H:i:s');

        $appointment->room_id = $appointmentParams['room_id'];
        $appointment->location = $appointmentParams['location'];
        

        try {
            $appointment->save();

            $time = Carbon::parse($appointmentParams['date'])->format('d/m/Y') .' à '. $appointmentParams['hours']. 'h' . $appointmentParams['minutes'];
            $icon = $action == 'ADD' ? '🆕' : '✅';
            $action = $action == 'ADD' ? 'créé' : 'modifié';
            $this->helpers['notification']->save("$icon Le rendez-vous (avec ".$appointment->firstname.") a bien été planifié pour le ". $time ." ", $params['user'][0], 'RDV', $appointment->id);
        } catch (Exception $e) {
            _helper('api')->error($e->getMessage());
        }

        return $appointment;
    }

    /**
     * Delete One appointment
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $appointment = Appointment::find($id);
        try {
            $appointment->delete();
        } catch (Exception $e) {
            _helper('api')->error($e->getMessage());
        }

        return true;
    }

    // -- USEFUL
}
