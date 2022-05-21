<?php

namespace App\Http\Services\Explorer;

use App\Enum\ExplorerRegistrationStatusEnum;
use App\Enum\ExplorerRegistrationTypeEnum;
use App\Http\Helpers\UserHelper;
use App\Models\ExplorerRegistration;
use App\User;
use Exception;
use phpDocumentor\Reflection\Element;

class ExplorerRegistrationService
{
    /**
     * @throws Exception
     */
    public function register(User $user, $params): ExplorerRegistration
    {
        $registration = ExplorerRegistration::where('user_id', '=', $user->id)->first();

        if ($registration == null) {
            $registration = new ExplorerRegistration();
            $registration->user_id = $user->id;
            $registration->status = ExplorerRegistrationStatusEnum::WAITING;

            if (!UserHelper::isFreelance($user)) {
                $registration->registration_type = ExplorerRegistrationTypeEnum::CLIENT;
                $registration->budget = $params['budget'];
                $registration->project_description = $params['project_description'];
            } else {
                $registration->registration_type = ExplorerRegistrationTypeEnum::FREELANCE;
            }

            try {
                $registration->save();

                $user->explorer_access_code = $this->generateCode();
                $user->save();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            throw new Exception("User Already Registered");
        }


        return $registration;
    }

    public function getUserRegistrationCode(User $user)
    {
        return $user->explorer_access_code;
    }

    public function markUserAsRegistered(User $user)
    {
        $user->is_explorer = true;
        $user->save();
    }


    /**
     * @throws Exception
     */
    public function unlockExplorer(User $user, $accessCode)
    {
        $registration = ExplorerRegistration::where('user_id', '=', $user->id)->first();

        if ($registration->status == ExplorerRegistrationStatusEnum::ACCEPTED) {
            if ($accessCode == $user->explorer_access_code) {
                $user->is_explorer = true;
                $user->save();
            } else {
                throw new Exception("Explorer access code not valid");
            }

        } else {
            throw new Exception("Explorer Registration not validated");
        }
    }


    public function grantExplorerAccess(User $user)
    {
        $registration = ExplorerRegistration::where('user_id', '=', $user->id)->first();
        $registration->status = ExplorerRegistrationStatusEnum::ACCEPTED;
        $registration->save();
    }

    public function refuseExplorerAccess(User $user)
    {
        $registration = ExplorerRegistration::where('user_id', '=', $user->id)->first();
        $registration->status = ExplorerRegistrationStatusEnum::REFUSED;
        $registration->save();
    }

    public function getWaitingFreelanceRegistration()
    {
        return ExplorerRegistration::where('status', '=', ExplorerRegistrationStatusEnum::WAITING)
            ->where('registration_type', '=', ExplorerRegistrationTypeEnum::FREELANCE)
            ->get();
    }

    public function getClientWaitingRegistration()
    {
        return ExplorerRegistration::where('status', '=', ExplorerRegistrationStatusEnum::WAITING)
            ->where('registration_type', '=', ExplorerRegistrationTypeEnum::CLIENT)
            ->get();
    }

    private function generateCode()
    {
        return substr(str_shuffle(
            'abcdefghijklmnopqrstuvwxyzABCEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 1, 10);
    }
}
