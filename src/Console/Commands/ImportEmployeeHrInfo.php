<?php

namespace Reddot\Employee\Management\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
//use Reddot\Base\Models\Hr4uEmployee;
use ProcessMaker\Models\Hr4uEmployee;

class ImportEmployeeHrInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:hrdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import HR data from a txt file and insert them into hr4u_employees table';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filePath = storage_path("hrdata/eApproval.txt");
        $file = fopen($filePath, 'r');

        if (!$file) {
            echo "Error while opening the file.\n";
            Log::channel('hr4u')->error("Error while openning the file from $filePath");
            return;
        }

        //clean the old table
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Hr4uEmployee::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        echo "=========== CLEANED OLD HR DATA ===============\n";
        echo "=========== STARTED IMPORTING HR DATA ===============\n";
        Log::channel('hr4u')->info("=========== STARTED IMPORTING HR DATA ===============");

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
                Log::channel('hr4u')->error(
                    "Insertion failed! Invalid ID: $employeeId Or Email: $employeeEmail"
                );
                continue;
            }

            try {
                $rowArray = $this->convertEmptyStringsToNull($rowArray);

                Hr4uEmployee::updateOrCreate(
                    [
                        'email' => $employeeEmail,
                    ],
                    [
                        "email" => $employeeEmail,
                        "id" => $employeeId,
                        "name" => $rowArray[1],
                        "division" => $rowArray[2],
                        "department" => $rowArray[3],
                        "unit" => $rowArray[4],
                        "unit_id" => $rowArray[5],
                        "position_id" => $rowArray[6],
                        // "designation" => $rowArray[7], // for data sensitive issue real designation is omitted
                        "designation" => $rowArray[14],
                        "mobile" => $rowArray[8],
                        "hierarchy_manager_id" => $rowArray[10],
                        "hierarchy_manager_name" => $rowArray[11],
                        "hierarchy_manager_email" => $rowArray[12],
                        "hierarchy_manager_position_id" => $rowArray[13],
                        "position_text" => $rowArray[14],
                        "division_id" => $rowArray[15],
                        "department_id" => $rowArray[16],
                        "cost_center_id" => $rowArray[17],
                        "cost_center_description" => $rowArray[18],
                        "employee_band" => $rowArray[19],
                        "second_level_supervisor_id" => $rowArray[20],
                        "third_level_supervisor_id" => $rowArray[21],
                        "gender" => $rowArray[22],
                        "office_location" => $rowArray[23],
                        "company" => $rowArray[24],
                        "joining_date" => $rowArray[25],
                    ]
                );
            } catch (\Exception $e) {
                echo "Insert failed! ID: $employeeId  Email: $employeeEmail";
                Log::channel('hr4u')->error(
                    "Insert failed! ID: $employeeId  Email: $employeeEmail\n
                    Error Details: " . $e->getMessage()
                );
            }
        }
        echo "=========== HR DATA IS IMPORTED. ===============\n";
        Log::channel('hr4u')->info("=========== HR DATA IS IMPORTED ===============");
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


