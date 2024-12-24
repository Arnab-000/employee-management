<?php

namespace Reddot\Employee\Management\Http\Controllers;

use ProcessMaker\Http\Controllers\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        return view('employee-management::welcome');
    }
}
