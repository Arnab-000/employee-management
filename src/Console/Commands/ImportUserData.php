<?php

namespace Reddot\Employee\Management\Console\Commands;

use ProcessMaker\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use ProcessMaker\Models\GroupMember;

class ImportUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:userdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates users table from hrdata/eApproval.txt password = admin123 . Updates the user if exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = storage_path("hrdata/eApproval.txt");
        $file = fopen($filePath, 'r');

        if (!$file) {
            echo "Error while opening the file.\n";
            Log::channel('importuser')->error("Error while openning the file from $filePath");
            return;
        }

        echo "=========== STARTED IMPORTING USERS DATA ===============\n";
        Log::channel('importUser')->info("=========== STARTED IMPORTING USERS DATA ===============");

        $isHeader = true;
        while ($row = fgets($file)) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            $rowArray = explode("|", $row);
            $employeeId = $rowArray[0] ?? '';
            $employeeEmail = $rowArray[9] ?? '';

            if (! $this->isValidEmployee($employeeId, $employeeEmail)) {
                Log::channel('importUser')->error(
                    "Insert failed ! Invalid ID: $employeeId Or Email: $employeeEmail"
                );
                continue;
            }

            try {
                $rowArray = $this->convertEmptyStringsToNull($rowArray);

                $nameParts = explode(' ', $rowArray[1]);
                $firstName = array_shift($nameParts);
                $lastName = implode(' ', $nameParts);

                $user = User::updateOrCreate(
                    [
                        'email' => $employeeEmail,
                    ],
                    [
                        'username' => $employeeEmail,
                        'email' => $employeeEmail,
                        'firstname' => $firstName,
                        'lastname' => $lastName,
                        'password' => Hash::make('admin123'),
                    ]);

                $groupData = [
                    'group_id' => 1,
                    'member_type' => User::class,
                    'member_id' => $user->id,
                ];

                GroupMember::updateOrCreate($groupData, $groupData);

            } catch (\Exception $e) {
                Log::channel('importUser')->error(
                    "Insert failed! ID: $employeeId  Email: $employeeEmail\n
                    Error Details: " . $e->getMessage()
                );
            }
        }
        echo "=========== USERS ARE IMPORTED ===============\n";
        Log::channel('importUser')->info("=========== USERS ARE IMPORTED ===============");
    }

    private function isValidEmployee($employeeId, $employeeEmail): bool
    {
        return is_numeric($employeeId) && !empty($employeeEmail);
    }

    private function convertEmptyStringsToNull(array $data): array
    {
        return array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $data);
    }
}
