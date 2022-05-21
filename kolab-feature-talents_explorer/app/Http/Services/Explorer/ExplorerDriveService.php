<?php

namespace App\Http\Services\Explorer;

use App\Models\ExplorerDrive;
use App\Models\ExplorerDriveLink;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ExplorerDriveService
{
    /**
     * @param User $user
     * @param $driveId
     * @return ExplorerDrive|ExplorerDrive[]|Collection|Model|null
     * @throws Exception
     */
    public function getDrive(User $user, $driveId)
    {
        $drive = ExplorerDrive::find($driveId);

        if ($this->checkUserDriveAccess($drive, $user)) {
            return $drive;
        } else {
            throw new Exception("User Doesn't have access to the drive");
        }

    }

    public function addLink(ExplorerDrive $drive, $linkUrl): ExplorerDriveLink
    {
        $link = new ExplorerDriveLink();

        $link->id_drive = $drive->id;
        $link->link = $linkUrl;

        $link->save();

        return $link;
    }

    /**
     * @param ExplorerDrive $drive
     * @param User $user
     * @return bool
     */
    private function checkUserDriveAccess(ExplorerDrive $drive, User $user): bool
    {
        return $drive->missionProposition()->get('freelance_id') == $user->id || $drive->missionProposition()->get('client_id') == $user->id;
    }
}
