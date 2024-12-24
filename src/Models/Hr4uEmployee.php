<?php

//namespace Reddot\Base\Models;
namespace ProcessMaker\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ProcessMaker\Models\User;

class Hr4uEmployee extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Get the company name based on the parameter passed.
     * If 'short' or nothing is passed, it extracts the company name from the email.
     * If 'full' is passed, it returns the 'company' column value from the Hr4uEmployee model.
     *
     * @param string $type 'short' or 'full'
     * @return string
     */
    public function company(string $type = 'short'): string
    {
        if ($type === 'full') {
            return $this->company;
        }

        $email = $this->email;
        $domain = substr(strrchr($email, "@"), 1);
        $company = strstr($domain, '.', true);
        return $company;
    }

    /**
     * Generate the vendor code based on the company and employee ID.
     *
     * @return string
     */
    public function vendorCode(): string
    {
        $companyName = $this->company();
        $employeeId = $this->id;

        $companyCodes = config('company.vendor_code');
        $companyCode = $companyCodes[strtolower($companyName)] ?? '00'; // Default to '00' if no match

        // Get the last 4 digits of the employee ID
        $lastFourDigits = substr($employeeId, -4);

        // Concatenate the company code with the last 4 digits of the employee ID
        return $companyCode . $lastFourDigits;
    }
}
